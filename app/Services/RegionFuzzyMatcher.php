<?php

namespace App\Services;

use App\Models\RegionModel;
use Illuminate\Support\Collection;

class RegionFuzzyMatcher
{
    // Treshold
    private const SIMILARITY_THRESHOLD = 60;

    /**
     * Alias table: normalisasi prefix/suffix umum yang sering typo di Excel.
     * Key = pola regex, Value = pengganti.
     */
    private const ALIAS_PATTERNS = [
        // Kabupaten / Kota
        '/^kab\.\s*/i'          => '',
        '/^kabupaten\s+/i'      => '',
        '/^kota\s+/i'           => '',
        '/^kab\s+/i'            => '',
        // Kecamatan
        '/^kec\.\s*/i'          => '',
        '/^kecamatan\s+/i'      => '',
        '/^kec\s+/i'            => '',
        // Kelurahan / Desa
        '/^kel\.\s*/i'          => '',
        '/^kelurahan\s+/i'      => '',
        '/^desa\s+/i'           => '',
        '/^ds\.\s*/i'           => '',
        // Provinsi
        '/^prov\.\s*/i'         => '',
        '/^provinsi\s+/i'       => '',
        // Karakter khusus yang sering salah ketik
        '/\s+/'                 => ' ',   // multiple spaces → single
    ];

    /** Cache regionMap agar tidak query ulang */
    private array $regionMap = [];

    /** Flat list per level untuk fuzzy search */
    private array $allProvinsi  = [];
    private array $allKabupaten = [];
    private array $allKecamatan = [];
    private array $allKelurahan = [];

    public function __construct()
    {
        $this->buildAliasTable();
    }

    /**
     * Build pre-computed alias table dari database.
     * Dipanggil sekali saat konstruksi.
     */
    private function buildAliasTable(): void
    {
        $regions = RegionModel::all();

        // Build nested map: provinsi → kabupaten → kecamatan → [kelurahan]
        $this->regionMap = $regions
            ->groupBy(fn($r) => strtolower(trim($r->provinsi)))
            ->map(fn($kabs) =>
                $kabs->groupBy(fn($r) => strtolower(trim($r->kabupaten)))
                    ->map(fn($kecs) =>
                        $kecs->groupBy(fn($r) => strtolower(trim($r->kecamatan)))
                            ->map(fn($items) =>
                                $items->pluck('kelurahan')
                                    ->map(fn($k) => strtolower(trim($k)))
                                    ->values()
                                    ->toArray()
                            )->toArray()
                    )->toArray()
            )->toArray();

        // Flat lists untuk fuzzy search
        $this->allProvinsi  = array_keys($this->regionMap);

        foreach ($this->regionMap as $prov => $kabs) {
            foreach ($kabs as $kab => $kecs) {
                if (!in_array($kab, $this->allKabupaten)) {
                    $this->allKabupaten[] = $kab;
                }
                foreach ($kecs as $kec => $kels) {
                    if (!in_array($kec, $this->allKecamatan)) {
                        $this->allKecamatan[] = $kec;
                    }
                    foreach ($kels as $kel) {
                        if (!in_array($kel, $this->allKelurahan)) {
                            $this->allKelurahan[] = $kel;
                        }
                    }
                }
            }
        }
    }

