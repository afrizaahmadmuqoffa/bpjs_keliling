<div class="w-full max-w-md px-4">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary to-blue-700 p-6 text-center">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('logo_bpjs.png') }}" class="h-16 w-16 object-contain">
            </div>
            <h2 class="text-xl font-bold text-white">Reset Password</h2>
            <p class="text-blue-100 text-sm mt-1">BPJS Keliling</p>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4">

            {{-- Success Message --}}
            @if (session()->has('success'))
            <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-400"></i>
                <div class="flex-1">
                    @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Step Indicator --}}
            <div class="flex items-center justify-center gap-2 mb-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $step >= 1 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-400' }}">
                        1
                    </div>
                    <span class="text-xs font-medium {{ $step >= 1 ? 'text-slate-700' : 'text-slate-400' }}">Email</span>
                </div>
                <div class="w-8 h-0.5 {{ $step >= 2 ? 'bg-primary' : 'bg-slate-200' }}"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $step >= 2 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-400' }}">
                        2
                    </div>
                    <span class="text-xs font-medium {{ $step >= 2 ? 'text-slate-700' : 'text-slate-400' }}">OTP</span>
                </div>
                <div class="w-8 h-0.5 {{ $step >= 3 ? 'bg-primary' : 'bg-slate-200' }}"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $step >= 3 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-400' }}">
                        3
                    </div>
                    <span class="text-xs font-medium {{ $step >= 3 ? 'text-slate-700' : 'text-slate-400' }}">Password</span>
                </div>
            </div>

            {{-- STEP 1: Email --}}
            @if ($step === 1)
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input
                            wire:model="email"
                            type="email"
                            placeholder="email@example.com"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                </div>

                <button
                    wire:click="sendOtp"
                    wire:loading.attr="disabled"
                    class="w-full bg-primary hover:bg-blue-900 text-white py-3 rounded-xl text-sm font-bold shadow-lg transition-all active:scale-95 disabled:opacity-70">
                    <span wire:loading.remove wire:target="sendOtp">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Kode OTP
                    </span>
                    <span wire:loading wire:target="sendOtp">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...
                    </span>
                </button>
            </div>
            @endif

            {{-- STEP 2: OTP --}}
            @if ($step === 2)
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                    <i class="fas fa-envelope-open-text text-blue-500 text-2xl mb-2"></i>
                    <p class="text-sm text-blue-700 font-medium">Kode OTP telah dikirim ke:</p>
                    <p class="text-sm font-bold text-blue-900 mt-1">{{ $email }}</p>
                    <p class="text-xs text-blue-600 mt-2">Periksa inbox atau folder spam Anda</p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Kode OTP (6 Digit)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-key"></i>
                        </div>
                        <input
                            wire:model="otp"
                            type="text"
                            maxlength="6"
                            inputmode="numeric"
                            placeholder="123456"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                </div>

                <button
                    wire:click="verifyOtp"
                    wire:loading.attr="disabled"
                    class="w-full bg-primary hover:bg-blue-900 text-white py-3 rounded-xl text-sm font-bold shadow-lg transition-all active:scale-95 disabled:opacity-70">
                    <span wire:loading.remove wire:target="verifyOtp">
                        <i class="fas fa-check-circle mr-2"></i> Verifikasi OTP
                    </span>
                    <span wire:loading wire:target="verifyOtp">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Memverifikasi...
                    </span>
                </button>
            </div>
            @endif

            {{-- STEP 3: New Password --}}
            @if ($step === 3)
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                    <p class="text-sm text-green-700 font-medium">OTP Berhasil Diverifikasi</p>
                    <p class="text-xs text-green-600 mt-1">Silakan masukkan password baru Anda</p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input
                            wire:model="password"
                            type="password"
                            placeholder="Minimal 6 karakter"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input
                            wire:model="password_confirmation"
                            type="password"
                            placeholder="Ketik ulang password"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
                    </div>
                </div>

                <button
                    wire:click="resetPassword"
                    wire:loading.attr="disabled"
                    class="w-full bg-secondary hover:bg-green-700 text-white py-3 rounded-xl text-sm font-bold shadow-lg transition-all active:scale-95 disabled:opacity-70">
                    <span wire:loading.remove wire:target="resetPassword">
                        <i class="fas fa-save mr-2"></i> Reset Password
                    </span>
                    <span wire:loading wire:target="resetPassword">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...
                    </span>
                </button>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-center">
            <a href="{{ route('login') }}" class="text-sm text-primary hover:text-blue-900 font-medium transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>

    </div>
</div>
