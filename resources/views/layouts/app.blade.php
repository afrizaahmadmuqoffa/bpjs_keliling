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

        /* Mobile Sidebar Toggle */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        #sidebar.mobile-hidden {
            transform: translateX(-100%);
        }

        @media (min-width: 1024px) {
            #sidebar {
                transform: none !important;
            }
        }

        /* Smooth fade-in */
        body {
            opacity: 0;
            transition: opacity 0.3s;
        }

        body.loaded {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-lightBg font-sans text-slate-700" onload="document.body.classList.add('loaded')">

    {{-- MOBILE SIDEBAR TOGGLE --}}
    @auth
    <button id="mobileToggle" class="lg:hidden fixed top-4 left-4 z-30 bg-primary text-white p-3 rounded-lg shadow-lg">
        <i class="fas fa-bars"></i>
    </button>
    @endauth

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        @auth
        <aside id="sidebar" class="mobile-hidden fixed top-0 left-0 h-screen w-64 bg-primary text-white flex flex-col shadow-xl z-20 overflow-y-auto">

            <!-- LOGO -->
            <div class="p-4 lg:p-6 flex items-center justify-between lg:justify-start gap-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo_bpjs.png') }}" class="h-10 w-10 lg:h-[50px] lg:w-[50px] object-contain">
                    <span class="font-bold text-base lg:text-lg sm:inline">BPJS Keliling</span>
                </div>
            </div>

            <!-- MENU -->
            <nav class="flex-1 px-3 lg:px-4 py-4 space-y-1 lg:space-y-2 overflow-y-auto">

                {{-- DASHBOARD — admin & super_admin --}}
                @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                <a href="{{ route('dashboard') }}"
                    class="sidebar-item {{ request()->routeIs('*.dashboard') ? 'sidebar-active' : '' }} flex items-center gap-3 px-4 py-3 text-slate-300 hover:text-white rounded-lg">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                @endif

                {{-- DATA PESERTA — semua role --}}
                <a href="{{ route('participants.index') }}"
                    class="sidebar-item {{ request()->routeIs('participants.*') && !request()->routeIs('participants.create') ? 'sidebar-active' : '' }} flex items-center gap-3 px-4 py-3 text-slate-300 hover:text-white rounded-lg">
                    <i class="fas fa-id-card w-5"></i>
                    <span class="text-sm">Data Peserta</span>
                </a>

                {{-- MANAJEMEN PENGGUNA — super_admin only --}}
                @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('users.index') }}"
                    class="sidebar-item {{ request()->routeIs('users.*') ? 'sidebar-active' : '' }} flex items-center gap-3 px-4 py-3 text-slate-300 hover:text-white rounded-lg">
                    <i class="fas fa-user-shield w-5"></i>
                    <span class="text-sm">Manajemen Pengguna</span>
                </a>
                @endif

            </nav>

            {{-- Overlay for mobile --}}
            <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black/50 z-[-1] hidden"></div>

            {{-- USER PROFILE + LOGOUT --}}
            <div class="p-4 border-t border-white/10">

                <!-- Profile -->
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-secondary text-white rounded-full flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">
                            {{ auth()->user()->nama }}
                        </p>
                        <p class="text-xs text-white/70 uppercase">
                            {{ match(auth()->user()->role) {
                    'super_admin' => 'Super Admin',
                    'admin'       => 'Admin',
                    'pic'         => 'PIC',
                    default       => auth()->user()->role,
                } }}
                        </p>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>

            </div>

        </aside>
        @endauth

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0 lg:ml-64">

            {{-- CONTENT --}}
            <main class="p-3 lg:p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    {{-- Mobile Sidebar Script --}}
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('mobileToggle');
            const close = document.getElementById('sidebarClose');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar(show) {
                if (show) {
                    // Sidebar muncul
                    sidebar.classList.remove('mobile-hidden');
                    overlay.classList.remove('hidden');

                    // ❗ UI control
                    toggle.classList.add('hidden'); // hide hamburger
                    close.classList.remove('hidden'); // show close

                    document.body.style.overflow = 'hidden';
                } else {
                    // Sidebar hilang
                    sidebar.classList.add('mobile-hidden');
                    overlay.classList.add('hidden');

                    // ❗ UI control
                    toggle.classList.remove('hidden'); // show hamburger
                    close.classList.add('hidden'); // hide close

                    document.body.style.overflow = '';
                }
            }

            toggle?.addEventListener('click', () => toggleSidebar(true));
            close?.addEventListener('click', () => toggleSidebar(false));
            overlay?.addEventListener('click', () => toggleSidebar(false));

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') toggleSidebar(false);
            });
        });
    </script>
    @endauth

    @livewireScripts
</body>

</html>