    /**
     * Normalisasi string: lowercase, trim, hapus alias prefix/suffix.
     */
    public function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        foreach (self::ALIAS_PATTERNS as $pattern => $replacement) {
            $value = preg_replace($pattern, $replacement, $value);
        }
        return trim($value);
    }

    /**
     * Hitung similarity score (0–100) antara dua string.
     * Kombinasi similar_text + levenshtein untuk akurasi lebih baik.
     */
    public function similarity(string $a, string $b): float
    {
        if ($a === $b) return 100.0;
        if (empty($a) || empty($b)) return 0.0;

        // similar_text score
        similar_text($a, $b, $pct);

        // Levenshtein-based score
        $maxLen = max(strlen($a), strlen($b));
        $lev    = levenshtein($a, $b);
        $levPct = (1 - ($lev / $maxLen)) * 100;

        // Weighted average: similar_text lebih sensitif untuk substring
        return ($pct * 0.6) + ($levPct * 0.4);
    }

    /**
     * Cari kandidat terbaik dari daftar kandidat.
     * Return: ['match' => string|null, 'score' => float]
     */
    public function findBest(string $input, array $candidates): array
    {
        $normalized = $this->normalize($input);
        $best       = null;
        $bestScore  = 0.0;

        foreach ($candidates as $candidate) {
            $normCandidate = $this->normalize($candidate);

            // Exact match setelah normalisasi
            if ($normalized === $normCandidate) {
                return ['match' => $candidate, 'score' => 100.0, 'exact' => true];
            }

            $score = $this->similarity($normalized, $normCandidate);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best      = $candidate;
            }
        }

        if ($bestScore >= self::SIMILARITY_THRESHOLD) {
            return ['match' => $best, 'score' => round($bestScore, 1), 'exact' => false];
        }

        return ['match' => null, 'score' => $bestScore, 'exact' => false];
    }

    /**
     * Coba koreksi satu baris wilayah dari Excel.
     *
     * Return array:
     * - 'found'       => bool  (exact match ditemukan, tidak perlu koreksi)
     * - 'correctable' => bool  (fuzzy match ditemukan, perlu konfirmasi user)
     * - 'region'      => RegionModel|null (jika found)
     * - 'suggestion'  => array ['provinsi', 'kabupaten', 'kecamatan', 'kelurahan'] (jika correctable)
     * - 'original'    => array ['provinsi', 'kabupaten', 'kecamatan', 'kelurahan']
     * - 'scores'      => array skor per field
     * - 'changed'     => array field mana saja yang dikoreksi
     */
    public function tryCorrect(
        string $provinsi,
        string $kabupaten,
        string $kecamatan,
        string $kelurahan
    ): array {
        $original = compact('provinsi', 'kabupaten', 'kecamatan', 'kelurahan');

        $normProv = $this->normalize($provinsi);
        $normKab  = $this->normalize($kabupaten);
        $normKec  = $this->normalize($kecamatan);
        $normKel  = $this->normalize($kelurahan);

        // ── Step 1: Exact match ──────────────────────────────────────
        if (isset($this->regionMap[$normProv][$normKab][$normKec])) {
            $kels = $this->regionMap[$normProv][$normKab][$normKec];
            if (in_array($normKel, $kels)) {
                // Temukan RegionModel yang sesuai
                $region = RegionModel::whereRaw('LOWER(TRIM(provinsi)) = ?', [$normProv])
                    ->whereRaw('LOWER(TRIM(kabupaten)) = ?', [$normKab])
                    ->whereRaw('LOWER(TRIM(kecamatan)) = ?', [$normKec])
                    ->whereRaw('LOWER(TRIM(kelurahan)) = ?', [$normKel])
                    ->first();

                return [
                    'found'       => true,
                    'correctable' => false,
                    'region'      => $region,
                    'suggestion'  => null,
                    'original'    => $original,
                    'scores'      => [],
                    'changed'     => [],
                ];
            }
        }

        // ── Step 2: Fuzzy match bertahap ─────────────────────────────
        // 2a. Cari provinsi terbaik
        $provResult = $this->findBest($provinsi, $this->allProvinsi);
        if (!$provResult['match']) {
            return $this->notFound($original);
        }
        $bestProv = $provResult['match'];

        // 2b. Cari kabupaten terbaik dalam provinsi yang ditemukan
        $kabCandidates = array_keys($this->regionMap[$bestProv] ?? []);
        if (empty($kabCandidates)) return $this->notFound($original);

        $kabResult = $this->findBest($kabupaten, $kabCandidates);
        if (!$kabResult['match']) return $this->notFound($original);
        $bestKab = $kabResult['match'];

        // 2c. Cari kecamatan terbaik dalam kabupaten yang ditemukan
        $kecCandidates = array_keys($this->regionMap[$bestProv][$bestKab] ?? []);
        if (empty($kecCandidates)) return $this->notFound($original);

        $kecResult = $this->findBest($kecamatan, $kecCandidates);
        if (!$kecResult['match']) return $this->notFound($original);
        $bestKec = $kecResult['match'];

        // 2d. Cari kelurahan terbaik dalam kecamatan yang ditemukan
        $kelCandidates = $this->regionMap[$bestProv][$bestKab][$bestKec] ?? [];
        if (empty($kelCandidates)) return $this->notFound($original);

        $kelResult = $this->findBest($kelurahan, $kelCandidates);
        if (!$kelResult['match']) return $this->notFound($original);
        $bestKel = $kelResult['match'];

        // ── Step 3: Cek apakah ada perubahan ─────────────────────────
        $changed = [];
        if ($normProv !== $bestProv) $changed[] = 'provinsi';
        if ($normKab  !== $bestKab)  $changed[] = 'kabupaten';
        if ($normKec  !== $bestKec)  $changed[] = 'kecamatan';
        if ($normKel  !== $bestKel)  $changed[] = 'kelurahan';

        // Jika tidak ada perubahan, berarti exact match sudah ditemukan
        if (empty($changed)) {
            $region = RegionModel::whereRaw('LOWER(TRIM(provinsi)) = ?', [$bestProv])
                ->whereRaw('LOWER(TRIM(kabupaten)) = ?', [$bestKab])
                ->whereRaw('LOWER(TRIM(kecamatan)) = ?', [$bestKec])
                ->whereRaw('LOWER(TRIM(kelurahan)) = ?', [$bestKel])
                ->first();

            return [
                'found'       => true,
                'correctable' => false,
                'region'      => $region,
                'suggestion'  => null,
                'original'    => $original,
                'scores'      => [],
                'changed'     => [],
            ];
        }

        // Ada koreksi → kembalikan saran
        return [
            'found'       => false,
            'correctable' => true,
            'region'      => null,
            'suggestion'  => [
                'provinsi'  => $bestProv,
                'kabupaten' => $bestKab,
                'kecamatan' => $bestKec,
                'kelurahan' => $bestKel,
            ],
            'original'    => $original,
            'scores'      => [
                'provinsi'  => $provResult['score'],
                'kabupaten' => $kabResult['score'],
                'kecamatan' => $kecResult['score'],
                'kelurahan' => $kelResult['score'],
            ],
            'changed'     => $changed,
        ];
    }

    /**
     * Helper: return "not found" result.
     */
    private function notFound(array $original): array
    {
        return [
            'found'       => false,
            'correctable' => false,
            'region'      => null,
            'suggestion'  => null,
            'original'    => $original,
            'scores'      => [],
            'changed'     => [],
        ];
    }

    /**
     * Ambil RegionModel berdasarkan suggestion yang sudah disetujui user.
     */
    public function resolveRegion(
        string $provinsi,
        string $kabupaten,
        string $kecamatan,
        string $kelurahan
    ): ?RegionModel {
        $normProv = $this->normalize($provinsi);
        $normKab  = $this->normalize($kabupaten);
        $normKec  = $this->normalize($kecamatan);
        $normKel  = $this->normalize($kelurahan);

        return RegionModel::whereRaw('LOWER(TRIM(provinsi)) = ?', [$normProv])
            ->whereRaw('LOWER(TRIM(kabupaten)) = ?', [$normKab])
            ->whereRaw('LOWER(TRIM(kecamatan)) = ?', [$normKec])
            ->whereRaw('LOWER(TRIM(kelurahan)) = ?', [$normKel])
            ->first();
    }
}
