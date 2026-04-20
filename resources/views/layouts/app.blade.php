<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} | BPJS Keliling</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#033e87',
                        secondary: '#01a850',
                        lightBg: '#f8fafc',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-active {
            background-color: #01a850 !important;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body class="bg-lightBg font-sans text-slate-700 opacity-0 transition-opacity duration-300"
    onload="document.body.classList.remove('opacity-0')">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        @auth
        <aside class="fixed top-0 left-0 h-screen w-64 bg-primary text-white flex flex-col shadow-xl z-20">

            <!-- LOGO -->
            <div class="p-6 flex items-center gap-3">
                <img src="{{ asset('logo_bpjs.png') }}" class="h-[50px] w-[50px] object-contain">
                <span class="font-bold text-lg">BPJS Keliling</span>
            </div>

            <!-- MENU -->
            <nav class="flex-1 px-4 py-4 space-y-2">

                {{-- DASHBOARD — admin & super_admin --}}
                @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                <a href="{{ route('dashboard') }}"
                    class="sidebar-item {{ request()->routeIs('*.dashboard') ? 'sidebar-active' : '' }} flex items-center gap-3 px-4 py-3 text-slate-300 hover:text-white">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                @endif

                {{-- DATA PESERTA — semua role --}}
                <a href="{{ route('participants.index') }}"
                    class="sidebar-item {{ request()->routeIs('participants.*') && !request()->routeIs('participants.create') ? 'sidebar-active' : '' }} flex items-center gap-3 px-4 py-3 text-slate-300 hover:text-white">
                    <i class="fas fa-id-card w-5"></i>
                    <span class="text-sm">Data Peserta</span>
                </a>

                {{-- MANAJEMEN PENGGUNA — super_admin only --}}
                @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('users.index') }}"
                    class="sidebar-item {{ request()->routeIs('users.*') ? 'sidebar-active' : '' }} flex items-center gap-3 px-4 py-3 text-slate-300 hover:text-white">
                    <i class="fas fa-user-shield w-5"></i>
                    <span class="text-sm">Manajemen Pengguna</span>
                </a>
                @endif

            </nav>

        </aside>
        @endauth

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0 {{ auth()->check() ? 'ml-64' : '' }}">

            {{-- NAVBAR --}}
            @auth
            <header class="h-16 bg-white border-b flex items-center justify-between px-8 sticky top-0 z-10">

                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-secondary font-semibold uppercase">
                            {{ auth()->user()->nama }}
                        </p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">
                            {{ match(auth()->user()->role) {
                                'super_admin' => 'Super Admin',
                                'admin'       => 'Admin',
                                'pic'         => 'PIC',
                                default       => auth()->user()->role,
                            } }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-2 px-4 py-2 text-red-500 hover:bg-red-100 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>

            </header>
            @endauth

            {{-- CONTENT --}}
            <main class="p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>
    @livewireScripts
</body>

</html>