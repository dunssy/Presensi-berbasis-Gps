<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PT Technodrives') — Presensi GPS & Logbook</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a', 950: '#172554'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .leaflet-pane { z-index: 10 !important; }
        .leaflet-top, .leaflet-bottom { z-index: 11 !important; }
    </style>
    @stack('styles')
</head>
<body x-data="{ mobileMenuOpen: false }" class="h-full font-sans text-slate-800 antialiased flex flex-col lg:flex-row overflow-hidden">

    <!-- Mobile Backdrop -->
    <div 
        x-show="mobileMenuOpen" 
        x-cloak 
        @click="mobileMenuOpen = false" 
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
    ></div>

    <!-- SIDEBAR -->
    <aside 
        :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 ease-in-out lg:static shrink-0"
    >
        <!-- Brand Header -->
        <div class="h-16 px-5 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-primary-600 text-white font-extrabold flex items-center justify-center text-lg shadow-sm shadow-primary-600/30">
                    TD
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 tracking-tight leading-tight">PT Technodrives</h1>
                    <p class="text-[11px] font-medium text-slate-500">Presensi GPS & Logbook</p>
                </div>
            </div>
            <button @click="mobileMenuOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                ✕
            </button>
        </div>

        <!-- Role Badge -->
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/70">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Panel Akses</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider 
                    @if(Auth::user()?->isAdmin()) bg-rose-100 text-rose-700
                    @elseif(Auth::user()?->isManager()) bg-indigo-100 text-indigo-700
                    @else bg-emerald-100 text-emerald-700 @endif">
                    {{ Auth::user()?->role ?? 'Guest' }}
                </span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1 text-sm">
            @if(Auth::user()?->isAdmin())
                {{-- ADMIN NAV --}}
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📊</span>
                    <span>Dashboard Admin</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.employees.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">👥</span>
                    <span>Data Karyawan</span>
                </a>
                <a href="{{ route('admin.projects.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.projects.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📁</span>
                    <span>Data Projek</span>
                </a>
                <a href="{{ route('admin.attendance-points.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.attendance-points.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📍</span>
                    <span>Titik Lokasi GPS</span>
                </a>
                <a href="{{ route('admin.assignments.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.assignments.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📋</span>
                    <span>Penugasan Tim</span>
                </a>
                
                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Laporan
                </div>
                <a href="{{ route('admin.reports.attendance') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.reports.attendance') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📑</span>
                    <span>Laporan Presensi</span>
                </a>
                <a href="{{ route('admin.reports.logbook') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.reports.logbook') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📘</span>
                    <span>Laporan Logbook</span>
                </a>

            @elseif(Auth::user()?->isManager())
                {{-- MANAGER NAV --}}
                <a href="{{ route('manager.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('manager.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📊</span>
                    <span>Dashboard PM</span>
                </a>
                <a href="{{ route('manager.projects.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('manager.projects.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📁</span>
                    <span>Monitoring Projek</span>
                </a>
                <a href="{{ route('manager.logbooks.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('manager.logbooks.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">✓</span>
                    <span>Review Logbook</span>
                </a>

            @else
                {{-- KARYAWAN NAV --}}
                <a href="{{ route('karyawan.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('karyawan.dashboard') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📍</span>
                    <span>Presensi GPS</span>
                </a>
                <a href="{{ route('karyawan.logbooks.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('karyawan.logbooks.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base">📝</span>
                    <span>Logbook Kegiatan</span>
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()?->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()?->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shrink-0 z-20">
            <div class="flex items-center gap-3">
                <button @click="mobileMenuOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h2 class="text-base font-bold text-slate-900 leading-tight">@yield('page_title', 'Dashboard')</h2>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-medium text-slate-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- WORKSPACE CONTENT -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-3">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 text-sm text-rose-800 flex items-center gap-3">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-rose-100 text-rose-600 font-bold">!</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 text-sm text-rose-800 space-y-1">
                    <p class="font-bold">Terjadi kesalahan validasi data:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

