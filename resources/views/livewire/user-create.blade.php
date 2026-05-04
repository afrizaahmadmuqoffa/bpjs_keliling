<div class="space-y-8 px-4 py-14 lg:px-10 lg:py-10">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 lg:mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Buat Akun Petugas</h2>
            <p class="text-sm text-slate-500 mt-1">Daftarkan akun baru untuk akses sistem BPJS Keliling.</p>
        </div>
    </div>

    {{-- SUCCESS ALERT --}}
    @if ($showSuccess)
    <div
        x-data="{ visible: true }"
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
        <div class="flex items-start gap-2"><i class="fas fa-exclamation-circle text-red-400 mt-0.5 shrink-0"></i> {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            {{-- SECTION: IDENTITAS --}}
            <div class="p-5 lg:p-8 border-b border-slate-50">
                <div class="flex items-center gap-3 mb-5 lg:mb-8">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center shadow-sm shrink-0">
                        <i class="fas fa-id-badge text-sm lg:text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-700">Informasi Identitas</h3>
                        <p class="text-[10px] lg:text-xs text-slate-400">Data resmi sesuai KTP petugas.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                    {{-- NAMA --}}
                    <div class="space-y-2">
                        <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <input wire:model="nama" type="text" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" placeholder="Nama Petugas"
                                class="block w-full pl-11 pr-4 py-3 bg-slate-50 border @error('nama') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary focus:bg-white transition-all">
                        </div>
                        @error('nama') <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- NIK --}}
                    <div class="space-y-2">
                        <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Nomor Induk Kependudukan (NIK)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                <i class="fas fa-id-card text-sm"></i>
                            </div>
                            <input wire:model="nik" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" inputmode="numeric" maxlength="16" placeholder="16 Digit NIK"
                                class="block w-full pl-11 pr-4 py-3 bg-slate-50 border @error('nik') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary focus:bg-white transition-all">
                        </div>
                        @error('nik') <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION: KREDENSIAL --}}
            <div class="p-5 lg:p-8 bg-slate-50/30">
                <div class="flex items-center gap-3 mb-5 lg:mb-8">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-secondary/10 text-secondary rounded-xl flex items-center justify-center shadow-sm shrink-0">
                        <i class="fas fa-key text-sm lg:text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-700">Kredensial Login & Akses</h3>
                        <p class="text-[10px] lg:text-xs text-slate-400">Pengaturan email, role, dan keamanan akun.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                    {{-- EMAIL --}}
                    <div class="space-y-2">
                        <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Email Instansi</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-secondary transition-colors">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input wire:model="email" type="email" placeholder="Masukkan Email Aktif"
                                class="block w-full pl-11 pr-4 py-3 bg-white border @error('email') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary/10 focus:border-secondary transition-all">
                        </div>
                        @error('email') <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- NO HP --}}
                    <div class="space-y-2">
                        <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Nomor HP</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-secondary transition-colors">
                                <i class="fas fa-phone text-sm"></i>
                            </div>
                            <input wire:model="no_hp" type="text" placeholder="0812xxxx" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary/10 focus:border-secondary transition-all">
                        </div>
                    </div>

                    {{-- ROLE --}}
                    <div class="space-y-2">
                        <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Level Akses (Role)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-secondary transition-colors">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </div>
                            <select wire:model="role"
                                class="block w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-secondary/10 focus:border-secondary cursor-pointer transition-all">
                                @if(auth()->user()->role === 'super_admin')
                                <option value="super_admin">Super Admin</option>
                                @endif
                                <option value="admin">Admin</option>
                                <option value="pic">PIC</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-300">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- PASSWORD --}}
                    <div class="space-y-2">
                        <label class="text-[10px] lg:text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Kata Sandi Akun</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-secondary transition-colors">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input wire:model="password" type="{{ $showPassword ? 'text' : 'password' }}" placeholder="Minimal 6 karakter"
                                class="block w-full pl-11 pr-11 py-3 bg-white border @error('password') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-secondary/10 focus:border-secondary transition-all">
                            <button type="button" wire:click="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-secondary transition">
                                <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }} text-sm"></i>
                            </button>
                        </div>
                        @error('password') <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            {{-- FOOTER ACTIONS --}}
            <div class="p-5 lg:p-6 bg-slate-100/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Bagian Info (Kiri) -->
                <div class="flex items-start gap-3 text-slate-400 w-full sm:w-auto">
                    <i class="fas fa-shield-check text-secondary shrink-0 mt-0.5"></i>
                    <p class="text-[10px] lg:text-[11px] leading-tight text-left">
                        <span class="flex items-center gap-1 mb-0.5">
                            <i class="fas fa-info-circle"></i>
                            <span class="font-semibold uppercase tracking-wider">Penting:</span>
                        </span>
                        Data ini akan digunakan sebagai kredensial login resmi.<br class="hidden sm:block">
                        Pastikan email aktif.
                    </p>
                </div>

                <!-- Bagian Tombol (Kanan) -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('users.index') }}"
                        class="flex-1 sm:flex-none px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-all text-center">
                        Batal
                    </a>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="flex-1 sm:flex-none bg-primary hover:bg-blue-900 text-white px-6 lg:px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span class="hidden sm:inline">Simpan Data User</span>
                            <span class="sm:hidden">Simpan</span>
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span class="hidden sm:inline">Menyimpan...</span>
                            <span class="sm:hidden">Proses...</span>
                        </span>
                    </button>
                </div>
            </div>
    </form>

</div>