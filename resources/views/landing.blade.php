<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Technodrives — Solusi Presensi GPS & Logbook Lapangan Terintegrasi</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body 
    x-data="{
        scrolled: false,
        mobileMenu: false,
        activeFeatureTab: 'gps',
        activeFaq: 1,
        // Live GPS Simulator State
        gpsSim: {
            mode: 'inside', // 'inside' or 'outside'
            distance: 18,
            radius: 100,
            statusText: 'Di dalam zona aman presensi',
            isCheckingIn: false,
            checkedIn: false,
            checkInTime: null
        },
        toggleGpsMode(mode) {
            this.gpsSim.mode = mode;
            if (mode === 'inside') {
                this.gpsSim.distance = 18;
                this.gpsSim.statusText = 'Di dalam zona aman presensi (Jarak: 18 meter)';
            } else {
                this.gpsSim.distance = 285;
                this.gpsSim.statusText = 'Di luar radius geofence (Jarak: 285 meter, Max: 100 meter)';
            }
        },
        simulateCheckIn() {
            if (this.gpsSim.mode !== 'inside') return;
            this.gpsSim.isCheckingIn = true;
            setTimeout(() => {
                this.gpsSim.isCheckingIn = false;
                this.gpsSim.checkedIn = true;
                const now = new Date();
                this.gpsSim.checkInTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            }, 800);
        },
        resetCheckIn() {
            this.gpsSim.checkedIn = false;
            this.gpsSim.checkInTime = null;
        }
    }" 
    @scroll.window="scrolled = (window.pageYOffset > 20)"
    class="bg-slate-900 text-slate-100 font-sans antialiased selection:bg-primary-500 selection:text-white"
