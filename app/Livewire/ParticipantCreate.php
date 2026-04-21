<?php

namespace App\Livewire;

use App\Models\ParticipantModel;
use App\Models\RegionModel;
use App\Services\RegionFuzzyMatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithFileUploads;
use Rap2hpoutre\FastExcel\FastExcel;

class ParticipantCreate extends Component
{
    use WithFileUploads;

    // Form fields
    public string $nama = '';
    public string $nik = '';
    public string $no_hp = '';
    public string $alamat = '';
    public string $provinsi  = '';
    public string $kabupaten = '';
    public string $kecamatan = '';
    public string $kelurahan = '';

    // Region data
    public array $regionMap    = [];
    public array $kabupatenList = [];
    public array $kecamatanList = [];
    public array $kelurahanList = [];

    // UI state
    public bool $showSuccess = false;
    public string $successMessage = '';

    // File upload (manual form)
    public $file;

    // Import state
    public $importFile = null;
    public bool $importing = false;
    public bool $importDone = false;
    public int $importTotal = 0;
    public int $importSuccess = 0;
    public int $importSkipped = 0;
    public array $importLogs = [];

    // ── Fuzzy Correction Review State ───────────────────────────────
    // Apakah sedang menampilkan panel review koreksi
    public bool $showFuzzyReview = false;

    /**
     * Daftar baris yang membutuhkan review koreksi wilayah.
     * Setiap item:
     * [
     *   'row'        => int,           // nomor baris Excel
     *   'rowData'    => array,         // data baris lengkap (sudah divalidasi non-wilayah)
     *   'original'   => array,         // wilayah asli dari Excel
     *   'suggestion' => array,         // saran koreksi dari fuzzy matcher
     *   'scores'     => array,         // skor similarity per field
     *   'changed'    => array,         // field yang berubah
     *   'decision'   => string|null,   // 'approve' | 'reject' | null (belum diputuskan)
     * ]
     */
    public array $fuzzyReviewItems = [];

    /**
     * Baris yang sudah valid (tidak perlu koreksi), disimpan sementara
     * sampai user selesai review fuzzy items.
     * Setiap item: ['row' => int, 'data' => array (siap insert)]
     */
    public array $pendingValidRows = [];

    /**
     * Baris yang gagal validasi non-wilayah (langsung skip, tidak perlu review).
     * Setiap item: ['row' => int, 'errors' => array]
     */
    public array $pendingErrorRows = [];

    public function mount()
    {
        $regions = RegionModel::all();

        $this->regionMap = $regions->groupBy('provinsi')
            ->map(
                fn($kabs) =>
                $kabs->groupBy('kabupaten')
                    ->map(
                        fn($kecs) =>
                        $kecs->groupBy('kecamatan')
                            ->map(fn($items) => $items->pluck('kelurahan')->values()->toArray())
                            ->toArray()
                    )->toArray()
            )->toArray();
    }

    // ── Region Dropdown Handlers ──────────────────────────────────────
    public function updatedProvinsi($value)
    {
        $this->resetRegionFields(['kabupaten', 'kecamatan', 'kelurahan']);
        $this->kabupatenList = isset($this->regionMap[$value])
            ? array_keys($this->regionMap[$value]) : [];
    }

    public function updatedKabupaten($value)
    {
        $this->resetRegionFields(['kecamatan', 'kelurahan']);
        $this->kecamatanList = isset($this->regionMap[$this->provinsi][$value])
            ? array_keys($this->regionMap[$this->provinsi][$value]) : [];
    }

    public function updatedKecamatan($value)
    {
        $this->resetRegionFields(['kelurahan']);
        $this->kelurahanList = isset($this->regionMap[$this->provinsi][$this->kabupaten][$value])
            ? $this->regionMap[$this->provinsi][$this->kabupaten][$value] : [];
    }

    private function resetRegionFields(array $fields): void
    {
        foreach ($fields as $field) {
            $this->$field = '';
            $this->{$field . 'List'} = [];
        }
    }

