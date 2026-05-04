<div class="space-y-8 px-4 py-14 lg:px-10 lg:py-10">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        {{-- LEFT --}}
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Formulir Pendaftaran Peserta</h2>
            <p class="text-sm text-slate-500 mt-1">Registrasi peserta BPJS Keliling.</p>
        </div>
        {{-- RIGHT (BUTTON GROUP) --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- DOWNLOAD TEMPLATE --}}
            <a href="{{ route('participants.template') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-slate-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-semibold hover:bg-slate-800 transition shadow-sm">
                <i class="fas fa-download text-[10px]"></i>
                <span class="hidden sm:inline">Download Template</span>
                <span class="sm:hidden">Template</span>
            </a>
            {{-- IMPORT --}}
            <button type="button" x-data @click="$dispatch('open-import-modal')"
                class="inline-flex items-center justify-center gap-1.5 bg-secondary text-white px-3.5 py-2.5 rounded-xl text-xs font-semibold hover:bg-green-700 transition shadow-sm">
                <i class="fas fa-file-import text-[10px]"></i>
                <span class="hidden sm:inline">Import Excel</span>
                <span class="sm:hidden">Import</span>
            </button>
        </div>
    </div>

    {{-- SUCCESS ALERT --}}
    @if ($showSuccess)
    <div x-data="{ visible: true }"
        x-init="setTimeout(() => { visible = false; $wire.dismissSuccess() }, 4000)"
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 shadow-sm">
        <i class="fas fa-check-circle text-green-500 text-lg"></i>
        <span class="text-sm font-semibold">{{ $successMessage }}</span>
        <button wire:click="dismissSuccess" class="ml-auto text-green-400 hover:text-green-600 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    {{-- ERROR MESSAGES --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">
        @foreach ($errors->all() as $error)
        <div class="flex items-start gap-2"><i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i> {{ $error }}</div>
        @endforeach
    </div>
    @endif

    {{-- FORM CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- INFORMASI PRIBADI --}}
        <div class="p-5 lg:p-6 border-b border-slate-50 bg-slate-50/30">
            <div class="flex items-center gap-3 mb-5 lg:mb-6">
                <div class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3 class="font-bold text-slate-700">Informasi Pribadi</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                {{-- NAMA --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Nama Lengkap</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <input wire:model="nama" type="text" placeholder="Contoh: Desta Oli Samping"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            class="block w-full pl-11 pr-4 py-3 bg-white border @error('nama') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                    @error('nama') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- NIK --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">NIK</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-id-card text-sm"></i>
                        </div>
                        <input wire:model="nik" type="text" maxlength="16" inputmode="numeric" placeholder="16 Digit NIK"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full pl-11 pr-4 py-3 bg-white border @error('nik') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                    @error('nik') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- NO HP --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Nomor HP/WA</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-phone text-sm"></i>
                        </div>
                        <input wire:model="no_hp" type="text" maxlength="16" placeholder="0812xxxx" inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="block w-full pl-11 pr-4 py-3 bg-white border @error('no_hp') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                    @error('no_hp') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- ALAMAT --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Alamat Domisili</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-map-marker-alt text-sm"></i>
                        </div>
                        <input wire:model="alamat" type="text" placeholder="Jl. Raya No. 123, RT/RW..."
                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                </div>
            </div>
        </div>

        {{-- WILAYAH DOMISILI --}}
        <div class="p-5 lg:p-6">
            <div class="flex items-center gap-3 mb-5 lg:mb-6">
                <div class="w-8 h-8 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3 class="font-bold text-slate-700">Wilayah Domisili</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                {{-- PROVINSI --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Provinsi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-map-marked-alt text-sm"></i>
                        </div>
                        <select wire:model.live="provinsi"
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('provinsi') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none">
                            <option value="">Pilih Provinsi</option>
                            @foreach (array_keys($regionMap) as $prov)
                            <option value="{{ $prov }}" class="capitalize">{{ ucwords($prov) }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                    @error('provinsi') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- KABUPATEN --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Kabupaten</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-city text-sm"></i>
                        </div>
                        <select wire:model.live="kabupaten"
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('kabupaten') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none"
                            @if(empty($kabupatenList)) disabled @endif>
                            <option value="">Pilih Kabupaten</option>
                            @foreach ($kabupatenList as $kab)
                            <option value="{{ $kab }}" class="capitalize">{{ strtolower($kab) }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                    @error('kabupaten') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- KECAMATAN --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Kecamatan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-map-signs text-sm"></i>
                        </div>
                        <select wire:model.live="kecamatan"
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('kecamatan') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none"
                            @if(empty($kecamatanList)) disabled @endif>
                            <option value="">Pilih Kecamatan</option>
                            @foreach ($kecamatanList as $kec)
                            <option value="{{ $kec }}" class="capitalize">{{ strtolower($kec) }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                    @error('kecamatan') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- KELURAHAN --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Kelurahan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <select wire:model="kelurahan"
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('kelurahan') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none"
                            @if(empty($kelurahanList)) disabled @endif>
                            <option value="">Pilih Kelurahan</option>
                            @foreach ($kelurahanList as $kel)
                            <option value="{{ $kel }}" class="capitalize">{{ strtolower($kel) }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                    @error('kelurahan') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="bg-slate-50 p-5 lg:p-6 flex flex-col sm:flex-row items-start justify-between gap-3">
            <p class="text-[10px] lg:text-xs text-slate-400 italic text-center sm:text-left">
                <i class="fas fa-info-circle mr-1"></i> Pastikan semua data sudah benar sebelum menyimpan.
            </p>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" onclick="window.history.back()"
                    class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-200 rounded-xl transition-all">
                    Batal
                </button>
                <button wire:click="save" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                    class="flex-1 sm:flex-none bg-primary hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> <span class="hidden sm:inline">Simpan Data Peserta</span><span class="sm:hidden">Simpan</span></span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> <span class="hidden sm:inline">Menyimpan...</span><span class="sm:hidden">Proses...</span></span>
                </button>
            </div>
        </div>
    </div>


    {{-- ===================================================================== --}}
    {{-- MODAL IMPORT EXCEL - RESPONSIVE                                      --}}
    {{-- ===================================================================== --}}
    <div x-data="{ open: false }"
        x-on:open-import-modal.window="open = true"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
        style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>

        {{-- Panel - Full screen on mobile, centered card on desktop --}}
        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-full sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:scale-95"
            x-on:import-finished.window=""
            class="relative bg-white sm:rounded-2xl rounded-t-2xl shadow-2xl w-full sm:max-w-3xl max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-hidden animate-slide-up">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/60 sticky top-0 z-10">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 bg-secondary/10 text-secondary rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-800 text-sm truncate">Import Data Peserta</h3>
                        <p class="text-[10px] text-slate-400 truncate hidden sm:block">Format: .xlsx / .xls — Kolom: nama, nik, no_hp, alamat, provinsi, kabupaten, kecamatan, kelurahan, layanan</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 text-slate-400 hover:text-slate-600 transition shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-4">

                @if (!$importDone && !$showFuzzyReview)
                {{-- FILE PICKER --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pilih File Excel</label>
                    <label for="importFileInput"
                        class="flex flex-col items-center justify-center w-full h-28 sm:h-32 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-secondary hover:bg-secondary/5 transition-all group">
                        <div class="flex flex-col items-center gap-2 text-slate-400 group-hover:text-secondary transition-colors px-4 text-center">
                            @if ($importFile)
                            <i class="fas fa-file-excel text-2xl text-secondary"></i>
                            <span class="text-xs font-semibold text-secondary truncate w-full">{{ $importFile->getClientOriginalName() }}</span>
                            <span class="text-[10px] text-slate-400">{{ number_format($importFile->getSize() / 1024, 1) }} KB — klik untuk ganti</span>
                            @else
                            <i class="fas fa-cloud-upload-alt text-2xl"></i>
                            <span class="text-xs font-semibold">Klik untuk pilih file</span>
                            <span class="text-[10px]">.xlsx atau .xls, maks 5MB</span>
                            @endif
                        </div>
                        <input id="importFileInput" type="file" wire:model="importFile" accept=".xlsx,.xls" class="hidden">
                    </label>
                    @error('importFile')
                    <p class="text-xs text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                @endif

                {{-- ============================================================ --}}
                {{-- FUZZY REVIEW PANEL - RESPONSIVE GRID --}}
                {{-- ============================================================ --}}
                @if ($showFuzzyReview)
                <div class="space-y-3">
                    {{-- Header info --}}
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="text-amber-500 mt-0.5 shrink-0"><i class="fas fa-magic text-lg"></i></div>
                        <div>
                            <p class="text-sm font-bold text-amber-800">AI Auto-Correct menemukan {{ count($fuzzyReviewItems) }} baris dengan wilayah yang perlu dikoreksi</p>
                            <p class="text-xs text-amber-600 mt-0.5">Tinjau setiap saran koreksi di bawah. Setujui atau tolak sebelum melanjutkan import.</p>
                        </div>
                    </div>

                    {{-- Bulk actions --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs text-slate-500 font-medium">
                            {{ collect($fuzzyReviewItems)->where('decision', null)->count() }} belum diputuskan
                        </span>
                        <div class="flex gap-2">
                            <button wire:click="approveAllFuzzyCorrections"
                                class="text-[10px] sm:text-xs font-bold text-green-600 hover:text-green-800 border border-green-200 hover:border-green-400 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <i class="fas fa-check-double text-[9px]"></i> <span class="hidden sm:inline">Setujui Semua</span><span class="sm:hidden">Setuju Semua</span>
                            </button>
                            <button wire:click="rejectAllFuzzyCorrections"
                                class="text-[10px] sm:text-xs font-bold text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <i class="fas fa-times text-[9px]"></i> <span class="hidden sm:inline">Tolak Semua</span><span class="sm:hidden">Tolak Semua</span>
                            </button>
                        </div>
                    </div>

                    {{-- Review cards --}}
                    <div class="space-y-3 max-h-[40vh] sm:max-h-[380px] overflow-y-auto pr-1">
                        @foreach ($fuzzyReviewItems as $idx => $item)
                        <div class="border rounded-xl overflow-hidden transition-all
                            @if($item['decision'] === 'approve') border-green-300 bg-green-50/50
                            @elseif($item['decision'] === 'reject') border-red-200 bg-red-50/30 opacity-60
                            @else border-amber-200 bg-white
                            @endif">

                            {{-- Card header --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-2.5 border-b
                                @if($item['decision'] === 'approve') border-green-200 bg-green-100/60
                                @elseif($item['decision'] === 'reject') border-red-100 bg-red-50
                                @else border-amber-100 bg-amber-50/60
                                @endif">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black text-slate-600 bg-white border border-slate-200 rounded-md px-2 py-0.5">Baris {{ $item['row'] }}</span>
                                    <span class="text-xs font-semibold text-slate-700 truncate max-w-[150px] sm:max-w-none">{{ $item['rowData']['nama'] ?? '—' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if($item['decision'] === null)
                                    <button wire:click="approveFuzzyCorrection({{ $idx }})"
                                        class="text-[10px] font-bold text-white bg-green-500 hover:bg-green-600 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                        <i class="fas fa-check text-[8px]"></i> Setuju
                                    </button>
                                    <button wire:click="rejectFuzzyCorrection({{ $idx }})"
                                        class="text-[10px] font-bold text-white bg-red-400 hover:bg-red-500 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                        <i class="fas fa-times text-[8px]"></i> Tolak
                                    </button>
                                    @elseif($item['decision'] === 'approve')
                                    <span class="text-[10px] font-bold text-green-700 flex items-center gap-1"><i class="fas fa-check-circle"></i> Disetujui</span>
                                    <button wire:click="rejectFuzzyCorrection({{ $idx }})" class="text-[10px] text-slate-400 hover:text-red-500 transition ml-1"><i class="fas fa-undo"></i></button>
                                    @else
                                    <span class="text-[10px] font-bold text-red-500 flex items-center gap-1"><i class="fas fa-times-circle"></i> Ditolak</span>
                                    <button wire:click="approveFuzzyCorrection({{ $idx }})" class="text-[10px] text-slate-400 hover:text-green-500 transition ml-1"><i class="fas fa-undo"></i></button>
                                    @endif
                                </div>
                            </div>

                            {{-- Comparison table - Responsive grid --}}
                            <div class="p-3">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 text-[9px] sm:text-[10px]">
                                    @foreach (['provinsi', 'kabupaten', 'kecamatan', 'kelurahan'] as $field)
                                    <div class="space-y-1">
                                        <div class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">{{ ucfirst($field) }}</div>
                                        {{-- Original --}}
                                        <div class="flex items-center gap-1">
                                            <span class="text-slate-400 shrink-0"><i class="fas fa-file-excel text-[8px]"></i></span>
                                            <span class="@if(in_array($field, $item['changed'])) text-red-500 line-through @else text-slate-600 @endif font-mono truncate" title="{{ $item['original'][$field] }}">
                                                {{ \Str::limit($item['original'][$field] ?: '—', 12) }}
                                            </span>
                                        </div>
                                        {{-- Suggestion --}}
                                        @if(in_array($field, $item['changed']))
                                        <div class="flex items-center gap-1">
                                            <span class="text-green-500 shrink-0"><i class="fas fa-magic text-[8px]"></i></span>
                                            <span class="text-green-700 font-semibold font-mono truncate" title="{{ $item['suggestion'][$field] }}">
                                                {{ \Str::limit($item['suggestion'][$field], 12) }}
                                            </span>
                                        </div>
                                        @if(isset($item['scores'][$field]))
                                        <div>
                                            <span class="inline-flex items-center gap-0.5 text-[8px] font-bold px-1.5 py-0.5 rounded-full
                                                @if($item['scores'][$field] >= 85) bg-green-100 text-green-700
                                                @elseif($item['scores'][$field] >= 70) bg-yellow-100 text-yellow-700
                                                @else bg-orange-100 text-orange-700
                                                @endif">
                                                <i class="fas fa-percentage text-[6px]"></i> {{ $item['scores'][$field] }}%
                                            </span>
                                        </div>
                                        @endif
                                        @else
                                        <div class="flex items-center gap-1">
                                            <span class="text-slate-300 shrink-0"><i class="fas fa-check text-[8px]"></i></span>
                                            <span class="text-slate-400 italic text-[9px]">sama</span>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- LOG PANEL --}}
                @if ($importing || $importDone)
                <div class="space-y-2">
                    {{-- Summary bar --}}
                    @if ($importDone && $importTotal > 0)
                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="bg-slate-50 rounded-xl p-2 sm:p-3 text-center border border-slate-100">
                            <div class="text-base sm:text-lg font-black text-slate-700">{{ $importTotal }}</div>
                            <div class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</div>
                        </div>
                        <div class="bg-green-50 rounded-xl p-2 sm:p-3 text-center border border-green-100">
                            <div class="text-base sm:text-lg font-black text-green-600">{{ $importSuccess }}</div>
                            <div class="text-[9px] sm:text-[10px] font-bold text-green-400 uppercase tracking-wider">Berhasil</div>
                        </div>
                        <div class="bg-red-50 rounded-xl p-2 sm:p-3 text-center border border-red-100">
                            <div class="text-base sm:text-lg font-black text-red-500">{{ $importSkipped }}</div>
                            <div class="text-[9px] sm:text-[10px] font-bold text-red-400 uppercase tracking-wider">Dilewati</div>
                        </div>
                    </div>
                    @endif

                    {{-- Log terminal --}}
                    <div class="bg-slate-900 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2 bg-slate-800 border-b border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                            </div>
                            <span class="text-[9px] sm:text-[10px] text-slate-400 font-mono">import.log</span>
                            <div class="w-8"></div>
                        </div>
                        <div id="importLogBox" class="p-3 sm:p-4 h-40 sm:h-56 overflow-y-auto font-mono text-[9px] sm:text-[11px] space-y-1"
                            x-data x-on:import-finished.window="$el.scrollTop = $el.scrollHeight">
                            @if ($importing)
                            <div class="flex items-center gap-2 text-yellow-400">
                                <svg class="animate-spin h-3 w-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memproses file...</span>
                            </div>
                            @endif
                            @forelse ($importLogs as $log)
                            @if ($log['type'] === 'success')
                            <div class="text-green-400"><span class="text-slate-500 select-none">›</span><span class="text-green-500">✓</span> {{ $log['msg'] }}</div>
                            @elseif ($log['type'] === 'error')
                            <div class="text-red-400"><span class="text-slate-500 select-none">›</span><span class="text-red-500">✗</span> {{ $log['msg'] }}</div>
                            @else
                            <div class="text-blue-400 border-t border-slate-700 pt-1 mt-1"><span class="text-slate-500 select-none">›</span><span class="text-blue-400">ℹ</span> {{ $log['msg'] }}</div>
                            @endif
                            @empty
                            @if (!$importing)<div class="text-slate-500 italic">Log akan muncul di sini...</div>@endif
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                @if ($importDone)
                <button wire:click="resetImport" class="text-xs font-bold text-slate-500 hover:text-slate-700 flex items-center gap-1 transition">
                    <i class="fas fa-redo text-[10px]"></i> Import Lagi
                </button>
                <button @click="open = false" class="w-full sm:w-auto bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-blue-900 transition text-center">
                    Tutup
                </button>
                @elseif ($showFuzzyReview)
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i class="fas fa-info-circle text-amber-400"></i>
                    <span class="truncate">{{ collect($fuzzyReviewItems)->where('decision', 'approve')->count() }} disetujui, {{ collect($fuzzyReviewItems)->where('decision', 'reject')->count() }} ditolak</span>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button wire:click="resetImport" class="flex-1 sm:flex-none text-sm font-bold text-slate-500 hover:text-slate-700 transition px-4 py-2">
                        Batal
                    </button>
                    <button wire:click="commitImport" @if(!$this->allFuzzyDecided()) disabled @endif
                        class="flex-1 sm:flex-none bg-secondary text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700 transition flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fas fa-check"></i> Lanjutkan
                    </button>
                </div>
                @else
                <button @click="open = false" class="w-full sm:w-auto text-sm font-bold text-slate-500 hover:text-slate-700 transition px-4 py-2 text-center">
                    Batal
                </button>
                <button wire:click="runImport" wire:loading.attr="disabled" @if(!$importFile) disabled @endif
                    class="w-full sm:w-auto bg-secondary text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="runImport"><i class="fas fa-play"></i> Mulai Import</span>
                    <span wire:loading wire:target="runImport"><i class="fas fa-spinner fa-spin"></i> Menganalisis...</span>
                </button>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- CSS Animation for modal slide-up on mobile --}}
<style>
    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 1023px) {
        .animate-slide-up {
            animation: slide-up 0.25s ease-out;
        }
    }
</style>