>

    <!-- NAVIGATION HEADER -->
    <header 
        :class="scrolled ? 'bg-slate-900/90 backdrop-blur-md border-b border-slate-800 shadow-xl' : 'bg-transparent border-b border-transparent'"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo & Brand -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-primary-600 to-indigo-500 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-primary-500/25 group-hover:scale-105 transition-transform">
                        TD
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-base sm:text-lg font-extrabold text-white tracking-tight">PT Technodrives</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-primary-500/10 text-primary-400 border border-primary-500/20">GPS v2.0</span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium">GeoAttendance & Digital Logbook</p>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                    <a href="#fitur" class="hover:text-primary-400 transition-colors">Fitur Unggulan</a>
                    <a href="#simulator" class="hover:text-primary-400 transition-colors">Simulasi GPS</a>
                    <a href="#workflow" class="hover:text-primary-400 transition-colors">Alur Kerja</a>
                    <a href="#roles" class="hover:text-primary-400 transition-colors">Peran & Akses</a>
                    <a href="#faq" class="hover:text-primary-400 transition-colors">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        @php
                            $user = Auth::user();
                            $targetRoute = $user->isAdmin() ? route('admin.dashboard') : ($user->isManager() ? route('manager.dashboard') : route('karyawan.dashboard'));
                        @endphp
                        <a href="{{ $targetRoute }}" 
                           class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-primary-600/30 transition-all flex items-center gap-2">
                            <span>Buka Dashboard</span>
                            <span class="text-xs">→</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('login') }}" 
                           class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-primary-600/30 transition-all flex items-center gap-2">
                            <span>Akses Portal</span>
                            <span class="text-xs">→</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Toggle -->
                <button 
                    @click="mobileMenu = !mobileMenu" 
                    class="md:hidden p-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none"
                    aria-label="Toggle Menu"
                >
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div 
            x-show="mobileMenu" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden bg-slate-900/95 backdrop-blur-xl border-b border-slate-800 px-4 pt-3 pb-6 space-y-3"
        >
            <a @click="mobileMenu = false" href="#fitur" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Fitur Unggulan</a>
            <a @click="mobileMenu = false" href="#simulator" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Simulasi GPS</a>
            <a @click="mobileMenu = false" href="#workflow" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Alur Kerja</a>
            <a @click="mobileMenu = false" href="#roles" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Peran & Akses</a>
            <a @click="mobileMenu = false" href="#faq" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">FAQ</a>
            
            <div class="pt-3 border-t border-slate-800">
                <a href="{{ route('login') }}" class="block w-full text-center py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md">
                    Masuk ke Sistem
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative pt-32 pb-20 sm:pt-40 sm:pb-32 overflow-hidden">
        
        <!-- Ambient Background Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary-600/20 rounded-full blur-[140px] pointer-events-none -z-10"></div>
        <div class="absolute top-1/3 right-10 w-[400px] h-[400px] bg-indigo-600/15 rounded-full blur-[120px] pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Copywriting -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    
                    <!-- Pill Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/60 shadow-inner">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-semibold text-slate-300">Geofencing & Logbook Lapangan Terpadu</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight">
                        Presensi GPS Akurat. <br>
                        <span class="bg-gradient-to-r from-primary-400 via-sky-300 to-indigo-300 bg-clip-text text-transparent">
                            Logbook Real-Time.
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="text-sm sm:text-base text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Solusi terintegrasi untuk perusahaan multi-lokasi dan teknisi lapangan. Validasi kehadiran berbasis radius koordinat GPS otomatis tanpa fake GPS, dilengkapi pelaporan aktivitas harian dan persetujuan bertingkat oleh Project Manager.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('login') }}" 
                           class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-extrabold text-sm shadow-xl shadow-primary-600/30 transition-all flex items-center justify-center gap-2.5 hover:-translate-y-0.5">
                            <span>Mulai Presensi Sekarang</span>
                            <span class="text-sm">→</span>
                        </a>
                        <a href="#simulator" 
                           class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-750 border border-slate-700/80 text-slate-200 font-semibold text-sm transition-all flex items-center justify-center gap-2">
                            <span>🎮 Uji Simulasi GPS</span>
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="pt-6 border-t border-slate-800/80 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Formula Haversine Akurat</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Reverse Geocoding Alamat</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Audit Trail Terenkripsi</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Simulator Preview -->
                <div class="lg:col-span-5" id="simulator">
                    <div class="relative rounded-3xl bg-slate-800/90 border border-slate-700/70 p-6 shadow-2xl shadow-slate-950/60 backdrop-blur-xl">
                        
                        <!-- Card Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                            <div class="flex items-center gap-2.5">
                                <span class="h-3 w-3 rounded-full bg-rose-500/80 inline-block"></span>
                                <span class="h-3 w-3 rounded-full bg-amber-500/80 inline-block"></span>
                                <span class="h-3 w-3 rounded-full bg-emerald-500/80 inline-block"></span>
                                <span class="text-xs font-mono text-slate-400 pl-2">Radar Terminal GPS</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-primary-500/20 text-primary-300">
                                LIVE DEMO
                            </span>
                        </div>

                        <!-- Interactive Simulator Controls -->
                        <div class="mt-5 space-y-4 text-xs">
                            
                            <!-- Toggle Scenario Buttons -->
                            <div>
                                <label class="block font-bold text-slate-300 mb-2">Skenario Posisi Teknisi:</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button 
                                        type="button" 
                                        @click="toggleGpsMode('inside')"
                                        :class="gpsSim.mode === 'inside' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-400/50' : 'bg-slate-700 text-slate-300 hover:bg-slate-650 font-medium'"
                                        class="p-2.5 rounded-xl transition text-center flex items-center justify-center gap-1.5"
                                    >
                                        <span>📍 Di Lokasi (18m)</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="toggleGpsMode('outside')"
                                        :class="gpsSim.mode === 'outside' ? 'bg-rose-600 text-white font-bold ring-2 ring-rose-400/50' : 'bg-slate-700 text-slate-300 hover:bg-slate-650 font-medium'"
                                        class="p-2.5 rounded-xl transition text-center flex items-center justify-center gap-1.5"
                                    >
                                        <span>🚫 Di Luar Lokasi (285m)</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Mock Visual Map Graphic -->
                            <div class="relative h-44 rounded-2xl bg-slate-900 border border-slate-700/60 overflow-hidden flex items-center justify-center">
                                
                                <!-- Geofence Ring -->
                                <div class="absolute h-32 w-32 rounded-full border-2 border-dashed border-emerald-500/60 bg-emerald-500/10 flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-emerald-400/80">Radius 100m</span>
                                </div>

                                <!-- Center Target Point -->
                                <div class="absolute h-5 w-5 rounded-full bg-emerald-500 text-slate-900 flex items-center justify-center text-[10px] font-black shadow-lg shadow-emerald-500/50">
                                    🏢
                                </div>

                                <!-- User Pin Marker animated by scenario -->
                                <div 
                                    class="absolute transition-all duration-500 ease-out flex flex-col items-center"
                                    :style="gpsSim.mode === 'inside' ? 'top: 42%; left: 54%;' : 'top: 10%; left: 82%;'"
                                >
                                    <div class="h-6 w-6 rounded-full bg-primary-500 text-white flex items-center justify-center text-xs shadow-lg shadow-primary-500/50 animate-bounce">
                                        📍
                                    </div>
                                    <span class="text-[9px] font-bold bg-slate-800 text-white px-1.5 py-0.5 rounded shadow mt-0.5 whitespace-nowrap">
                                        Teknisi
                                    </span>
                                </div>
                            </div>

                            <!-- Live Geofence Feedback Badge -->
                            <div 
                                class="p-3 rounded-xl border flex items-center gap-2.5 transition-colors"
                                :class="gpsSim.mode === 'inside' ? 'bg-emerald-950/60 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/60 border-rose-500/40 text-rose-300'"
                            >
                                <span class="text-base" x-text="gpsSim.mode === 'inside' ? '✅' : '⚠️'"></span>
                                <div class="text-[11px] leading-tight flex-1">
                                    <p class="font-bold" x-text="gpsSim.mode === 'inside' ? 'Validasi Lolos (Aman)' : 'Validasi Ditolak (Di Luar Radius)'"></p>
                                    <p class="text-[10px] opacity-80" x-text="gpsSim.statusText"></p>
                                </div>
                            </div>

                            <!-- Action Simulator Button -->
                            <template x-if="!gpsSim.checkedIn">
                                <button 
                                    type="button"
                                    @click="simulateCheckIn()"
                                    :disabled="gpsSim.mode !== 'inside' || gpsSim.isCheckingIn"
                                    class="w-full py-3 rounded-xl font-bold text-xs transition shadow-lg flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
                                    :class="gpsSim.mode === 'inside' ? 'bg-primary-600 hover:bg-primary-500 text-white shadow-primary-600/30' : 'bg-slate-700 text-slate-400'"
                                >
                                    <span x-show="!gpsSim.isCheckingIn">📍 Kirim Check-In Presensi</span>
                                    <span x-show="gpsSim.isCheckingIn" x-cloak>Memverifikasi koordinat...</span>
                                </button>
                            </template>

                            <template x-if="gpsSim.checkedIn">
                                <div class="space-y-2">
                                    <div class="p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-center font-bold text-xs">
                                        🎉 Presensi Masuk Berhasil! Jam: <span x-text="gpsSim.checkInTime"></span>
                                    </div>
                                    <button 
                                        type="button"
                                        @click="resetCheckIn()"
                                        class="w-full py-2 rounded-lg bg-slate-700 hover:bg-slate-650 text-slate-300 text-[11px] font-semibold"
                                    >
                                        Ulangi Simulasi
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- METRICS SECTION -->
    <section class="border-y border-slate-800 bg-slate-950/60 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div class="space-y-1">
                    <p class="text-3xl sm:text-4xl font-black text-white">99.8%</p>
                    <p class="text-xs sm:text-sm font-medium text-slate-400">Akurasi Validasi Geofence</p>
                </div>
                <div class="space-y-1">
                    <p class="text-3xl sm:text-4xl font-black text-primary-400">&lt; 3 Detik</p>
                    <p class="text-xs sm:text-sm font-medium text-slate-400">Kecepatan Verifikasi GPS</p>
                </div>
                <div class="space-y-1">
                    <p class="text-3xl sm:text-4xl font-black text-white">100%</p>
                    <p class="text-xs sm:text-sm font-medium text-slate-400">Bebas Manipulasi / Anti-Cheat</p>
                </div>
                <div class="space-y-1">
                    <p class="text-3xl sm:text-4xl font-black text-indigo-400">Multi-Role</p>
                    <p class="text-xs sm:text-sm font-medium text-slate-400">Admin, PM, & Teknisi Lapangan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- INTERACTIVE FEATURES SHOWCASE (ALPINE.JS TABS) -->
    <section id="fitur" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary-500/10 text-primary-400 border border-primary-500/20 uppercase tracking-wider">
                Fitur Unggulan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Dirancang untuk Menjawab Tantangan Operasional Lapangan
            </h2>
            <p class="text-sm text-slate-400">
                Pilih modul di bawah ini untuk melihat kemampuan sistem yang membantu kelancaran proyek dan akurasi absensi tim Anda.
            </p>
        </div>

        <!-- Alpine Tab Switcher -->
        <div class="mt-12">
            <div class="flex items-center justify-center flex-wrap gap-2 sm:gap-3">
                <button 
                    @click="activeFeatureTab = 'gps'"
                    :class="activeFeatureTab === 'gps' ? 'bg-primary-600 text-white font-bold shadow-lg shadow-primary-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-750 font-medium'"
                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2"
                >
                    <span>📍 Radar GPS Geofence</span>
                </button>
                <button 
                    @click="activeFeatureTab = 'logbook'"
                    :class="activeFeatureTab === 'logbook' ? 'bg-primary-600 text-white font-bold shadow-lg shadow-primary-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-750 font-medium'"
                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2"
                >
                    <span>📝 Logbook & Validasi PM</span>
                </button>
                <button 
                    @click="activeFeatureTab = 'maps'"
                    :class="activeFeatureTab === 'maps' ? 'bg-primary-600 text-white font-bold shadow-lg shadow-primary-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-750 font-medium'"
                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2"
                >
                    <span>🗺️ Leaflet & Tile Maps API</span>
                </button>
                <button 
                    @click="activeFeatureTab = 'reports'"
                    :class="activeFeatureTab === 'reports' ? 'bg-primary-600 text-white font-bold shadow-lg shadow-primary-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-750 font-medium'"
                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2"
                >
                    <span>📊 Rekapitulasi Laporan</span>
                </button>
            </div>

            <!-- Tab 1: GPS Geofence -->
            <div x-show="activeFeatureTab === 'gps'" x-cloak x-transition.opacity class="mt-8 rounded-3xl bg-slate-800/60 border border-slate-700/60 p-8 sm:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <span class="text-xs font-bold text-primary-400 uppercase tracking-wider">Akurasi Maksimal</span>
                        <h3 class="text-2xl font-bold text-white">Geofencing Radius Pintar Berbasis Haversine</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Setiap titik presensi dapat dikonfigurasi dengan batas radius kustom (contoh: 50m, 100m, atau 200m). Sistem secara otomatis menghitung kelengkungan bumi untuk menentukan apakah teknisi benar-benar berada di lokasi kerja proyek sebelum mengizinkan absensi masuk.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-300">
                            <li class="flex items-center gap-2">✓ <strong>Anti Titip Absen:</strong> Koordinat dikunci langsung dari hardware sensor GPS.</li>
                            <li class="flex items-center gap-2">✓ <strong>Multi Lokasi:</strong> Mendukung ribuan titik koordinat site proyek berbeda.</li>
                            <li class="flex items-center gap-2">✓ <strong>Reverse Geocode:</strong> Mengubah koordinat menjadi alamat jalan otomatis.</li>
                        </ul>
                    </div>
                    <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-700/80 space-y-3 font-mono text-xs">
                        <p class="text-primary-400 font-bold">// Output Formula Haversine API</p>
                        <p class="text-slate-300">Target Geofence: Site Menara Surabaya (-7.25747, 112.75208)</p>
                        <p class="text-slate-300">User Position: (-7.25750, 112.75210)</p>
                        <p class="text-emerald-400 font-bold">Calculated Distance: 3.39 meters</p>
                        <p class="text-emerald-400 font-bold">Status: WITHIN_RADIUS (Valid)</p>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Logbook & PM Approval -->
            <div x-show="activeFeatureTab === 'logbook'" x-cloak x-transition.opacity class="mt-8 rounded-3xl bg-slate-800/60 border border-slate-700/60 p-8 sm:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Transparansi Progres</span>
                        <h3 class="text-2xl font-bold text-white">Logbook Kegiatan dengan Evaluasi Mandor / PM</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Bukan sekadar absensi masuk dan pulang. Teknisi melaporkan pekerjaan harian, kendala teknis, dan persentase progres. Project Manager dapat langsung memeriksa kualitas pekerjaan lalu memutuskan untuk <strong>Menyetujui</strong> atau <strong>Menolak</strong> dengan catatan revisi yang transparan.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-300">
                            <li class="flex items-center gap-2">✓ <strong>Audit Trail Lengkap:</strong> Riwayat perubahan status tercatat dengan timestamp.</li>
                            <li class="flex items-center gap-2">✓ <strong>Catatan Evaluasi Wajib:</strong> Penolakan mengharuskan feedback perbaikan jelas.</li>
                            <li class="flex items-center gap-2">✓ <strong>Monitoring Terpusat:</strong> Dashboard PM merangkum antrean review kegiatan.</li>
                        </ul>
                    </div>
                    <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-700/80 space-y-3 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                            <span class="font-bold text-white">Logbook: Terminasi Kabel Fiber ODC</span>
                            <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 font-bold">Submitted</span>
                        </div>
                        <p class="text-slate-400 text-[11px]">Teknisi: Ahmad Fauzi • Progres: 75%</p>
                        <div class="flex gap-2 pt-2">
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-[11px]">✓ Setujui</span>
                            <span class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-bold text-[11px]">↺ Tolak (Revisi)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Leaflet & Maps API -->
            <div x-show="activeFeatureTab === 'maps'" x-cloak x-transition.opacity class="mt-8 rounded-3xl bg-slate-800/60 border border-slate-700/60 p-8 sm:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <span class="text-xs font-bold text-sky-400 uppercase tracking-wider">Engine Pemetaan</span>
                        <h3 class="text-2xl font-bold text-white">Peta Interaktif Tanpa Ketergantungan API Berbayar</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Menggunakan ekosistem Leaflet JS dan OpenStreetMap Tile Layer yang ringan, cepat, dan hemat biaya infrastruktur. Dilengkapi Map Picker interaktif pada admin panel untuk klik & pasang koordinat secara langsung.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-300">
                            <li class="flex items-center gap-2">✓ <strong>Interactive Marker & Circle:</strong> Visualisasi lingkaran geofence dinamis.</li>
                            <li class="flex items-center gap-2">✓ <strong>Map Picker Admin:</strong> Klik langsung di peta untuk mendapatkan lat/long.</li>
                            <li class="flex items-center gap-2">✓ <strong>Polyline Distance:</strong> Garis radar visual dari posisi user ke target.</li>
                        </ul>
                    </div>
                    <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-700/80 space-y-2 text-xs font-mono">
                        <p class="text-sky-400 font-bold">// REST API Endpoint</p>
                        <p class="text-slate-300">GET /api/maps/attendance-points</p>
                        <p class="text-slate-300">POST /api/maps/validate-location</p>
                        <p class="text-emerald-400 mt-2">HTTP 200 OK — Response time &lt; 25ms</p>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Rekapitulasi Laporan -->
            <div x-show="activeFeatureTab === 'reports'" x-cloak x-transition.opacity class="mt-8 rounded-3xl bg-slate-800/60 border border-slate-700/60 p-8 sm:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Analitik & Rekap</span>
                        <h3 class="text-2xl font-bold text-white">Laporan Presensi & Logbook Siap Pakai</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Rekapitulasi lengkap berdasarkan rentang tanggal, filter karyawan, atau nama projek. Menyajikan jam masuk, jam pulang, rincian koordinat GPS riil, dan status verifikasi logbook.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-300">
                            <li class="flex items-center gap-2">✓ <strong>Filter Fleksibel:</strong> Berdasarkan periode tanggal dan karyawan.</li>
                            <li class="flex items-center gap-2">✓ <strong>Detail GPS Terarsip:</strong> Koordinat latitude & longitude disimpan rapi.</li>
                        </ul>
                    </div>
                    <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-700/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between font-bold text-white pb-2 border-b border-slate-800">
                            <span>Rekap Presensi November 2026</span>
                            <span class="text-emerald-400">100% Hadir Tepat Waktu</span>
                        </div>
                        <p class="text-slate-400 text-[11px]">Total 140 Entri Presensi Terverifikasi Geofence.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- WORKFLOW SECTION -->
    <section id="workflow" class="py-20 bg-slate-950/80 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h2 class="text-3xl font-extrabold text-white">Alur Kerja Sederhana & Otomatis</h2>
                <p class="text-sm text-slate-400">Hanya butuh 4 langkah mudah untuk memastikan disiplin dan transparansi kerja lapangan.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Step 1 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 relative group hover:border-primary-500/50 transition">
                    <span class="h-10 w-10 rounded-xl bg-primary-600/20 text-primary-400 font-black text-sm flex items-center justify-center border border-primary-500/30">
                        01
                    </span>
                    <h3 class="text-base font-bold text-white">Deteksi Sensor GPS</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Teknisi membuka browser di smartphone. Sensor GPS membaca koordinat posisi terkini secara instan.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 relative group hover:border-primary-500/50 transition">
                    <span class="h-10 w-10 rounded-xl bg-indigo-600/20 text-indigo-400 font-black text-sm flex items-center justify-center border border-indigo-500/30">
                        02
                    </span>
                    <h3 class="text-base font-bold text-white">Validasi Geofence</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sistem memverifikasi apakah koordinat teknisi berada dalam radius lokasi proyek yang ditugaskan.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 relative group hover:border-primary-500/50 transition">
                    <span class="h-10 w-10 rounded-xl bg-sky-600/20 text-sky-400 font-black text-sm flex items-center justify-center border border-sky-500/30">
                        03
                    </span>
                    <h3 class="text-base font-bold text-white">Lapor Logbook</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Setelah bekerja, teknisi mengisi aktivitas pekerjaan harian, kendala, dan persentase progres.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 relative group hover:border-primary-500/50 transition">
                    <span class="h-10 w-10 rounded-xl bg-emerald-600/20 text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-500/30">
                        04
                    </span>
                    <h3 class="text-base font-bold text-white">Review & Selesai</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Project Manager memeriksa dan menyetujui logbook. Data tersimpan rapi untuk rekapitulasi gaji / tagihan.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- ROLES & RBAC SHOWCASE -->
    <section id="roles" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <h2 class="text-3xl font-extrabold text-white">Hak Akses Sesuai Kebutuhan Tim</h2>
            <p class="text-sm text-slate-400">Setiap peran mendapatkan tampilan dashboard dan fungsi yang disesuaikan.</p>
        </div>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Role 1: Admin -->
            <div class="rounded-3xl bg-slate-800/60 border border-slate-700/60 p-6 space-y-4 hover:border-slate-600 transition">
                <div class="h-12 w-12 rounded-2xl bg-rose-500/20 text-rose-400 text-2xl flex items-center justify-center border border-rose-500/30">
                    🛡️
                </div>
                <h3 class="text-lg font-bold text-white">Administrator</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Kendali menyeluruh terhadap sistem: manajemen proyek, pengaturan titik GPS radius, akun pengguna, dan rekapitulasi data.
                </p>
                <div class="pt-3 border-t border-slate-700/60 space-y-2 text-xs text-slate-300">
                    <p>✓ Manajemen Projek & Klien</p>
                    <p>✓ Atur Radius Geofence GPS</p>
                    <p>✓ Penugasan Karyawan ke Tim</p>
                    <p>✓ Ekspor Laporan Presensi</p>
                </div>
            </div>

            <!-- Role 2: Manager -->
            <div class="rounded-3xl bg-slate-800/60 border border-slate-700/60 p-6 space-y-4 hover:border-slate-600 transition">
                <div class="h-12 w-12 rounded-2xl bg-indigo-500/20 text-indigo-400 text-2xl flex items-center justify-center border border-indigo-500/30">
                    📋
                </div>
                <h3 class="text-lg font-bold text-white">Project Manager</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Fokus pada kualitas output lapangan: memonitor sebaran tim proyek dan memvalidasi logbook kegiatan harian.
                </p>
                <div class="pt-3 border-t border-slate-700/60 space-y-2 text-xs text-slate-300">
                    <p>✓ Monitoring Progres Projek</p>
                    <p>✓ Peta Sebaran Titik Tugas Tim</p>
                    <p>✓ Approval & Reject Logbook</p>
                    <p>✓ Catatan Evaluasi Lapangan</p>
                </div>
            </div>

            <!-- Role 3: Karyawan -->
            <div class="rounded-3xl bg-slate-800/60 border border-slate-700/60 p-6 space-y-4 hover:border-slate-600 transition">
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/20 text-emerald-400 text-2xl flex items-center justify-center border border-emerald-500/30">
                    📍
                </div>
                <h3 class="text-lg font-bold text-white">Karyawan / Teknisi</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Pengalaman antarmuka yang cepat di HP: absensi GPS satu klik dan pengisian form logbook tanpa kendala.
                </p>
                <div class="pt-3 border-t border-slate-700/60 space-y-2 text-xs text-slate-300">
                    <p>✓ Presensi Check-In / Check-Out</p>
                    <p>✓ Deteksi GPS & Alamat Otomatis</p>
                    <p>✓ Form Input Logbook Harian</p>
                    <p>✓ Riwayat Catatan Review</p>
                </div>
            </div>

        </div>
    </section>

    <!-- FAQ ACCORDION (ALPINE.JS) -->
    <section id="faq" class="py-20 bg-slate-950/60 border-t border-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center space-y-3">
                <h2 class="text-3xl font-extrabold text-white">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                <p class="text-sm text-slate-400">Temukan jawaban cepat seputar cara kerja presensi GPS dan logbook.</p>
            </div>

            <div class="space-y-3">
                
                <!-- FAQ 1 -->
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <button 
                        @click="activeFaq = (activeFaq === 1 ? null : 1)"
                        class="w-full p-5 text-left font-bold text-sm text-white flex items-center justify-between"
                    >
                        <span>Apakah teknisi dapat memanipulasi koordinat (Fake GPS)?</span>
                        <span class="text-primary-400 font-mono text-base" x-text="activeFaq === 1 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 1" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                        Sistem mengandalkan HTML5 Geolocation API yang membaca data langsung dari satelit GPS hardware perangkat. Backend juga melakukan validasi toleransi akurasi dan formula Haversine untuk memverifikasi jarak riil dalam satuan meter.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <button 
                        @click="activeFaq = (activeFaq === 2 ? null : 2)"
                        class="w-full p-5 text-left font-bold text-sm text-white flex items-center justify-between"
                    >
                        <span>Bagaimana jika teknisi berada di luar radius lokasi proyek?</span>
                        <span class="text-primary-400 font-mono text-base" x-text="activeFaq === 2 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 2" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                        Jika teknisi berada di luar radius geofence (misalnya lebih dari 100 meter dari titik pusat yang ditentukan admin), tombol Check-In akan dinonaktifkan secara otomatis dan sistem menampilkan jarak riil teknisi dari titik tugas.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <button 
                        @click="activeFaq = (activeFaq === 3 ? null : 3)"
                        class="w-full p-5 text-left font-bold text-sm text-white flex items-center justify-between"
                    >
                        <span>Apakah sistem ini membutuhkan aplikasi Android/iOS khusus (APK)?</span>
                        <span class="text-primary-400 font-mono text-base" x-text="activeFaq === 3 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 3" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                        Tidak perlu menginstal APK apapun. Aplikasi ini merupakan Web App responsif (PWA ready) yang dapat diakses langsung melalui Chrome, Safari, atau browser smartphone apapun dengan izin akses lokasi (GPS permission).
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <button 
                        @click="activeFaq = (activeFaq === 4 ? null : 4)"
                        class="w-full p-5 text-left font-bold text-sm text-white flex items-center justify-between"
                    >
                        <span>Bagaimana jika logbook ditolak oleh Project Manager?</span>
                        <span class="text-primary-400 font-mono text-base" x-text="activeFaq === 4 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 4" x-cloak class="px-5 pb-5 text-xs text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                        Project Manager wajib mencantumkan catatan evaluasi saat menolak logbook. Catatan tersebut langsung muncul pada dashboard teknisi agar teknisi dapat memperbaiki laporan pekerjaannya.
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CALL TO ACTION BANNER -->
    <section class="py-20 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-r from-primary-600 via-indigo-600 to-primary-700 p-8 sm:p-14 text-center space-y-6 shadow-2xl shadow-primary-600/30 relative">
                <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    Tingkatkan Disiplin & Akurasi Proyek Anda Hari Ini
                </h2>
                <p class="text-sm sm:text-base text-primary-100 max-w-2xl mx-auto leading-relaxed">
                    Gunakan kredensial akun demo atau hubungi tim IT Administrator Anda untuk mendaftarkan akun teknisi dan titik geofence proyek baru.
                </p>
                <div class="pt-2 flex justify-center">
                    <a href="{{ route('login') }}" 
                       class="px-8 py-3.5 rounded-xl bg-white hover:bg-slate-100 text-primary-700 font-extrabold text-sm shadow-lg transition-transform hover:scale-105">
                        Masuk ke Portal Presensi →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-slate-800 bg-slate-950 py-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-primary-600 text-white font-bold flex items-center justify-center text-sm">
                    TD
                </div>
                <div>
                    <p class="font-bold text-slate-300">PT Technodrives</p>
                    <p class="text-[11px] text-slate-500">Sistem Presensi Berbasis GPS & Logbook Lapangan</p>
                </div>
            </div>
            
            <p class="text-center sm:text-right text-[11px]">
                &copy; {{ date('Y') }} PT Technodrives. Seluruh hak cipta dilindungi undang-undang.<br>
                Dibangun dengan Laravel 12 & Alpine.js
            </p>
        </div>
    </footer>

</body>
</html>