    // ── Utility Methods ──────────────────────────────────────────────
    private function normalizeNoHp(string $value): string
    {
        $no = preg_replace('/\D/', '', $value);

        if (str_starts_with($no, '0')) {
            $no = '62' . substr($no, 1);
        } elseif (!str_starts_with($no, '62') && $no !== '') {
            $no = '62' . $no;
        }

        return $no;
    }

    private function buildRegionKey(string $provinsi, string $kabupaten, string $kecamatan, string $kelurahan): string
    {
        return strtolower("{$provinsi}|{$kabupaten}|{$kecamatan}|{$kelurahan}");
    }

    private function findRegion(string $provinsi, string $kabupaten, string $kecamatan, string $kelurahan): ?RegionModel
    {
        $key = $this->buildRegionKey($provinsi, $kabupaten, $kecamatan, $kelurahan);
        $regions = RegionModel::all()->keyBy(
            fn($r) =>
            $this->buildRegionKey($r->provinsi, $r->kabupaten, $r->kecamatan, $r->kelurahan)
        );
        return $regions[$key] ?? null;
    }

    // ── Validation Rules (Centralized) ───────────────────────────────
    private function participantRules(bool $isImport = false): array
    {
        $uniqueOption = $isImport ? '' : 'withoutTrashed';

        return [
            'nama' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'regex:/^[a-zA-Z\s\.\']+$/'
            ],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('participants', 'nik')->$uniqueOption()
            ],
            'no_hp' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^(08|628)[0-9]+$/',
                Rule::unique('participants', 'no_hp')->$uniqueOption()
            ],
            'alamat' => ['required', 'string', 'min:5'],
            'provinsi'  => ['required'],
            'kabupaten' => ['required'],
            'kecamatan' => ['required'],
            'kelurahan' => ['required'],
        ];
    }

    private function participantMessages(): array
    {
        return [
            // Nama
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min'      => 'Nama minimal 1 karakter.',
            'nama.max'      => 'Nama maksimal 255 karakter.',
            'nama.regex'    => 'Nama hanya boleh huruf, spasi, titik, dan apostrof.',
            // NIK
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'   => 'NIK sudah terdaftar.',
            // No HP
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.min'      => 'Nomor HP minimal 10 digit.',
            'no_hp.max'      => 'Nomor HP maksimal 15 digit.',
            'no_hp.regex'    => 'Nomor HP harus format Indonesia (08 atau 628).',
            'no_hp.unique'   => 'Nomor HP sudah terdaftar.',
            // Alamat
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min'      => 'Alamat terlalu pendek.',
            // Wilayah
            'provinsi.required'  => 'Provinsi wajib dipilih.',
            'kabupaten.required' => 'Kabupaten wajib dipilih.',
            'kecamatan.required' => 'Kecamatan wajib dipilih.',
            'kelurahan.required' => 'Kelurahan wajib dipilih.',
        ];
    }

    // ── Core Validation Logic (Reusable) ─────────────────────────────
    private function validateParticipantData(array $data, int $rowNumber = null): array
    {
        $errors = [];

        // Normalize & Extract
        $nama      = trim($data['nama'] ?? '');
        $nik       = preg_replace('/\D/', '', $data['nik'] ?? '');
        $no_hp     = $this->normalizeNoHp(trim($data['no_hp'] ?? ''));
        $alamat    = trim($data['alamat'] ?? '');
        $provinsi  = strtolower(trim($data['provinsi'] ?? ''));
        $kabupaten = strtolower(trim($data['kabupaten'] ?? ''));
        $kecamatan = strtolower(trim($data['kecamatan'] ?? ''));
        $kelurahan = strtolower(trim($data['kelurahan'] ?? ''));

        // Validasi Nama
        if (empty($nama)) {
            $errors[] = 'Nama kosong';
        } elseif (strlen($nama) > 255) {
            $errors[] = "Nama terlalu panjang (maks 255 karakter)";
        } elseif (!preg_match("/^[a-zA-Z\s\.\']+$/", $nama)) {
            $errors[] = "Nama mengandung karakter tidak valid";
        }

        // Validasi NIK
        if (strlen($nik) !== 16) {
            $errors[] = "NIK tidak valid (harus 16 digit angka)";
        }

        // Validasi No HP
        if (empty($no_hp)) {
            $errors[] = 'Nomor HP kosong';
        } elseif (strlen($no_hp) < 10) {
            $errors[] = "No HP terlalu pendek (min 10 digit)";
        } elseif (strlen($no_hp) > 15) {
            $errors[] = "No HP terlalu panjang (maks 15 digit)";
        } elseif (!preg_match('/^(08|628)[0-9]+$/', $no_hp)) {
            $errors[] = "No HP bukan format Indonesia (harus diawali 08 atau 628)";
        }

        // Validasi Alamat
        if (empty($alamat)) {
            $errors[] = 'Alamat kosong';
        } elseif (strlen($alamat) < 5) {
            $errors[] = "Alamat terlalu pendek (min 5 karakter)";
        }

        // Validasi Wilayah
        if (empty($provinsi)) $errors[] = 'Kolom provinsi kosong';
        if (empty($kabupaten)) $errors[] = 'Kolom kabupaten kosong';
        if (empty($kecamatan)) $errors[] = 'Kolom kecamatan kosong';
        if (empty($kelurahan)) $errors[] = 'Kolom kelurahan kosong';

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        // Cek Region
        $region = $this->findRegion($provinsi, $kabupaten, $kecamatan, $kelurahan);
        if (!$region) {
            return ['valid' => false, 'errors' => ["Wilayah tidak ditemukan: {$provinsi} / {$kabupaten} / {$kecamatan} / {$kelurahan}"]];
        }

        // Cek Duplikat
        $nikExists = ParticipantModel::withoutTrashed()->where('nik', $nik)->exists();
        if ($nikExists) {
            return ['valid' => false, 'errors' => ["NIK {$nik} sudah terdaftar"]];
        }

        $hpExists = ParticipantModel::withoutTrashed()->where('no_hp', $no_hp)->exists();
        if ($hpExists) {
            return ['valid' => false, 'errors' => ["No HP {$no_hp} sudah terdaftar"]];
        }

        return [
            'valid' => true,
            'data' => [
                'nama' => $nama,
                'nik' => $nik,
                'no_hp' => $no_hp,
                'alamat' => $alamat,
                'region_id' => $region->id,
                'status' => 'pending',
                'created_by' => Auth::id()
            ]
        ];
    }

    // ── Save Method (Manual Form) ────────────────────────────────────
    public function save()
    {
        $key = 'save-participant:' . Auth::id() . '|' . request()->ip();
        if ($this->checkRateLimit($key, 20)) return;

        $this->no_hp = $this->normalizeNoHp($this->no_hp);

        $this->validate($this->participantRules(), $this->participantMessages());

        $region = $this->findRegion($this->provinsi, $this->kabupaten, $this->kecamatan, $this->kelurahan);
        if (!$region) {
            $this->addError('region', 'Wilayah tidak ditemukan.');
            return;
        }

        ParticipantModel::create([
            'nama' => $this->nama,
            'nik' => $this->nik,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
            'region_id' => $region->id,
            'layanan_id' => null,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        $this->resetForm();
        $this->showSuccess = true;
        $this->successMessage = 'Peserta berhasil ditambahkan!';
        $this->dispatch('participantSaved');
    }

    private function resetForm(): void
    {
        $this->reset(['nama', 'nik', 'no_hp', 'alamat', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan']);
        $this->resetRegionFields(['kabupaten', 'kecamatan', 'kelurahan']);
    }

    public function dismissSuccess()
    {
        $this->showSuccess = false;
    }

    // ── Import Excel Methods ─────────────────────────────────────────
    public function updatedImportFile()
    {
        $this->validateOnly('importFile', [
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'importFile.mimes' => 'File harus berformat .xlsx atau .xls.',
            'importFile.max'   => 'Ukuran file maksimal 5MB.',
        ]);
    }

    /**
     * Tahap 1: Parse Excel, pisahkan baris valid / perlu koreksi / error.
     * Jika ada baris yang perlu koreksi → tampilkan review panel.
     * Jika tidak ada → langsung commit.
     */
    public function runImport()
    {
        $key = 'import-participant:' . Auth::id() . '|' . request()->ip();
        if ($this->checkRateLimit($key, 5, 'import_rate_limit')) return;

        $this->validateOnly('importFile', [
            'importFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'importFile.required' => 'Pilih file Excel terlebih dahulu.',
            'importFile.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'importFile.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $this->resetImportState();
        $this->importing = true;

        $fuzzyMatcher      = new RegionFuzzyMatcher();
        $fuzzyReviewItems  = [];
        $pendingValidRows  = [];
        $pendingErrorRows  = [];
        $total             = 0;

        try {
            $rowNumber = 1;
            (new FastExcel)->import($this->importFile->getRealPath(), function ($row) use (
                &$rowNumber, &$total, &$fuzzyReviewItems, &$pendingValidRows, &$pendingErrorRows, $fuzzyMatcher
            ) {
                $row = collect($row)->mapWithKeys(fn($v, $k) => [strtolower(trim($k)) => $v])->toArray();
                $total++;
                $currentRow = $rowNumber++;

                // Validasi field non-wilayah terlebih dahulu
                $nonRegionValidation = $this->validateNonRegionData($row, $currentRow);
                if (!$nonRegionValidation['valid']) {
                    $pendingErrorRows[] = [
                        'row'    => $currentRow,
                        'errors' => $nonRegionValidation['errors'],
                    ];
                    return;
                }

                $provinsi  = strtolower(trim($row['provinsi'] ?? ''));
                $kabupaten = strtolower(trim($row['kabupaten'] ?? ''));
                $kecamatan = strtolower(trim($row['kecamatan'] ?? ''));
                $kelurahan = strtolower(trim($row['kelurahan'] ?? ''));

                // Coba exact match + fuzzy match
                $matchResult = $fuzzyMatcher->tryCorrect($provinsi, $kabupaten, $kecamatan, $kelurahan);

                if ($matchResult['found']) {
                    // Exact match → langsung masuk pending valid
                    $pendingValidRows[] = [
                        'row'  => $currentRow,
                        'data' => array_merge($nonRegionValidation['data'], [
                            'region_id' => $matchResult['region']->id,
                        ]),
                    ];
                } elseif ($matchResult['correctable']) {
                    // Fuzzy match → perlu review user
                    $fuzzyReviewItems[] = [
                        'row'        => $currentRow,
                        'rowData'    => $nonRegionValidation['data'],
                        'original'   => $matchResult['original'],
                        'suggestion' => $matchResult['suggestion'],
                        'scores'     => $matchResult['scores'],
                        'changed'    => $matchResult['changed'],
                        'decision'   => null,
                    ];
                } else {
                    // Tidak ditemukan sama sekali
                    $pendingErrorRows[] = [
                        'row'    => $currentRow,
                        'errors' => ["Wilayah tidak ditemukan: {$provinsi} / {$kabupaten} / {$kecamatan} / {$kelurahan}"],
                    ];
                }
            });
        } catch (\Throwable $e) {
            $this->logImport('error', 0, 'Gagal membaca file: ' . $e->getMessage());
            $this->importing = false;
            return;
        }

        $this->importTotal      = $total;
        $this->pendingValidRows = $pendingValidRows;
        $this->pendingErrorRows = $pendingErrorRows;
        $this->importing        = false;

        if (!empty($fuzzyReviewItems)) {
            // Ada baris yang perlu review → tampilkan panel review
            $this->fuzzyReviewItems = $fuzzyReviewItems;
            $this->showFuzzyReview  = true;
        } else {
            // Tidak ada yang perlu review → langsung commit
            $this->commitImport();
        }
    }

    /**
     * User menyetujui koreksi untuk satu baris.
     */
    public function approveFuzzyCorrection(int $index): void
    {
        if (!isset($this->fuzzyReviewItems[$index])) return;
        $this->fuzzyReviewItems[$index]['decision'] = 'approve';
    }

    /**
     * User menolak koreksi untuk satu baris (baris akan di-skip).
     */
    public function rejectFuzzyCorrection(int $index): void
    {
        if (!isset($this->fuzzyReviewItems[$index])) return;
        $this->fuzzyReviewItems[$index]['decision'] = 'reject';
    }

    /**
     * User menyetujui semua koreksi sekaligus.
     */
    public function approveAllFuzzyCorrections(): void
    {
        foreach ($this->fuzzyReviewItems as $i => $item) {
            if ($item['decision'] === null) {
                $this->fuzzyReviewItems[$i]['decision'] = 'approve';
            }
        }
    }

    /**
     * User menolak semua koreksi sekaligus.
     */
    public function rejectAllFuzzyCorrections(): void
    {
        foreach ($this->fuzzyReviewItems as $i => $item) {
            if ($item['decision'] === null) {
                $this->fuzzyReviewItems[$i]['decision'] = 'reject';
            }
        }
    }

    /**
     * Cek apakah semua fuzzy items sudah diberi keputusan.
     */
    public function allFuzzyDecided(): bool
    {
        foreach ($this->fuzzyReviewItems as $item) {
            if ($item['decision'] === null) return false;
        }
        return true;
    }

    /**
     * Tahap 2: Setelah user selesai review, commit semua data ke database.
     */
    public function commitImport(): void
    {
        $fuzzyMatcher = new RegionFuzzyMatcher();

        // Proses fuzzy review items yang sudah diberi keputusan
        foreach ($this->fuzzyReviewItems as $item) {
            $row = $item['row'];

            if ($item['decision'] === 'approve') {
                // Resolve region dari suggestion yang disetujui
                $sug    = $item['suggestion'];
                $region = $fuzzyMatcher->resolveRegion(
                    $sug['provinsi'],
                    $sug['kabupaten'],
                    $sug['kecamatan'],
                    $sug['kelurahan']
                );

                if ($region) {
                    $this->pendingValidRows[] = [
                        'row'  => $row,
                        'data' => array_merge($item['rowData'], ['region_id' => $region->id]),
                    ];
                } else {
                    $this->pendingErrorRows[] = [
                        'row'    => $row,
                        'errors' => ['Region tidak ditemukan setelah koreksi.'],
                    ];
                }
            } else {
                // Rejected atau belum diputuskan → skip
                $orig = $item['original'];
                $this->pendingErrorRows[] = [
                    'row'    => $row,
                    'errors' => ["Koreksi wilayah ditolak: {$orig['provinsi']} / {$orig['kabupaten']} / {$orig['kecamatan']} / {$orig['kelurahan']}"],
                ];
            }
        }

        // Urutkan semua baris berdasarkan nomor baris
        $allRows = array_merge(
            array_map(fn($r) => ['type' => 'valid', 'row' => $r['row'], 'payload' => $r], $this->pendingValidRows),
            array_map(fn($r) => ['type' => 'error', 'row' => $r['row'], 'payload' => $r], $this->pendingErrorRows),
        );
        usort($allRows, fn($a, $b) => $a['row'] <=> $b['row']);

        // Insert ke database & build log
        foreach ($allRows as $entry) {
            $row = $entry['row'];
            if ($entry['type'] === 'valid') {
                $data = $entry['payload']['data'];

                // Cek duplikat NIK & HP sebelum insert
                $nikExists = ParticipantModel::withoutTrashed()->where('nik', $data['nik'])->exists();
                $hpExists  = ParticipantModel::withoutTrashed()->where('no_hp', $data['no_hp'])->exists();

                if ($nikExists) {
                    $this->importSkipped++;
                    $this->logImport('error', $row, "Baris {$row}: NIK {$data['nik']} sudah terdaftar");
                    continue;
                }
                if ($hpExists) {
                    $this->importSkipped++;
                    $this->logImport('error', $row, "Baris {$row}: No HP {$data['no_hp']} sudah terdaftar");
                    continue;
                }

                ParticipantModel::create($data);
                $this->importSuccess++;
                $this->logImport('success', $row, "Baris {$row}: {$data['nama']} — berhasil diimpor");
            } else {
                $this->importSkipped++;
                $errors = $entry['payload']['errors'];
                $this->logImport('error', $row, "Baris {$row}: " . implode('; ', $errors));
            }
        }

        $this->logImport('info', 0, "Selesai — {$this->importSuccess} berhasil, {$this->importSkipped} dilewati dari total {$this->importTotal} baris.");

        // Reset state
        $this->showFuzzyReview  = false;
        $this->fuzzyReviewItems = [];
        $this->pendingValidRows = [];
        $this->pendingErrorRows = [];
        $this->importDone       = true;
        $this->importFile       = null;
        $this->dispatch('importFinished', 'participantSaved');
    }

    /**
     * Validasi field non-wilayah (nama, nik, no_hp, alamat).
     * Wilayah divalidasi terpisah via fuzzy matcher.
     */
    private function validateNonRegionData(array $data, int $rowNumber): array
    {
        $errors = [];

        $nama      = trim($data['nama'] ?? '');
        $nik       = preg_replace('/\D/', '', $data['nik'] ?? '');
        $no_hp     = $this->normalizeNoHp(trim($data['no_hp'] ?? ''));
        $alamat    = trim($data['alamat'] ?? '');
        $provinsi  = strtolower(trim($data['provinsi'] ?? ''));
        $kabupaten = strtolower(trim($data['kabupaten'] ?? ''));
        $kecamatan = strtolower(trim($data['kecamatan'] ?? ''));
        $kelurahan = strtolower(trim($data['kelurahan'] ?? ''));

        // Validasi Nama
        if (empty($nama)) {
            $errors[] = 'Nama kosong';
        } elseif (strlen($nama) > 255) {
            $errors[] = 'Nama terlalu panjang (maks 255 karakter)';
        } elseif (!preg_match("/^[a-zA-Z\s\.\']+$/", $nama)) {
            $errors[] = 'Nama mengandung karakter tidak valid';
        }

        // Validasi NIK
        if (strlen($nik) !== 16) {
            $errors[] = 'NIK tidak valid (harus 16 digit angka)';
        }

        // Validasi No HP
        if (empty($no_hp)) {
            $errors[] = 'Nomor HP kosong';
        } elseif (strlen($no_hp) < 10) {
            $errors[] = 'No HP terlalu pendek (min 10 digit)';
        } elseif (strlen($no_hp) > 15) {
            $errors[] = 'No HP terlalu panjang (maks 15 digit)';
        } elseif (!preg_match('/^(08|628)[0-9]+$/', $no_hp)) {
            $errors[] = 'No HP bukan format Indonesia (harus diawali 08 atau 628)';
        }

        // Validasi Alamat
        if (empty($alamat)) {
            $errors[] = 'Alamat kosong';
        } elseif (strlen($alamat) < 5) {
            $errors[] = 'Alamat terlalu pendek (min 5 karakter)';
        }

        // Validasi kolom wilayah tidak kosong
        if (empty($provinsi))  $errors[] = 'Kolom provinsi kosong';
        if (empty($kabupaten)) $errors[] = 'Kolom kabupaten kosong';
        if (empty($kecamatan)) $errors[] = 'Kolom kecamatan kosong';
        if (empty($kelurahan)) $errors[] = 'Kolom kelurahan kosong';

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return [
            'valid' => true,
            'data'  => [
                'nama'       => $nama,
                'nik'        => $nik,
                'no_hp'      => $no_hp,
                'alamat'     => $alamat,
                'status'     => 'pending',
                'created_by' => Auth::id(),
            ],
        ];
    }

    private function resetImportState(): void
    {
        $this->importLogs       = [];
        $this->importTotal      = $this->importSuccess = $this->importSkipped = 0;
        $this->importDone       = $this->importing = false;
        $this->showFuzzyReview  = false;
        $this->fuzzyReviewItems = [];
        $this->pendingValidRows = [];
        $this->pendingErrorRows = [];
    }

    private function logImport(string $type, int $row, string $msg): void
    {
        $this->importLogs[] = ['type' => $type, 'row' => $row, 'msg' => $msg];
    }

    public function resetImport()
    {
        $this->resetImportState();
    }

    // ── Rate Limiter Helper ──────────────────────────────────────────
    private function checkRateLimit(string $key, int $maxAttempts, string $errorField = 'rate_limit'): bool
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError($errorField, "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
            return true;
        }
        RateLimiter::hit($key, 60);
        return false;
    }

    public function render()
    {
        return view('livewire.participant-create');
    }
}
