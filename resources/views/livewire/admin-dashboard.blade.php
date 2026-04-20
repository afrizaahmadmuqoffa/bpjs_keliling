<div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Admin Dashboard</h1>
            <p class="text-sm text-slate-500">Pantau aktivitas pelayanan BPJS Keliling secara real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-medium px-3 py-1.5 bg-secondary/10 text-secondary rounded-full border border-secondary/20">
                <i class="fas fa-circle text-[8px] mr-2"></i> Sistem Online
            </span>
            <div class="text-sm text-slate-500 font-medium">
                <i class="far fa-calendar-alt mr-1"></i> {{ date('d M Y') }}
            </div>
        </div>
    </div>

    {{-- CARD SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary text-2xl">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Peserta</p>
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $total }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center text-orange-500 text-2xl">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Antrean Pending</p>
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $pending ?? 0 }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary text-2xl">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Layanan Selesai</p>
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $selesai ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- FILTER WILAYAH --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-filter text-primary text-xs"></i> Filter Wilayah
                </h4>

                <div class="space-y-4">

                    {{-- PROVINSI --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Provinsi</label>
                        <select
                            wire:model.live="provinsi"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm"
                        >
                            <option value="">Semua Provinsi</option>
                            @foreach($provinsiList as $prov)
                                <option value="{{ $prov }}">{{ ucwords($prov) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KABUPATEN --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Kabupaten</label>
                        <select
                            wire:model.live="kabupaten"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm"
                            @disabled(!$provinsi)
                        >
                            <option value="">Semua Kabupaten</option>
                            @foreach($kabupatenList as $kab)
                                <option value="{{ $kab }}">{{ ucfirst($kab) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KECAMATAN --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Kecamatan</label>
                        <select
                            wire:model.live="kecamatan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm"
                            @disabled(!$kabupaten)
                        >
                            <option value="">Semua Kecamatan</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec }}">{{ ucwords($kec) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KELURAHAN --}}
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Kelurahan</label>
                        <select
                            wire:model.live="kelurahan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm"
                            @disabled(!$kecamatan)
                        >
                            <option value="">Semua Kelurahan</option>
                            @foreach($kelurahanList as $kel)
                                <option value="{{ $kel }}">{{ ucwords($kel) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- RESET --}}
                    @if($provinsi || $kabupaten || $kecamatan || $kelurahan)
                        <button
                            wire:click="$set('provinsi', ''); $set('kabupaten', ''); $set('kecamatan', ''); $set('kelurahan', '')"
                            class="w-full text-xs text-slate-400 hover:text-red-500 transition-colors text-center py-1"
                        >
                            <i class="fas fa-times-circle mr-1"></i> Reset Filter
                        </button>
                    @endif

                </div>
            </div>
        </div>

        {{-- CHART --}}
        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- SEGMENTASI --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h4 class="font-bold text-slate-800 mb-6">Segmentasi Peserta</h4>
                <div class="h-64" wire:ignore>
                    <canvas id="segmentasiChart"></canvas>
                </div>
            </div>

            {{-- LAYANAN --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h4 class="font-bold text-slate-800 mb-6">Top Layanan</h4>
                <div class="h-64" wire:ignore>
                    <canvas id="layananChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h4 class="font-bold text-slate-800">Data Peserta Terbaru</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Identitas Peserta</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Lokasi & Alamat</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Layanan / Segment</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($latest as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            {{-- IDENTITAS --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 group-hover:text-primary">{{ $p->nama }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono mt-1">NIK: {{ $p->nik }}</span>
                                    <span class="text-[11px] text-secondary font-semibold mt-0.5">
                                        <i class="fas fa-phone-alt text-[9px] mr-1"></i>{{ $p->no_hp }}
                                    </span>
                                </div>
                            </td>
                            {{-- LOKASI --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col max-w-[220px]">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ ucwords($p->region->provinsi) }}</span>
                                    <span class="text-xs font-bold text-slate-600">
                                        {{ ucfirst($p->region->kabupaten) }},
                                        {{ ucfirst($p->region->kecamatan) }},
                                        {{ ucfirst($p->region->kelurahan) }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 truncate mt-1 italic">{{ $p->alamat }}</span>
                                </div>
                            </td>
                            {{-- LAYANAN --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold text-slate-700">{{ $p->service->nama ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">{{ $p->segment->nama ?? '-' }}</span>
                                </div>
                            </td>
                            {{-- STATUS --}}
                            <td class="px-6 py-4 text-center">
                                @if($p->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-100 uppercase">
                                        <i class="fas fa-history mr-1.5 text-[9px]"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-secondary/10 text-secondary border border-secondary/20 uppercase">
                                        <i class="fas fa-check-circle mr-1.5 text-[9px]"></i> Selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-slate-200 text-5xl mb-4"></i>
                                    <p class="text-slate-400 font-medium text-sm">Tidak ada data peserta.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let segmentChart = null;
    let layananChart = null;

    function initCharts(sLabels, sData, lLabels, lData) {
        // Hancurkan chart lama jika ada
        if (segmentChart) segmentChart.destroy();
        if (layananChart) layananChart.destroy();

        // Pastikan canvas masih ada di DOM
        const segmentCanvas = document.getElementById('segmentasiChart');
        const layananCanvas = document.getElementById('layananChart');

        if (!segmentCanvas || !layananCanvas) {
            console.warn('Canvas element not found');
            return;
        }

        segmentChart = new Chart(segmentCanvas, {
            type: 'doughnut',
            data: {
                labels: sLabels,
                datasets: [{
                    data: sData,
                    backgroundColor: ['#033e87', '#01a850', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        layananChart = new Chart(layananCanvas, {
            type: 'bar',
            data: {
                labels: lLabels,
                datasets: [{
                    data: lData,
                    backgroundColor: '#01a850',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { display: false }, grid: { display: false } },
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Inisialisasi pertama kali
    document.addEventListener('DOMContentLoaded', () => {
        const segmentLabels = @json($segments->map(fn($s) => $s->segment->nama ?? 'Unknown'));
        const segmentData   = @json($segments->pluck('total'));
        const serviceLabels = @json($services->map(fn($s) => $s->service->nama ?? 'Unknown'));
        const serviceData   = @json($services->pluck('total'));

        initCharts(segmentLabels, segmentData, serviceLabels, serviceData);
    });

    // Update chart saat Livewire dispatch event
    window.addEventListener('chartDataUpdated', (event) => {
        const { segmentLabels, segmentData, serviceLabels, serviceData } = event.detail[0];
        initCharts(segmentLabels, segmentData, serviceLabels, serviceData);
    });
</script>
