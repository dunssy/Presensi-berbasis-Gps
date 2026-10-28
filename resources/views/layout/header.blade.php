<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Technodrives — Sistem Presensi Lokasi & Logbook Karyawan</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body 
    x-data="adminApp()" 
    x-init="initApp()" 
    :class="{ 'dark bg-slate-950 text-slate-100': darkMode, 'bg-slate-50 text-slate-800': !darkMode }"
    class="h-full font-sans antialiased selection:bg-primary-600 selection:text-white transition-colors duration-200"
>
    @yield('sidebar')
     <!-- ========================================== -->
    <!-- SLIDE-OVER PANEL: PM LOGBOOK REVIEW        -->
    <!-- ========================================== -->
    <div 
        x-show="slideOver.open" 
        x-cloak 
        class="fixed inset-0 z-[80] overflow-hidden bg-slate-900/60 backdrop-blur-xs"
        role="dialog"
        aria-modal="true"
    >
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div 
                @click.outside="slideOver.open = false"
                x-show="slideOver.open"
                x-transition:enter="transform transition ease-in-out duration-200"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-lg bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col justify-between"
            >
                <template x-if="slideOver.logbook">
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Slide-over Header -->
                        <div class="flex items-start justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="badgeClass(slideOver.logbook.status)" x-text="badgeLabel(slideOver.logbook.status)"></span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-2" x-text="'Logbook Harian — ' + slideOver.logbook.employee"></h3>
                                <p class="text-xs text-slate-500" x-text="slideOver.logbook.log_date + ' (' + slideOver.logbook.start_time + ' - ' + slideOver.logbook.end_time + ')'"></p>
                            </div>
                            <button @click="slideOver.open = false" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">✕</button>
                        </div>

                        <!-- Logbook Content Details -->
                        <div class="space-y-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                                <p class="font-bold text-slate-500 uppercase text-[10px]">Aktivitas Pekerjaan</p>
                                <p class="text-slate-900 dark:text-slate-100 mt-1 leading-relaxed" x-text="slideOver.logbook.activity"></p>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                                <p class="font-bold text-slate-500 uppercase text-[10px]">Output / Hasil Kerja</p>
                                <p class="text-slate-900 dark:text-slate-100 mt-1 leading-relaxed" x-text="slideOver.logbook.result"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <p class="font-bold text-slate-400 uppercase text-[10px]">Kendala Lapangan</p>
                                    <p class="mt-1 text-slate-700 dark:text-slate-300" x-text="slideOver.logbook.obstacle || 'Tidak ada kendala'"></p>
                                </div>
                                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <p class="font-bold text-slate-400 uppercase text-[10px]">Rencana Besok</p>
                                    <p class="mt-1 text-slate-700 dark:text-slate-300" x-text="slideOver.logbook.next_plan"></p>
                                </div>
                            </div>

                            <!-- Review Note Input (Mandatory for Return) -->
                            <div class="pt-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Catatan Review Project Manager <span class="text-amber-600">(Wajib diisi jika dikembalikan untuk revisi)</span>
                                </label>
                                <textarea 
                                    rows="3" 
                                    x-model="slideOver.reviewNote"
                                    placeholder="Tuliskan evaluasi atau instruksi revisi untuk karyawan..."
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500"
                                ></textarea>
                                <p x-show="slideOver.error" x-text="slideOver.error" class="text-red-600 font-semibold mt-1"></p>
                            </div>

                            <!-- Audit Trail Timeline (logbook_histories) -->
                            <div class="pt-2">
                                <h4 class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[11px] mb-3">Riwayat Status (Audit Trail)</h4>
                                <div class="space-y-3 border-l-2 border-slate-200 dark:border-slate-800 ml-2 pl-4">
                                    <template x-for="(hist, idx) in slideOver.logbook.history" :key="idx">
                                        <div class="relative">
                                            <span class="absolute -left-[21px] top-1 h-2.5 w-2.5 rounded-full bg-primary-600"></span>
                                            <p class="font-semibold text-slate-800 dark:text-slate-200" x-text="hist.action"></p>
                                            <p class="text-[11px] text-slate-400" x-text="hist.actor + ' • ' + hist.time"></p>
                                            <template x-if="hist.note">
                                                <p class="mt-1 p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-[11px]" x-text="'Catatan: ' + hist.note"></p>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Slide-over Footer Actions -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-end gap-3">
                    <button 
                        @click="returnLogbookForRevision()"
                        class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition"
                    >
                        ↺ Kembalikan (Minta Revisi)
                    </button>
                    <button 
                        @click="approveSingleLogbook()"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition"
                    >
                        ✓ Setujui Logbook
                    </button>
                </div>
            </div>
        </div>
    </div>
