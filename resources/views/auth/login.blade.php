@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-4 sm:py-6 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-50 to-slate-100">
    
    <!-- Header Section -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="mx-auto">
            <img src="{{ asset('logo_bpjs.png') }}" 
                 alt="Logo BPJS Keliling" 
                 class="h-24 w-24 sm:h-32 sm:w-32 lg:h-36 lg:w-36 mx-auto object-contain drop-shadow-sm">
        </div>
        <h2 class="mt-3 sm:mt-4 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-800 tracking-tight">
            Selamat Datang
        </h2>
        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-slate-500 px-2">
            Silakan login ke akun <span class="text-primary font-semibold">BPJS Keliling</span> Anda
        </p>
    </div>

    <!-- Form Card -->
    <div class="mt-4 sm:mt-6 sm:mx-auto sm:w-full sm:max-w-md w-full">
        <div class="bg-white py-6 sm:py-8 px-4 sm:px-6 lg:px-10 shadow-2xl shadow-slate-200/60 rounded-2xl sm:rounded-3xl border border-slate-100">

            {{-- Error Messages --}}
            @if ($errors->any())
            <div class="mb-4 sm:mb-6 bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded-r-lg shadow-sm">
                <div class="text-red-700 text-xs sm:text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="/login" class="space-y-4 sm:space-y-6">
                @csrf

                {{-- NIK Input --}}
                <div>
                    <label for="nik" class="block text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 sm:mb-2 ml-1">
                        Nomor Induk Kependudukan (NIK)
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-id-card text-sm sm:text-base"></i>
                        </div>
                        <input id="nik" name="nik" type="text" value="{{ old('nik') }}" required minlength="16" maxlength="16" inputmode="numeric" pattern="[0-9]*"
                            class="block w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3 border border-slate-200 rounded-lg sm:rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all text-sm"
                            placeholder="Masukkan 16 digit NIK">
                    </div>
                    <p class="mt-1 text-[10px] text-slate-400 ml-1">* 16 angka tanpa spasi</p>
                </div>

                {{-- Password Input --}}
                <div>
                    <label for="password" class="block text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 sm:mb-2 ml-1">
                        Kata Sandi
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-lock text-sm sm:text-base"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="block w-full pl-10 sm:pl-11 pr-10 sm:pr-11 py-2.5 sm:py-3 border border-slate-200 rounded-lg sm:rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all text-sm"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center text-slate-400 hover:text-primary transition-colors p-1">
                            <i id="eyeIcon" class="fas fa-eye text-sm sm:text-base"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 sm:py-3.5 px-4 border border-transparent rounded-lg sm:rounded-xl shadow-lg text-xs sm:text-sm font-bold text-white bg-primary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98]">
                        <i class="fas fa-sign-in-alt mt-0.5 mr-2 text-xs sm:text-sm"></i> Masuk ke Dashboard
                    </button>
                </div>

                {{-- Forgot Password Link --}}
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-xs sm:text-sm text-primary hover:text-blue-900 font-medium transition-colors">
                        <i class="fas fa-key mr-1"></i> Lupa Password?
                    </a>
                </div>
            </form>

            {{-- Help Link --}}
            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-slate-100">
                <p class="text-center text-[10px] sm:text-sm text-slate-500 px-2">
                    Belum punya akun atau memiliki kendala?
                    <a href="https://wa.me/6282137993903" class="font-bold text-secondary hover:text-green-700 transition-colors break-words">Hubungi Admin</a>
                </p>
            </div>
        </div>
        {{-- Footer --}}
        <p class="mt-4 sm:mt-6 text-center text-[10px] sm:text-xs text-slate-400 px-4">
            &copy; {{ date('Y') }} BPJS Keliling.<br class="sm:hidden">
            <span class="hidden sm:inline"> </span>
            Sistem Informasi Pelayanan Terpadu.
        </p>
    </div>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById("password");
        const icon = document.getElementById("eyeIcon");

        if (password.type === "password") {
            password.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            password.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    // Auto-format NIK input (optional UX enhancement)
    document.getElementById('nik')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
    });
</script>
@endsection