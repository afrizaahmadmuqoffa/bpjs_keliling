<?php

namespace App\Livewire;

use App\Models\ParticipantModel;
use App\Models\RegionModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;

class ParticipantEdit extends Component
{
    // Participant
    public ParticipantModel $participant;

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
    public bool $isLocked = false;

    public function mount(ParticipantModel $participant)
    {
        // PIC tidak bisa edit data milik orang lain
        if (Auth::user()->role === 'pic' && $participant->created_by !== Auth::id()) {
            abort(403, 'Tidak punya akses terhadap data ini');
        }

        // Hanya admin & super_admin yang bisa edit (pic juga boleh data sendiri)
        if (!in_array(Auth::user()->role, ['admin', 'super_admin', 'pic'])) {
            abort(403, 'Tidak punya akses');
        }

        $this->participant = $participant->load('region');

        // Cek status locked
        $this->isLocked = $participant->status === 'selesai';

        // Isi form dari data existing
        $this->nama      = $participant->nama;
        $this->nik       = $participant->nik;
        $this->no_hp     = $participant->no_hp ?? '';
        $this->alamat    = $participant->alamat ?? '';
        $this->provinsi  = $participant->region->provinsi ?? '';
        $this->kabupaten = $participant->region->kabupaten ?? '';
        $this->kecamatan = $participant->region->kecamatan ?? '';
        $this->kelurahan = $participant->region->kelurahan ?? '';

        // Load region map: provinsi → kabupaten → kecamatan → [kelurahan]
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

        // Pre-populate dropdown lists
        if ($this->provinsi && isset($this->regionMap[$this->provinsi])) {
            $this->kabupatenList = array_keys($this->regionMap[$this->provinsi]);
        }

        if ($this->provinsi && $this->kabupaten && isset($this->regionMap[$this->provinsi][$this->kabupaten])) {
            $this->kecamatanList = array_keys($this->regionMap[$this->provinsi][$this->kabupaten]);
        }

        if (
            $this->provinsi && $this->kabupaten && $this->kecamatan
            && isset($this->regionMap[$this->provinsi][$this->kabupaten][$this->kecamatan])
        ) {
            $this->kelurahanList = $this->regionMap[$this->provinsi][$this->kabupaten][$this->kecamatan];
        }
    }

    public function updatedProvinsi($value)
    {
        $this->kabupaten = '';
        $this->kecamatan = '';
        $this->kelurahan = '';
        $this->kecamatanList = [];
        $this->kelurahanList = [];

        $this->kabupatenList = isset($this->regionMap[$value])
            ? array_keys($this->regionMap[$value])
            : [];
    }

    public function updatedKabupaten($value)
    {
        $this->kecamatan = '';
        $this->kelurahan = '';
        $this->kelurahanList = [];

        $this->kecamatanList = isset($this->regionMap[$this->provinsi][$value])
            ? array_keys($this->regionMap[$this->provinsi][$value])
            : [];
    }

    public function updatedKecamatan($value)
    {
        $this->kelurahan = '';

        $this->kelurahanList = isset($this->regionMap[$this->provinsi][$this->kabupaten][$value])
            ? $this->regionMap[$this->provinsi][$this->kabupaten][$value]
            : [];
    }

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

    public function save()
    {
        $key = 'edit-participant:' . Auth::id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError('rate_limit', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
            return;
        }

        RateLimiter::hit($key, 60);

        // PIC hanya bisa edit data miliknya
        if (Auth::user()->role === 'pic' && $this->participant->created_by !== Auth::id()) {
            abort(403, 'Tidak punya akses terhadap data ini');
        }

        if (!in_array(Auth::user()->role, ['admin', 'super_admin', 'pic'])) {
            abort(403, 'Tidak punya akses');
        }

        if ($this->participant->status === 'selesai') {
            $this->addError('msg', 'Data sudah selesai, tidak bisa diubah.');
            return;
        }

        // Normalisasi no_hp sebelum validasi agar unique check pakai format yang sama dengan DB
        $this->no_hp = $this->normalizeNoHp($this->no_hp);


        $this->validate([
            'nama' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z\s\.\']+$/'
            ],

            'nik' => [
                'required',
                'digits:16',
                Rule::unique('participants', 'nik')
                    ->ignore($this->participant->id)
                    ->withoutTrashed()
            ],

            'no_hp' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^(08|628)[0-9]+$/',
                Rule::unique('participants', 'no_hp')
                    ->ignore($this->participant->id)
                    ->withoutTrashed()
            ],

            'alamat' => [
                'required',
                'string',
                'min:5'
            ],

            'provinsi'  => ['required'],
            'kabupaten' => ['required'],
            'kecamatan' => ['required'],
            'kelurahan' => ['required'],

        ], [

            // ===== NAMA =====
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min'      => 'Nama minimal 3 karakter.',
            'nama.max'      => 'Nama maksimal 255 karakter.',
            'nama.regex'    => 'Nama hanya boleh huruf, spasi, titik, dan apostrof.',

            // ===== NIK =====
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'   => 'NIK sudah terdaftar.',

            // ===== NO HP =====
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.min'      => 'Nomor HP minimal 10 digit.',
            'no_hp.max'      => 'Nomor HP maksimal 15 digit.',
            'no_hp.regex'    => 'Nomor HP harus format Indonesia (08 atau 628).',
            'no_hp.unique'   => 'Nomor HP sudah terdaftar.',

            // ===== ALAMAT =====
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min'      => 'Alamat terlalu pendek.',

            // ===== WILAYAH =====
            'provinsi.required'  => 'Provinsi wajib dipilih.',
            'kabupaten.required' => 'Kabupaten wajib dipilih.',
            'kecamatan.required' => 'Kecamatan wajib dipilih.',
            'kelurahan.required' => 'Kelurahan wajib dipilih.',
        ]);

        $region = RegionModel::whereRaw('LOWER(provinsi) = ?', [strtolower($this->provinsi)])
            ->whereRaw('LOWER(kabupaten) = ?', [strtolower($this->kabupaten)])
            ->whereRaw('LOWER(kecamatan) = ?', [strtolower($this->kecamatan)])
            ->whereRaw('LOWER(kelurahan) = ?', [strtolower($this->kelurahan)])
            ->first();

        if (!$region) {
            $this->addError('region', 'Wilayah tidak ditemukan.');
            return;
        }

        $this->participant->update([
            'nama'      => $this->nama,
            'no_hp'     => $this->no_hp,
            'alamat'    => $this->alamat,
            'region_id' => $region->id,
        ]);

        $this->showSuccess = true;
        $this->successMessage = 'Data peserta berhasil diperbarui!';

        $this->dispatch('participantUpdated');
    }

    public function dismissSuccess()
    {
        $this->showSuccess = false;
    }

    public function render()
    {
        return view('livewire.participant-edit');
    }
}
