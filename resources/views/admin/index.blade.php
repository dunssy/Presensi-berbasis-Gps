@extends('layout.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard Utama Administrator')

@section('content')
<div class="space-y-6">

    <!-- Summary Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_karyawan'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl">
                👥
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Projek Aktif</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_projek'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                📁
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Presensi Hari Ini</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['presensi_hari_ini'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                📍
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Titik GPS Aktif</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['titik_gps_aktif'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                🛰️
            </div>
        </div>
    </div>

    <!-- Quick Navigation Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('admin.projects.index') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3 transition">
            <span class="text-2xl">➕</span>
            <div class="text-left">
                <p class="text-xs font-bold text-slate-900">Kelola Projek</p>
                <p class="text-[10px] text-slate-500">Tambah & atur proyek</p>
            </div>
        </a>
        <a href="{{ route('admin.attendance-points.index') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3 transition">
            <span class="text-2xl">📍</span>
            <div class="text-left">
                <p class="text-xs font-bold text-slate-900">Titik Geofence</p>
                <p class="text-[10px] text-slate-500">Atur radius GPS</p>
            </div>
        </a>
        <a href="{{ route('admin.employees.index') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3 transition">
            <span class="text-2xl">👤</span>
            <div class="text-left">
                <p class="text-xs font-bold text-slate-900">Data Karyawan</p>
                <p class="text-[10px] text-slate-500">Akun & jabatan</p>
            </div>
        </a>
        <a href="{{ route('admin.reports.attendance') }}" class="p-4 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3 transition">
            <span class="text-2xl">📊</span>
            <div class="text-left">
                <p class="text-xs font-bold text-slate-900">Rekap Laporan</p>
                <p class="text-[10px] text-slate-500">Presensi & logbook</p>
            </div>
        </a>
    </div>

    <!-- Grid: Presensi Terbaru & Projek Berjalan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Presensi Terbaru Hari Ini -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Presensi Karyawan Hari Ini</h3>
                <a href="{{ route('admin.reports.attendance') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentAttendances as $attendance)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($attendance->user?->name ?? 'K', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $attendance->user?->name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $attendance->attendancePoint?->name ?? 'Lokasi GPS Lapangan' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                                {{ $attendance->check_in_time ?? '-' }} WIB
                            </span>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                @if($attendance->check_out_time)
                                    Pulang: {{ $attendance->check_out_time }}
                                @else
                                    Aktif bekerja
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada data presensi untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Projek Aktif Berjalan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Projek Berjalan</h3>
                <a href="{{ route('admin.projects.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Semua Projek →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($activeProjects as $projek)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <span class="font-mono text-[10px] font-semibold text-primary-600 bg-primary-50 px-2 py-0.5 rounded">
                                {{ $projek->code }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">{{ $projek->name }}</h4>
                            <p class="text-[11px] text-slate-500">Klien: {{ $projek->client_name ?? '-' }}</p>
                        </div>
                        <div class="text-right text-xs">
                            <span class="font-semibold text-slate-700">{{ $projek->users_count }} Teknisi</span>
                            <p class="text-[10px] text-slate-400">{{ $projek->attendance_points_count }} Titik GPS</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada projek aktif yang didaftarkan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
