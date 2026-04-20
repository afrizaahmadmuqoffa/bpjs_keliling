@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-6 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="mx-auto">
            <img src="{{ asset('logo_bpjs.png') }}" alt="Logo" class="h-[150px] w-[150px] mx-auto object-contain">
        </div>
        <h2 class="mt-4 text-3xl font-extrabold text-slate-800 tracking-tight">
            Selamat Datang
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Silakan login ke akun <span class="text-primary font-semibold">BPJS Keliling</span> Anda
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-2xl shadow-slate-200/60 rounded-3xl border border-slate-100 sm:px-10">

            @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p> {{-- tampil semua error --}}
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="/login" class="space-y-6">
                @csrf

                <div>
                    <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                        Nomor Induk Kependudukan (NIK)
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <input id="nik" name="nik" type="text" value="{{ old('nik') }}" required minlength="16" maxlength="16" inputmode="numeric" pattern="[0-9]*"
                            class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all text-sm"
                            placeholder="Masukkan 16 digit NIK">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                        Kata Sandi
                    </label>
                    <div class="relative group">
                        <!-- Icon kiri -->
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="fas fa-lock"></i>
                        </div>

                        <!-- Input -->
                        <input id="password" name="password" type="password" required
                            class="block w-full pl-11 pr-11 py-3 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all text-sm"
                            placeholder="••••••••">

                        <!-- Icon mata -->
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary transition-colors">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-primary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98]">
                        <i class="fas fa-sign-in-alt mt-0.5 mr-2"></i> Masuk ke Dashboard
                    </button>
                </div>
            </form>

            <!-- Lupa Password dipindah ke bawah -->
            <div class="mt-4 text-right">
                <a href="#" class="text-xs font-semibold text-secondary hover:underline">
                    Lupa Password?
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-center text-sm text-slate-500">
                    Belum punya akun?
                    <a href="#" class="font-bold text-secondary hover:text-green-700 transition-colors">Hubungi Admin</a>
                </p>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; 2026 BPJS Keliling. <br>
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
</script>
@endsection