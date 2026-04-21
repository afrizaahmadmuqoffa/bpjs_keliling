<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ParticipantModel;
use App\Models\RegionModel;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public string $provinsi  = '';
    public string $kabupaten = '';
    public string $kecamatan = '';
    public string $kelurahan = '';
    public $filtersOpen = false;

    public function updatedProvinsi(): void
    {
        $this->kabupaten = '';
        $this->kecamatan = '';
        $this->kelurahan = '';
    }

    public function updatedKabupaten(): void
    {
        $this->kecamatan = '';
        $this->kelurahan = '';
    }

    public function updatedKecamatan(): void
    {
        $this->kelurahan = '';
    }



    /**
     * Dispatch event ke JS agar chart di-refresh dengan data terbaru.
     * Dipanggil di akhir render() via $this->dispatch().
     */
    private function dispatchChartData(
        $segments,
        $services,
    ): void {
        $this->dispatch('chartDataUpdated', [
            'segmentLabels' => $segments->map(fn($s) => $s->segment->nama ?? 'Unknown')->values(),
            'segmentData'   => $segments->pluck('total')->values(),
            'serviceLabels' => $services->map(fn($s) => $s->service->nama ?? 'Unknown')->values(),
            'serviceData'   => $services->pluck('total')->values(),
        ]);
    }

    public function render()
    {
        $query = ParticipantModel::query()->with('region');

        if ($this->provinsi) {
            $query->whereHas('region', fn($q) => $q->where('provinsi', $this->provinsi));
        }
        if ($this->kabupaten) {
            $query->whereHas('region', fn($q) => $q->where('kabupaten', $this->kabupaten));
        }
        if ($this->kecamatan) {
            $query->whereHas('region', fn($q) => $q->where('kecamatan', $this->kecamatan));
        }
        if ($this->kelurahan) {
            $query->whereHas('region', fn($q) => $q->where('kelurahan', $this->kelurahan));
        }

        // Summary
        $summary = (clone $query)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
            ")
            ->first();

        // Top Segment
        $segments = (clone $query)
            ->whereNotNull('segment_id')
            ->select('segment_id', DB::raw('count(*) as total'))
            ->groupBy('segment_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->load('segment');

        // Top Layanan
        $services = (clone $query)
            ->whereNotNull('layanan_id')
            ->select('layanan_id', DB::raw('count(*) as total'))
            ->groupBy('layanan_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->load('service');

        // Latest
        $latest = (clone $query)->latest()->take(10)->get();

        // Region data
        $regions = RegionModel::all();

        $regionMap = $regions
            ->groupBy('provinsi')
            ->map(fn($kabs) =>
                $kabs->groupBy('kabupaten')
                    ->map(fn($kecs) =>
                        $kecs->groupBy('kecamatan')
                            ->map(fn($items) => $items->pluck('kelurahan')->values())
                    )
            );

        $provinsiList = $regionMap->keys()->sort()->values();

        $kabupatenList = $this->provinsi && isset($regionMap[$this->provinsi])
            ? $regionMap[$this->provinsi]->keys()->sort()->values()->toArray()
            : [];

        $kecamatanList = $this->provinsi && $this->kabupaten
            && isset($regionMap[$this->provinsi][$this->kabupaten])
            ? array_keys($regionMap[$this->provinsi][$this->kabupaten]->toArray())
            : [];

        $kelurahanList = $this->provinsi && $this->kabupaten && $this->kecamatan
            && isset($regionMap[$this->provinsi][$this->kabupaten][$this->kecamatan])
            ? $regionMap[$this->provinsi][$this->kabupaten][$this->kecamatan]->toArray()
            : [];

        $this->dispatchChartData($segments, $services);

        return view('livewire.admin-dashboard', [
            'total'          => $summary->total,
            'pending'        => $summary->pending,
            'selesai'        => $summary->selesai,
            'segments'       => $segments,
            'services'       => $services,
            'latest'         => $latest,
            'regions'        => $regions,
            'regionMap'      => $regionMap,
            'provinsiList'   => $provinsiList,
            'kabupatenList'  => $kabupatenList,
            'kecamatanList'  => $kecamatanList,
            'kelurahanList'  => $kelurahanList,
        ]);
    }
}

