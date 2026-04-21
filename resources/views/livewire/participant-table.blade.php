{{-- resources/views/livewire/participant-table.blade.php --}}
<div class="min-h-screen pb-24 lg:pb-0">

    {{-- ===== TOAST NOTIFICATION ===== --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-slate-800 text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-xl"
        style="display: none;">
        <i class="fas fa-check-circle text-green-400"></i>
        <span x-text="message"></span>
    </div>

    {{-- ===== ERROR ALERT ===== --}}
    @if ($errors->any())
    <div
        x-data="{ visible: true }"
        x-show="visible"
        class="mb-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
        <i class="fas fa-exclamation-circle mt-0.5 text-red-400"></i>
        <div class="flex-1">
            @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        <button @click="visible = false" class="text-red-400 hover:text-red-600 ml-2">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    @endif

    {{-- ===== SEARCH & FILTER ===== --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <div class="flex flex-col md:flex-row gap-4">

            {{-- SEARCH INPUT --}}
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input
                    wire:model.live.debounce.400ms="search"
                    type="text"
                    placeholder="Cari Nama, NIK, No hp, atau Alamat..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
            </div>

            {{-- STATUS FILTER & EXPORT (DESKTOP) --}}
            <div class="flex gap-3">
                <div class="relative">
                    <select
                        wire:model.live="statusFilter"
                        class="appearance-none pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all cursor-pointer text-slate-600 font-medium">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="selesai">Selesai</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-filter text-xs"></i>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                {{-- DOWNLOAD EXCEL - DESKTOP ONLY --}}
                <button
                    wire:click="downloadExcel"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    class="hidden lg:flex items-center gap-2 px-4 py-2.5 bg-secondary text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all shadow-sm disabled:opacity-60"
                    title="Download Excel">
                    <span wire:loading.remove wire:target="downloadExcel">
                        <i class="fas fa-file-export text-xs"></i>
                        <span class="hidden sm:inline ml-1">Export</span>
                    </span>
                    <span wire:loading wire:target="downloadExcel">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Identitas Peserta</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Lokasi & Alamat</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Layanan / Segment</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Selesai</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Petugas</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($participants as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors group" wire:key="participant-{{ $p->id }}">

                        {{-- IDENTITAS --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700 group-hover:text-primary transition-colors">{{ $p->nama }}</span>
                                <span class="text-[13px] text-slate-400 font-mono mt-1 tracking-tighter">NIK: {{ $p->nik }}</span>
                                <span class="text-[13px] text-secondary font-semibold mt-0.5">
                                    <i class="fas fa-phone-alt text-[9px] mr-1"></i> {{ $p->no_hp }}
                                </span>
                            </div>
                        </td>

                        {{-- LOKASI --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col max-w-55">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ ucwords($p->region->provinsi) }}</span>
                                <span class="text-xs font-bold text-slate-600">
                                    {{ ucwords($p->region->kabupaten) }},
                                    {{ ucwords($p->region->kecamatan) }},
                                    {{ ucwords($p->region->kelurahan) }}
                                </span>
                                <span class="text-[11px] text-slate-400 truncate mt-1 italic" title="{{ $p->alamat }}">
                                    {{ $p->alamat }}
                                </span>
                            </div>
                        </td>

                        {{-- LAYANAN / SEGMENT --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-center gap-1">
                                @if($p->status === 'pending' && in_array(auth()->user()->role, ['admin', 'super_admin']))
                                <select wire:change="updateField('{{ $p->id }}', 'layanan_id', $event.target.value)" class="w-82.5 text-[11px] font-semibold bg-transparent border-none focus:ring-0 p-0 cursor-pointer text-slate-600 hover:text-primary text-center">
                                    <option value="">-</option>
                                    @foreach($services as $s)
                                    <option value="{{ $s->id }}" {{ $p->layanan_id == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                                <select wire:change="updateField('{{ $p->id }}', 'segment_id', $event.target.value)" class="w-82.5 text-[11px] font-semibold bg-transparent border-none focus:ring-0 p-0 cursor-pointer text-slate-600 hover:text-primary text-center">
                                    <option value="">-</option>
                                    @foreach($segments as $seg)
                                    <option value="{{ $seg->id }}" {{ $p->segment_id == $seg->id ? 'selected' : '' }}>{{ $seg->nama }}</option>
                                    @endforeach
                                </select>
                                @else
                                <span class="text-xs font-bold text-slate-700 max-w-75 truncate block">{{ $p->service->nama ?? '-' }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase max-w-75 truncate block">{{ $p->segment?->nama ?? '-' }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($p->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-100 uppercase tracking-wider">
                                <i class="fas fa-history mr-1.5 text-[9px]"></i> {{ $p->status }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-secondary/10 text-secondary border border-secondary/20 uppercase tracking-wider">
                                <i class="fas fa-check-circle mr-1.5 text-[9px]"></i> {{ $p->status }}
                            </span>
                            @endif
                        </td>

                        {{-- TANGGAL SELESAI --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($p->tanggal_selesai)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100 uppercase tracking-wider">
                                <i class="fas fa-calendar-check mr-1.5 text-[9px]"></i>
                                {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                            </span>
                            @else
                            <span class="text-[11px] text-slate-400 italic">-</span>
                            @endif
                        </td>

                        {{-- PETUGAS --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex flex-col gap-2 text-[11px] items-center">
                                <div class="w-full max-w-35 flex items-center gap-2 px-2 py-1.5 rounded-md bg-blue-50 border border-blue-100 justify-center">
                                    <div class="w-5 h-5 flex items-center justify-center rounded bg-blue-100 text-blue-600">
                                        <i class="fas fa-user-plus text-[10px]"></i>
                                    </div>
                                    <div class="flex flex-col items-center leading-tight w-full">
                                        <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider text-center">Input</span>
                                        <span class="font-semibold text-slate-700 truncate w-full text-center">{{ $p->creator->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="w-full max-w-35 flex items-center gap-2 px-2 py-1.5 rounded-md bg-green-50 border border-green-100 justify-center">
                                    <div class="w-5 h-5 flex items-center justify-center rounded bg-green-100 text-green-600">
                                        <i class="fas fa-user-check text-[10px]"></i>
                                    </div>
                                    <div class="flex flex-col items-center leading-tight w-full">
                                        <span class="text-[9px] font-bold text-green-500 uppercase tracking-wider text-center">Proses</span>
                                        <span class="font-semibold text-slate-700 truncate w-full text-center">{{ $p->processor->nama ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- AKSI --}}
                        @php
                        $isAdminOrSuper = in_array(auth()->user()->role, ['admin', 'super_admin']);
                        $isPic = auth()->user()->role === 'pic';
                        $isOwner = $p->created_by === auth()->id();
                        $canAct = $isAdminOrSuper || ($isPic && $isOwner);
                        @endphp
                        @if($canAct)
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex justify-end gap-2">
                                @if($p->status === 'pending' && $isAdminOrSuper)
                                <button wire:click="sendWa('{{ $p->id }}')" wire:confirm="Proses peserta ini? WA notifikasi akan dikirim ke peserta." wire:loading.attr="disabled" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Kirim WA">
                                    <span wire:loading wire:target="sendWa('{{ $p->id }}')"><i class="fas fa-spinner fa-spin"></i></span>
                                    <span wire:loading.remove wire:target="sendWa('{{ $p->id }}')"><i class="fab fa-whatsapp text-xs"></i></span>
                                </button>
                                @endif
                                @if($p->status === 'pending' && $isAdminOrSuper)
                                <button wire:click="process('{{ $p->id }}')" wire:confirm="Proses peserta ini? WA notifikasi akan dikirim ke peserta." wire:loading.attr="disabled" class="w-8 h-8 flex items-center justify-center rounded-lg bg-secondary/10 text-secondary hover:bg-secondary hover:text-white transition-all shadow-sm disabled:opacity-50" title="Proses Data">
                                    <span wire:loading wire:target="process('{{ $p->id }}')"><i class="fas fa-spinner fa-spin"></i></span>
                                    <span wire:loading.remove wire:target="process('{{ $p->id }}')"><i class="fas fa-check text-xs"></i></span>
                                </button>
                                @endif
                                @if($p->status === 'pending')
                                <a href="{{ route('participants.edit', $p->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @endif
                                @if($p->status === 'pending')
                                <button wire:click="delete('{{ $p->id }}')" wire:confirm="Hapus data peserta ini?" wire:loading.attr="disabled" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm disabled:opacity-50" title="Hapus">
                                    <span wire:loading wire:target="delete('{{ $p->id }}')"><i class="fas fa-spinner fa-spin text-red-600"></i></span>
                                    <span wire:loading.remove wire:target="delete('{{ $p->id }}')"><i class="fas fa-trash-alt text-xs"></i></span>
                                </button>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-folder-open text-slate-200 text-5xl mb-4"></i>
                                <p class="text-slate-400 font-medium text-sm">Tidak ada data peserta ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $participants->links() }}
        </div>
    </div>

    {{-- ===== FLOATING ACTION BUTTONS (MOBILE/TABLET ONLY) ===== --}}
    <div class="lg:hidden fixed bottom-6 right-6 z-40 flex flex-col gap-3">
        
        {{-- FAB Export Excel --}}
        <button
            wire:click="downloadExcel"
            wire:loading.attr="disabled"
            class="group relative w-14 h-14 flex items-center justify-center bg-slate-800 text-white rounded-full shadow-lg hover:bg-slate-700 transition-all active:scale-95 disabled:opacity-60"
            title="Export Excel">
            {{-- Tooltip --}}
            <span class="absolute right-full mr-3 px-3 py-1.5 bg-slate-800 text-white text-[11px] font-medium rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                Export Excel
            </span>
            {{-- Icon --}}
            <span wire:loading.remove wire:target="downloadExcel">
                <i class="fas fa-file-export text-lg"></i>
            </span>
            <span wire:loading wire:target="downloadExcel">
                <i class="fas fa-spinner fa-spin text-lg"></i>
            </span>
        </button>

        {{-- FAB Tambah Peserta --}}
        <a
            href="{{ route('participants.create') }}"
            class="group relative w-14 h-14 flex items-center justify-center bg-secondary text-white rounded-full shadow-lg hover:bg-green-700 transition-all active:scale-95"
            title="Tambah Peserta Baru">
            {{-- Tooltip --}}
            <span class="absolute right-full mr-3 px-3 py-1.5 bg-slate-800 text-white text-[11px] font-medium rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                Tambah Peserta
            </span>
            {{-- Icon --}}
            <i class="fas fa-plus text-lg"></i>
        </a>
    </div>

</div>