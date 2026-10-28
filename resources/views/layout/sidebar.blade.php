@extends('layout.header')
@section('sidebar')
   <!-- ========================================== -->
    <!-- MAIN LAYOUT SHELL                          -->
    <!-- ========================================== -->
    <div class="flex h-screen overflow-hidden">

        <!-- MOBILE SIDEBAR BACKDROP -->
        <div 
            x-show="mobileSidebarOpen" 
            x-cloak
            @click="mobileSidebarOpen = false"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
        ></div>

        <!-- LEFT SIDEBAR (Desktop Collapsible + Mobile Drawer) -->
        <aside 
            :class="{
                'w-64': !sidebarCollapsed,
                'w-20': sidebarCollapsed,
                'translate-x-0': mobileSidebarOpen,
                '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
            }"
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-200 ease-in-out lg:static shrink-0"
        >
            <!-- Brand Header -->
            <div class="flex h-16 items-center justify-between px-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white font-bold shadow-sm shadow-primary-600/30">
                        TD
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity class="truncate">
                        <p class="text-sm font-bold tracking-tight text-slate-900 dark:text-white truncate">PT Technodrives</p>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">GeoAttendance & Logbook</p>
                    </div>
                </div>
                <button 
                    @click="mobileSidebarOpen = false" 
                    class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"
                    aria-label="Tutup Menu"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Role Switcher Banner (Interactive Demo Helper) -->
            <div class="p-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/50">
                <label x-show="!sidebarCollapsed" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">
                    Simulasi Role Aktif
                </label>
                <select 
                    x-model="currentRole" 
                    @change="handleRoleChange()"
                    aria-label="Pilih Role Simulasi"
                    class="w-full text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="admin_hr">🛡️ Admin / HR</option>
                    <option value="project_manager">📋 Project Manager</option>
                    <option value="employee">📍 Karyawan (Andi)</option>
                </select>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <template x-for="item in filteredNavItems" :key="item.id">
                    <button
                        @click="switchPage(item.id)"
                        :class="{
                            'bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-400 font-semibold': activePage === item.id,
                            'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-slate-200 font-medium': activePage !== item.id
                        }"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors duration-150 group relative"
                        :title="sidebarCollapsed ? item.label : ''"
                    >
                        <span class="shrink-0 flex items-center justify-center w-5 h-5" x-html="getIconSvg(item.icon)"></span>
                        <span x-show="!sidebarCollapsed" class="truncate flex-1 text-left" x-text="item.label"></span>
                        <template x-if="item.badge && !sidebarCollapsed">
                            <span 
                                class="px-2 py-0.5 text-[11px] font-bold rounded-full"
                                :class="item.badgeColor || 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'"
                                x-text="item.badge"
                            ></span>
                        </template>
                    </button>
                </template>
            </nav>

            <!-- Sidebar Footer Collapse Button (Desktop) -->
            <div class="hidden lg:flex items-center justify-between p-3 border-t border-slate-200 dark:border-slate-800">
                <button 
                    @click="sidebarCollapsed = !sidebarCollapsed"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                >
                    <span x-text="sidebarCollapsed ? '»' : '« Ciutkan Sidebar'"></span>
                </button>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- MAIN CONTENT COLUMN                        -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- TOPBAR -->
            <header class="h-16 shrink-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 lg:px-8 flex items-center justify-between z-30">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Mobile Hamburger -->
                    <button 
                        @click="mobileSidebarOpen = true"
                        class="lg:hidden p-2.5 -ml-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 min-h-[44px] min-w-[44px] flex items-center justify-center"
                        aria-label="Buka Navigasi"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Breadcrumb & Page Title -->
                    <div class="min-w-0">
                        <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                            <span>PT Technodrives</span>
                            <span>/</span>
                            <span class="font-medium text-primary-600 dark:text-primary-400" x-text="roleBadgeLabel"></span>
                        </div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white truncate" x-text="currentPageTitle"></h1>
                    </div>
                </div>

                <!-- Right Actions: Clock, Dark Mode, Notifications, Profile -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Live WIB Time Badge -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span x-text="jakartaTime"></span>
                        <span class="text-slate-400">WIB</span>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button 
                        @click="toggleDarkMode()" 
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition min-h-[44px] min-w-[44px] flex items-center justify-center"
                        aria-label="Ubah Tema Gelap/Terang"
                        :title="darkMode ? 'Mode Terang' : 'Mode Gelap'"
                    >
                        <span x-show="!darkMode">🌙</span>
                        <span x-show="darkMode" x-cloak>☀️</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="relative p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition min-h-[44px] min-w-[44px] flex items-center justify-center"
                            aria-label="Notifikasi"
                        >
                            🔔
                            <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900"></span>
                        </button>
                        <div 
                            x-show="open" 
                            @click.outside="open = false"
                            x-cloak
                            x-transition
                            class="absolute right-0 mt-2 w-80 rounded-xl bg-white dark:bg-slate-900 shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50"
                        >
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Notifikasi Sistem</span>
                                <span class="text-[11px] font-semibold text-primary-600">2 Baru</span>
                            </div>
                            <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-64 overflow-y-auto">
                                <div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer" @click="switchPage('logbook_review'); open = false">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Logbook Menunggu Validasi PM</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Andi Pratama mengirim logbook proyek Bandung.</p>
                                    <p class="text-[10px] text-slate-400 mt-1">10 menit yang lalu</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer" @click="switchPage('attendance_terminal'); open = false">
                                    <p class="text-xs font-semibold text-amber-600 dark:text-amber-400">Revisi Logbook Diperlukan</p>
                                    <p class="text-xs text-slate-500 mt-0.5">PM meminta lampiran foto instalasi server.</p>
                                    <p class="text-[10px] text-slate-400 mt-1">1 jam yang lalu</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Menu -->
                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200 dark:border-slate-800">
                        <div class="h-9 w-9 rounded-xl bg-primary-100 dark:bg-primary-950 text-primary-700 dark:text-primary-300 font-bold text-xs flex items-center justify-center">
                            <span x-text="currentUser.initials"></span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-semibold text-slate-900 dark:text-white leading-none" x-text="currentUser.name"></p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-none" x-text="currentUser.nip"></p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE MAIN WORKSPACE -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8 space-y-6">

                <!-- ========================================== -->
                <!-- 1. DASHBOARD VIEW (Role-Adaptive)          -->
                <!-- ========================================== -->
                <div x-show="activePage === 'dashboard'" x-transition.opacity class="space-y-6">
                    
                    <!-- Role Info Banner -->
                    <div class="rounded-xl bg-gradient-to-r from-primary-600 to-indigo-700 p-5 sm:p-6 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-xs">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                Proyek Aktif: Implementasi Sistem Informasi ABC (Bandung)
                            </span>
                            <h2 class="text-xl sm:text-2xl font-bold mt-2">Selamat Datang, <span x-text="currentUser.name"></span>!</h2>
                            <p class="text-xs sm:text-sm text-primary-100 mt-1 max-w-2xl">
                                Sistem Presensi GPS & Logbook Proyek PT Technodrives. Presensi kantor tetap menggunakan mesin fingerprint, sedangkan tim proyek luar kantor melakukan check-in berbasis radius lokasi.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2 shrink-0">
                            <template x-if="currentRole === 'employee'">
                                <button @click="switchPage('attendance_terminal')" class="px-4 py-2.5 rounded-xl bg-white text-primary-700 font-semibold text-sm shadow-sm hover:bg-primary-50 transition">
                                    📍 Buka Terminal Presensi
                                </button>
                            </template>
                            <template x-if="currentRole === 'project_manager'">
                                <button @click="switchPage('logbook_review')" class="px-4 py-2.5 rounded-xl bg-white text-primary-700 font-semibold text-sm shadow-sm hover:bg-primary-50 transition">
                                    📋 Review Logbook (<span x-text="pendingReviewCount"></span>)
                                </button>
                            </template>
                            <template x-if="currentRole === 'admin_hr'">
                                <button @click="switchPage('attendance_points')" class="px-4 py-2.5 rounded-xl bg-white text-primary-700 font-semibold text-sm shadow-sm hover:bg-primary-50 transition">
                                    🗺️ Kelola Titik Presensi
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- KPI Stat Cards (4 Columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="rounded-xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Hadir Hari Ini (GPS)</span>
                                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 text-sm">✓</span>
                            </div>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-3">3 / 3 <span class="text-xs font-normal text-slate-500">Karyawan Proyek</span></p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium">2 Tepat Waktu • 1 Terlambat</p>
                        </div>

                        <div class="rounded-xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Proyek & Titik Aktif</span>
                                <span class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-sm">📍</span>
                            </div>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-3" x-text="attendancePoints.filter(p => p.is_active).length + ' Titik'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">1 Kantor Pusat • 2 Lokasi Proyek</p>
                        </div>

                        <div class="rounded-xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Menunggu Validasi PM</span>
                                <span class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 text-sm">⏳</span>
                            </div>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-3" x-text="pendingReviewCount + ' Logbook'"></p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium">Klik antrean review untuk validasi</p>
                        </div>

                        <div class="rounded-xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Perlu Revisi</span>
                                <span class="p-2 rounded-lg bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 text-sm">↺</span>
                            </div>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-3" x-text="needsRevisionCount + ' Logbook'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Wajib dilengkapi oleh karyawan</p>
                        </div>
                    </div>

                    <!-- Charts Row (Chart.js) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 rounded-xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tren Kehadiran Proyek (7 Hari Terakhir)</h3>
                                    <p class="text-xs text-slate-500">Perbandingan status Hadir Tepat Waktu vs Terlambat</p>
                                </div>
                                <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">Asia/Jakarta</span>
                            </div>
                            <div class="h-64 relative">
                                <canvas id="attendanceTrendChart"></canvas>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white dark:bg-slate-900 p-5 border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="mb-4">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Komposisi Status Logbook</h3>
                                <p class="text-xs text-slate-500">Distribusi status logbook seluruh anggota tim</p>
                            </div>
                            <div class="h-64 relative flex items-center justify-center">
                                <canvas id="logbookStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Monitoring Table (Auto-refresh simulation) -->
                    <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Monitoring Harian Presensi & Logbook</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span> Polling 60d
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Proyek: Implementasi Sistem Informasi ABC — Bandung</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    x-model="monitoringSearch" 
                                    placeholder="Cari nama karyawan..." 
                                    class="text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                >
                                <button 
                                    @click="triggerToast('success', 'Data Diperbarui', 'Tabel monitoring harian telah disinkronkan dengan server.')"
                                    class="px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                >
                                    ↻ Segarkan
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/40 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                        <th class="py-3 px-4">Karyawan</th>
                                        <th class="py-3 px-4">Titik Presensi</th>
                                        <th class="py-3 px-4">Check-In</th>
                                        <th class="py-3 px-4">Check-Out</th>
                                        <th class="py-3 px-4">Jarak / Akurasi</th>
                                        <th class="py-3 px-4">Status Presensi</th>
                                        <th class="py-3 px-4">Status Logbook</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                                    <template x-for="row in filteredMonitoring" :key="row.id">
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">
                                                <div x-text="row.employee"></div>
                                                <div class="text-[11px] font-normal text-slate-400" x-text="row.role"></div>
                                            </td>
                                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300" x-text="row.point"></td>
                                            <td class="py-3.5 px-4 font-mono text-slate-700 dark:text-slate-300" x-text="row.check_in"></td>
                                            <td class="py-3.5 px-4 font-mono text-slate-700 dark:text-slate-300" x-text="row.check_out || '—'"></td>
                                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                                <span x-text="row.distance + ' m'"></span>
                                                <span class="text-slate-400" x-text="'(±' + row.accuracy + 'm)'"></span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span 
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="badgeClass(row.attendance_status)"
                                                    x-text="badgeLabel(row.attendance_status)"
                                                ></span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span 
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="badgeClass(row.logbook_status)"
                                                    x-text="badgeLabel(row.logbook_status)"
                                                ></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 2. EMPLOYEE ATTENDANCE TERMINAL (GPS)      -->
                <!-- ========================================== -->
                <div x-show="activePage === 'attendance_terminal'" x-cloak x-transition.opacity class="space-y-6">
                    
                    <!-- Simulator Controls for Easy Testing (Local Dev Mode) -->
                    <div class="rounded-xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/70 p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="text-lg">🛠️</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Panel Simulasi GPS & Haversine (Mode Pengujian)</p>
                                <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                                    Uji skenario *Business Rules #1–#6* secara langsung: di dalam radius (80m), di luar radius (750m), akurasi GPS buruk (>100m), atau izin lokasi ditolak.
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <button 
                                @click="simulateGpsScenario('inside')"
                                :class="gpsState.scenario === 'inside' ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700 shadow-2xs transition"
                            >
                                ✅ Dalam Radius (80m)
                            </button>
                            <button 
                                @click="simulateGpsScenario('outside')"
                                :class="gpsState.scenario === 'outside' ? 'bg-red-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700 shadow-2xs transition"
                            >
                                ❌ Luar Radius (750m)
                            </button>
                            <button 
                                @click="simulateGpsScenario('poor_accuracy')"
                                :class="gpsState.scenario === 'poor_accuracy' ? 'bg-amber-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700 shadow-2xs transition"
                            >
                                📡 Akurasi Buruk (145m)
                            </button>
                            <button 
                                @click="simulateGpsScenario('denied')"
                                :class="gpsState.scenario === 'denied' ? 'bg-slate-900 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700 shadow-2xs transition"
                            >
                                🚫 Izin Ditolak
                            </button>
                        </div>
                    </div>

                    <!-- Permission Denied Alert Banner -->
                    <div x-show="gpsState.permissionDenied" x-cloak class="rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 p-4 text-xs text-red-800 dark:text-red-200 space-y-1">
                        <p class="font-bold text-sm">Akses Lokasi (Geolocation) Ditolak oleh Browser</p>
                        <p>Cara mengaktifkan kembali: Klik ikon gembok/pengaturan di sebelah kiri bilah alamat browser (URL bar) → Izinkan <strong>Lokasi (Location)</strong> → Muat ulang halaman.</p>
                    </div>

                    <!-- Main Terminal Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Left Column: Clock, Status, Assignment & Check-In Button -->
                        <div class="lg:col-span-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6 flex flex-col justify-between space-y-6">
                            <div class="space-y-4">
                                <!-- Today Status Pill -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Terminal Presensi Proyek</span>
                                    <span 
                                        class="px-3 py-1 rounded-full text-xs font-bold"
                                        :class="{
                                            'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': attendanceSession.state === 'not_checked_in',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300': attendanceSession.state === 'checked_in',
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': attendanceSession.state === 'completed'
                                        }"
                                        x-text="attendanceSession.state === 'not_checked_in' ? 'Belum Check-In' : (attendanceSession.state === 'checked_in' ? 'Sudah Check-In' : 'Presensi Selesai')"
                                    ></span>
                                </div>

                                <!-- Digital Clock -->
                                <div class="text-center py-5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200/70 dark:border-slate-800">
                                    <p class="text-3xl sm:text-4xl font-extrabold tracking-tight font-mono text-slate-900 dark:text-white" x-text="jakartaTime"></p>
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Rabu, 21 Oktober 2026 • Zona Waktu Server (WIB)</p>
                                </div>

                                <!-- Active Assignment Card -->
                                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Nama Proyek:</span>
                                        <span class="font-semibold text-slate-900 dark:text-white text-right">Implementasi Sistem Informasi ABC</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Titik Presensi:</span>
                                        <span class="font-semibold text-primary-600 dark:text-primary-400 text-right">Site Bandung — Gedung Sate Tower</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Radius Diizinkan:</span>
                                        <span class="font-semibold text-slate-900 dark:text-white">Maksimal 200 meter</span>
                                    </div>
                                </div>

                                <!-- Live Distance & GPS Accuracy Readout -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
                                        <p class="text-[11px] text-slate-500">Jarak ke Titik (Haversine)</p>
                                        <p class="text-lg font-bold mt-0.5" :class="gpsState.distance <= 200 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                                            <span x-text="gpsState.distance"></span> m <span class="text-xs font-normal text-slate-400">/ 200 m</span>
                                        </p>
                                        <span 
                                            class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                            :class="gpsState.distance <= 200 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'"
                                            x-text="gpsState.distance <= 200 ? 'Di Dalam Area' : 'Di Luar Area'"
                                        ></span>
                                    </div>

                                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
                                        <p class="text-[11px] text-slate-500">Akurasi Sinyal GPS</p>
                                        <p class="text-lg font-bold mt-0.5" :class="gpsState.accuracy <= 100 ? 'text-slate-900 dark:text-white' : 'text-amber-600 dark:text-amber-400'">
                                            ±<span x-text="gpsState.accuracy"></span> meter
                                        </p>
                                        <span 
                                            class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                            :class="gpsState.accuracy <= 100 ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                                            x-text="gpsState.accuracy <= 100 ? 'Sinyal Akurat' : 'Akurasi Lemah (>100m)'"
                                        ></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button & Reason Message -->
                            <div class="space-y-3 pt-2">
                                <template x-if="attendanceRejectionReason">
                                    <div class="p-3 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-xs text-red-700 dark:text-red-300 font-medium flex items-center gap-2">
                                        <span>⚠️</span>
                                        <span x-text="attendanceRejectionReason"></span>
                                    </div>
                                </template>

                                <button 
                                    @click="submitAttendanceAction()"
                                    :disabled="Boolean(attendanceRejectionReason) || attendanceSession.loading || attendanceSession.state === 'completed'"
                                    class="w-full py-3.5 px-6 rounded-xl font-bold text-sm text-white shadow-md transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px]"
                                    :class="attendanceSession.state === 'checked_in' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-primary-600 hover:bg-primary-700'"
                                >
                                    <template x-if="attendanceSession.loading">
                                        <span class="inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    </template>
                                    <span x-text="attendanceButtonLabel"></span>
                                </button>

                                <!-- Post Check-In Summary Card -->
                                <div x-show="attendanceSession.checkInTime" x-transition class="p-3.5 rounded-xl bg-emerald-50/90 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/80 text-xs space-y-1">
                                    <div class="flex items-center justify-between font-bold text-emerald-800 dark:text-emerald-300">
                                        <span>✓ Rekap Presensi Hari Ini</span>
                                        <span x-text="attendanceSession.durationText"></span>
                                    </div>
                                    <p class="text-emerald-700 dark:text-emerald-400">
                                        Check-In: <strong x-text="attendanceSession.checkInTime"></strong> (<span x-text="attendanceSession.checkInDistance + 'm'"></span>)
                                        <template x-if="attendanceSession.checkOutTime">
                                            <span> • Check-Out: <strong x-text="attendanceSession.checkOutTime"></strong></span>
                                        </template>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Interactive Leaflet Map -->
                        <div class="lg:col-span-7 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Peta Radius Presensi Real-Time (OpenStreetMap)</h3>
                                    <p class="text-xs text-slate-500">Lingkaran hijau = di dalam area presensi, merah = di luar jangkauan.</p>
                                </div>
                                <button @click="fitEmployeeMap()" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800">
                                    🎯 Fokus Peta
                                </button>
                            </div>
                            <div id="employeeAttendanceMap" class="w-full flex-1 min-h-[380px] rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800"></div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 3. ADMIN ATTENDANCE POINT MAP EDITOR       -->
                <!-- ========================================== -->
                <div x-show="activePage === 'attendance_points'" x-cloak x-transition.opacity class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Left Column: Form Inputs -->
                        <div class="lg:col-span-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-6 space-y-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Form Titik Presensi Baru / Edit</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Klik peta atau geser pin untuk mengisi koordinat secara instan.</p>
                            </div>

                            <div class="space-y-3.5 text-xs">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Cari Alamat (Nominatim OSM)</label>
                                    <div class="flex gap-2">
                                        <input 
                                            type="text" 
                                            x-model="pointForm.searchQuery"
                                            @keydown.enter.prevent="searchNominatimAddress()"
                                            placeholder="Contoh: Gedung Sate Bandung..." 
                                            class="flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                        >
                                        <button 
                                            type="button"
                                            @click="searchNominatimAddress()"
                                            class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                                        >
                                            Cari
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Titik Presensi <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        x-model="pointForm.name" 
                                        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                    >
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Lokasi</label>
                                        <select x-model="pointForm.type" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
                                            <option value="project">Lokasi Proyek</option>
                                            <option value="office">Kantor PT Technodrives</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Proyek Terkait</label>
                                        <select x-model="pointForm.project" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
                                            <option value="Implementasi Sistem Informasi ABC">Implementasi SI ABC</option>
                                            <option value="Kantor Pusat">Kantor Pusat (Internal)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                                        <input 
                                            type="number" step="0.0000001" 
                                            x-model.number="pointForm.latitude" 
                                            @input="syncAdminMapFromInputs()"
                                            class="w-full font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2"
                                        >
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                                        <input 
                                            type="number" step="0.0000001" 
                                            x-model.number="pointForm.longitude" 
                                            @input="syncAdminMapFromInputs()"
                                            class="w-full font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2"
                                        >
                                    </div>
                                </div>

                                <!-- Radius Slider + Input (10m - 1000m) -->
                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="font-semibold text-slate-700 dark:text-slate-300">Radius Toleransi Presensi (m)</label>
                                        <input 
                                            type="number" min="10" max="1000" 
                                            x-model.number="pointForm.radius_m"
                                            @input="syncAdminMapFromInputs()"
                                            class="w-20 text-center font-bold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-1"
                                        >
                                    </div>
                                    <input 
                                        type="range" min="10" max="1000" step="10"
                                        x-model.number="pointForm.radius_m"
                                        @input="syncAdminMapFromInputs()"
                                        class="w-full accent-primary-600 cursor-pointer"
                                    >
                                    <div class="flex justify-between text-[10px] text-slate-400">
                                        <span>Min: 10m</span>
                                        <span>Standar Proyek: 200m</span>
                                        <span>Maks: 1000m</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <button 
                                        type="button" 
                                        @click="useCurrentLocationForAdminPoint()"
                                        class="px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                    >
                                        📍 Gunakan Lokasi Saya
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="saveAttendancePoint()"
                                        class="px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm transition"
                                    >
                                        Simpan Titik Presensi
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Interactive Map Picker -->
                        <div class="lg:col-span-7 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pratinjau Peta & Radius Geografis</h3>
                                    <p class="text-xs text-slate-500">Klik di mana saja pada peta atau seret marker biru untuk memindahkan titik.</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300" x-text="'Radius: ' + pointForm.radius_m + ' m'"></span>
                            </div>
                            <div id="adminPointMap" class="w-full flex-1 min-h-[380px] rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800"></div>
                        </div>
                    </div>

                    <!-- Saved Attendance Points List -->
                    <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Titik Presensi Terdaftar</h3>
                            <span class="text-xs text-slate-500" x-text="attendancePoints.length + ' Lokasi'"></span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 uppercase text-[11px]">
                                    <tr>
                                        <th class="py-3 px-4">Nama Titik</th>
                                        <th class="py-3 px-4">Tipe / Proyek</th>
                                        <th class="py-3 px-4">Koordinat</th>
                                        <th class="py-3 px-4">Radius</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    <template x-for="pt in attendancePoints" :key="pt.id">
                                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                            <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white" x-text="pt.name"></td>
                                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300" x-text="pt.project"></td>
                                            <td class="py-3.5 px-4 font-mono text-slate-500" x-text="pt.latitude.toFixed(5) + ', ' + pt.longitude.toFixed(5)"></td>
                                            <td class="py-3.5 px-4 font-semibold" x-text="pt.radius_m + ' m'"></td>
                                            <td class="py-3.5 px-4">
                                                <span 
                                                    class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="pt.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                                    x-text="pt.is_active ? 'Aktif' : 'Nonaktif'"
                                                ></span>
                                            </td>
                                            <td class="py-3.5 px-4 text-right">
                                                <button 
                                                    @click="confirmTogglePoint(pt)"
                                                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 font-semibold hover:bg-slate-100 dark:hover:bg-slate-800"
                                                    x-text="pt.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                                                ></button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 4. LOGBOOK & PM SLIDE-OVER REVIEW QUEUE    -->
                <!-- ========================================== -->
                <div x-show="activePage === 'logbook_review'" x-cloak x-transition.opacity class="space-y-6">
                    
                    <!-- Filter & Bulk Action Header -->
                    <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-for="st in ['all', 'submitted', 'needs_revision', 'approved', 'draft']" :key="st">
                                <button 
                                    @click="logbookFilter = st"
                                    :class="logbookFilter === st ? 'bg-primary-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'"
                                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
                                    x-text="st === 'all' ? 'Semua Status' : badgeLabel(st)"
                                ></button>
                            </template>
                        </div>

                        <div class="flex items-center gap-2">
                            <button 
                                x-show="selectedLogbookIds.length > 0"
                                @click="bulkApproveLogbooks()"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition"
                            >
                                ✓ Setujui Terpilih (<span x-text="selectedLogbookIds.length"></span>)
                            </button>
                            <button 
                                @click="openNewLogbookModal()"
                                class="px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition"
                            >
                                + Tulis Logbook Harian
                            </button>
                        </div>
                    </div>

                    <!-- Logbook List Table -->
                    <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 uppercase text-[11px] border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="py-3.5 px-4 w-10">
                                            <input type="checkbox" @change="toggleSelectAllLogbooks($event)" class="rounded border-slate-300">
                                        </th>
                                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                                        <th class="py-3.5 px-4">Karyawan</th>
                                        <th class="py-3.5 px-4">Aktivitas & Hasil Kerja</th>
                                        <th class="py-3.5 px-4">Revisi</th>
                                        <th class="py-3.5 px-4">Status</th>
                                        <th class="py-3.5 px-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    <template x-for="lb in filteredLogbooks" :key="lb.id">
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4">
                                                <input 
                                                    type="checkbox" 
                                                    :value="lb.id" 
                                                    x-model="selectedLogbookIds"
                                                    :disabled="lb.status !== 'submitted'"
                                                    class="rounded border-slate-300 disabled:opacity-30"
                                                >
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-semibold text-slate-900 dark:text-white" x-text="lb.log_date"></div>
                                                <div class="text-[11px] text-slate-400 font-mono" x-text="lb.start_time + ' - ' + lb.end_time"></div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-semibold text-slate-900 dark:text-white" x-text="lb.employee"></div>
                                                <div class="text-[11px] text-slate-400" x-text="lb.location_name"></div>
                                            </td>
                                            <td class="py-3.5 px-4 max-w-md">
                                                <p class="font-medium text-slate-800 dark:text-slate-200 truncate" x-text="lb.activity"></p>
                                                <p class="text-slate-500 truncate mt-0.5" x-text="'Hasil: ' + lb.result"></p>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono text-[11px]" x-text="lb.revision_count + 'x'"></span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span 
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="badgeClass(lb.status)"
                                                    x-text="badgeLabel(lb.status)"
                                                ></span>
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <button 
                                                    @click="inspectLogbook(lb)"
                                                    class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-primary-50 dark:hover:bg-primary-950/60 text-primary-600 dark:text-primary-400 font-semibold transition"
                                                >
                                                    Detail & Review →
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 5. DESIGN SYSTEM STYLEGUIDE (/styleguide)  -->
                <!-- ========================================== -->
                <div x-show="activePage === 'styleguide'" x-cloak x-transition.opacity class="space-y-6">
                    <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 space-y-6">
                        <div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300">APP_ENV=local</span>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-2">Dokumentasi Komponen UI & Design System (`/styleguide`)</h2>
                            <p class="text-xs text-slate-500">Seluruh komponen Blade dibangun dengan standar ukuran sentuh ≥44px, kontras WCAG AA, dan dukungan penuh Dark Mode.</p>
                        </div>

                        <!-- Status Badges Showcase -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Status Badges (Presensi & Logbook)</h3>
                            <div class="flex flex-wrap gap-2.5 pt-1">
                                <template x-for="st in ['draft', 'submitted', 'approved', 'needs_revision', 'present', 'late', 'incomplete']" :key="st">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="badgeClass(st)" x-text="badgeLabel(st) + ' (' + st + ')'"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Buttons Showcase -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Varian Tombol & Toast Trigger</h3>
                            <div class="flex flex-wrap gap-3 pt-1">
                                <button @click="triggerToast('success', 'Operasi Berhasil', 'Data berhasil disimpan ke dalam database.')" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold shadow-xs transition">
                                    Primary Button (Toast Sukses)
                                </button>
                                <button @click="triggerToast('warning', 'Perhatian Radius', 'Anda berada 185m dari titik presensi.')" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-semibold transition">
                                    Secondary Button (Toast Peringatan)
                                </button>
                                <button @click="triggerToast('error', 'Check-In Ditolak', 'Jarak aktual 750 m melebihi batas radius 200 m.')" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-xs transition">
                                    Danger Button (Toast Gagal)
                                </button>
                            </div>
                        </div>

                        <!-- Monthly Attendance Calendar Preview -->
                        <div class="space-y-3 pt-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">3. Kalender Riwayat Presensi Bulanan (Oktober 2026)</h3>
                            <div class="grid grid-cols-7 gap-2 max-w-xl text-center text-xs">
                                <template x-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']">
                                    <div class="font-bold text-slate-400 py-1" x-text="day"></div>
                                </template>
                                <template x-for="d in calendarDays" :key="d.day">
                                    <div 
                                        class="p-2.5 rounded-xl border font-semibold flex flex-col items-center justify-center gap-1"
                                        :class="{
                                            'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950/50 dark:border-emerald-800/70 dark:text-emerald-300': d.status === 'present',
                                            'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-950/50 dark:border-amber-800/70 dark:text-amber-300': d.status === 'late',
                                            'bg-red-50 border-red-200 text-red-800 dark:bg-red-950/50 dark:border-red-800/70 dark:text-red-300': d.status === 'incomplete',
                                            'bg-slate-50 border-slate-200 text-slate-400 dark:bg-slate-900 dark:border-slate-800': d.status === 'weekend'
                                        }"
                                    >
                                        <span x-text="d.day"></span>
                                        <span class="h-1.5 w-1.5 rounded-full" :class="{
                                            'bg-emerald-500': d.status === 'present',
                                            'bg-amber-500': d.status === 'late',
                                            'bg-red-500': d.status === 'incomplete',
                                            'bg-slate-300 dark:bg-slate-700': d.status === 'weekend'
                                        }"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

            </main>

            <!-- ========================================== -->
            <!-- MOBILE BOTTOM NAVIGATION (Employee Role)   -->
            <!-- ========================================== -->
            <nav 
                x-show="currentRole === 'employee'"
                class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 grid grid-cols-4 px-2 py-1"
            >
                <button @click="switchPage('dashboard')" class="flex flex-col items-center justify-center min-h-[48px] rounded-xl text-[11px] font-semibold" :class="activePage === 'dashboard' ? 'text-primary-600 dark:text-primary-400' : 'text-slate-500'">
                    <span>🏠</span>
                    <span>Beranda</span>
                </button>
                <button @click="switchPage('attendance_terminal')" class="flex flex-col items-center justify-center min-h-[48px] rounded-xl text-[11px] font-semibold" :class="activePage === 'attendance_terminal' ? 'text-primary-600 dark:text-primary-400' : 'text-slate-500'">
                    <span>📍</span>
                    <span>Presensi</span>
                </button>
                <button @click="switchPage('logbook_review')" class="flex flex-col items-center justify-center min-h-[48px] rounded-xl text-[11px] font-semibold" :class="activePage === 'logbook_review' ? 'text-primary-600 dark:text-primary-400' : 'text-slate-500'">
                    <span>📓</span>
                    <span>Logbook</span>
                </button>
                <button @click="switchPage('styleguide')" class="flex flex-col items-center justify-center min-h-[48px] rounded-xl text-[11px] font-semibold" :class="activePage === 'styleguide' ? 'text-primary-600 dark:text-primary-400' : 'text-slate-500'">
                    <span>📅</span>
                    <span>Riwayat</span>
                </button>
            </nav>
        </div>
    </div>
@endsection