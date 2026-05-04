<div class="space-y-8 px-4 py-14 lg:px-10 lg:py-10">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Data Peserta</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui peserta BPJS Keliling.</p>
        </div>
    </div>

    {{-- LOCKED WARNING --}}
    @if ($isLocked)
    <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl mb-4 shadow-sm">
        <i class="fas fa-lock text-yellow-500 text-lg shrink-0"></i>
        <span class="text-sm font-semibold">Data sudah berstatus <strong>selesai</strong> dan tidak dapat diubah.</span>
    </div>
    @endif

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
        <i class="fas fa-check-circle text-green-500 text-lg shrink-0"></i>
        <span class="text-sm font-semibold">{{ $successMessage }}</span>
        <button wire:click="dismissSuccess" class="ml-auto text-green-400 hover:text-green-600 transition shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    {{-- ERROR MESSAGES --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">
        @foreach ($errors->all() as $error)
        <div class="flex items-start gap-2">
            <i class="fas fa-exclamation-circle text-red-400 mt-0.5 shrink-0"></i> {{ $error }}
        </div>
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
                        <input wire:model="nama" type="text"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            @if($isLocked) disabled @endif
                            class="block w-full pl-11 pr-4 py-3 bg-white border @error('nama') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all @if($isLocked) opacity-60 cursor-not-allowed bg-slate-50 @endif">
                    </div>
                    @error('nama') <p class="text-xs text-red-500 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- NIK (readonly) --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">NIK</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-id-card text-sm"></i>
                        </div>
                        <input wire:model="nik" type="text" maxlength="16" inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            @if($isLocked) disabled @endif
                            class="block w-full pl-11 pr-4 py-3 bg-white border @error('nik') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all @if($isLocked) opacity-60 cursor-not-allowed bg-slate-50 @endif">
                    </div>
                </div>

                {{-- NO HP --}}
                <div class="space-y-2">
                    <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Nomor WhatsApp/HP</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </div>
                        <input wire:model="no_hp" type="text" placeholder="0812xxxx" inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            @if($isLocked) disabled @endif
                            class="block w-full pl-11 pr-4 py-3 bg-white border @error('no_hp') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all @if($isLocked) opacity-60 cursor-not-allowed bg-slate-50 @endif">
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
                        <select wire:model.live="provinsi" @if($isLocked) disabled @endif
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('provinsi') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none @if($isLocked) opacity-60 cursor-not-allowed @endif">
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
                        <select wire:model.live="kabupaten" @if($isLocked || empty($kabupatenList)) disabled @endif
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('kabupaten') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none @if($isLocked) opacity-60 cursor-not-allowed @endif">
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
                        <select wire:model.live="kecamatan" @if($isLocked || empty($kecamatanList)) disabled @endif
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('kecamatan') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none @if($isLocked) opacity-60 cursor-not-allowed @endif">
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
                        <select wire:model="kelurahan" @if($isLocked || empty($kelurahanList)) disabled @endif
                            class="block w-full pl-11 pr-10 py-3 bg-slate-50 border @error('kelurahan') border-red-400 @else border-slate-200 @enderror rounded-xl text-sm capitalize appearance-none @if($isLocked) opacity-60 cursor-not-allowed @endif">
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
                    class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-200 rounded-xl transition-all text-center">
                    Batal
                </button>
                @if (!$isLocked)
                <button wire:click="save" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                    class="flex-1 sm:flex-none bg-primary hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="save">
                        <i class="fas fa-save"></i> <span class="hidden sm:inline">Simpan Perubahan</span><span class="sm:hidden">Simpan</span>
                    </span>
                    <span wire:loading wire:target="save">
                        <i class="fas fa-spinner fa-spin"></i> <span class="hidden sm:inline">Menyimpan...</span><span class="sm:hidden">Proses...</span>
                    </span>
                </button>
                @else
                <button disabled class="flex-1 sm:flex-none bg-slate-300 text-slate-500 px-6 py-2.5 rounded-xl text-sm font-bold cursor-not-allowed flex items-center justify-center gap-2">
                    <i class="fas fa-lock"></i> <span class="hidden sm:inline">Data Terkunci</span><span class="sm:hidden">Terkunci</span>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>