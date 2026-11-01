@extends('layout.app')

@section('title', 'Manager Dashboard')
@section('page_title', 'Dashboard Project Manager')

@section('content')
<div class="space-y-6">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Menunggu Review</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['pending_logbooks'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                📝
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Projek Berjalan</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['projek_berjalan'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                📁
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Presensi Tim Hari Ini</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['presensi_hari_ini'] ?? 0 }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl">
                👥
            </div>
        </div>
    </div>

    <!-- Pending Logbook Review List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Antrean Logbook Menunggu Validasi</h3>
            <span class="text-xs font-semibold text-slate-500">{{ count($pendingReviews) }} kegiatan</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($pendingReviews as $logbook)
                <div class="p-5 hover:bg-slate-50/50 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                {{ ucfirst($logbook->status) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500">{{ $logbook->date->format('d M Y') }}</span>
                            <span class="text-xs font-semibold text-indigo-600">{{ $logbook->projek?->name ?? 'Projek Lapangan' }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $logbook->title }}</h4>
                        <p class="text-xs text-slate-600">{{ $logbook->description }}</p>
                        <p class="text-[11px] text-slate-400">Teknisi: <strong class="text-slate-600">{{ $logbook->user?->name }}</strong> • Progres: {{ $logbook->progress_percentage }}%</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('manager.logbooks.approve', $logbook) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs">
                                ✓ Setujui
                            </button>
                        </form>
                        <form action="{{ route('manager.logbooks.reject', $logbook) }}" method="POST" class="flex items-center gap-1">
                            @csrf
                            <input type="hidden" name="review_notes" value="Mohon lengkapi dokumentasi foto dan rincian pekerjaan.">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs">
                                ↺ Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    Tidak ada logbook yang sedang menunggu persetujuan saat ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
