@extends('layout.app')

@section('title', 'Logbook Saya')
@section('page_title', 'Riwayat & Arsip Logbook Kegiatan')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Arsip Logbook Kerja Saya</h3>
            <p class="text-xs text-slate-500">Daftar seluruh laporan aktivitas harian yang telah Anda ajukan.</p>
        </div>
        <a href="{{ route('karyawan.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition inline-flex items-center gap-2">
            <span>➕</span>
            <span>Input Logbook Hari Ini</span>
        </a>
    </div>

    <!-- Logbook Cards List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($logbooks as $log)
                <div class="p-5 hover:bg-slate-50/50 transition flex items-start justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                @if($log->status === 'approved') bg-emerald-100 text-emerald-700
                                @elseif($log->status === 'rejected') bg-rose-100 text-rose-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ ucfirst($log->status) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500">{{ $log->date->format('d M Y') }}</span>
                            <span class="text-xs font-semibold text-primary-600">{{ $log->projek?->name ?? 'Projek Umum' }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $log->title }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $log->description }}</p>

                        @if($log->review_notes)
                            <div class="mt-2 p-3 rounded-xl bg-amber-50 text-amber-800 text-xs border border-amber-200 flex items-start gap-2">
                                <span class="text-amber-600">💬</span>
                                <div>
                                    <p class="font-bold">Catatan Review Project Manager:</p>
                                    <p class="mt-0.5">{{ $log->review_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-xs font-bold text-slate-800">{{ $log->progress_percentage }}%</span>
                        <p class="text-[10px] text-slate-400">Progres</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada logbook yang dicatat.
                </div>
            @endforelse
        </div>

        @if($logbooks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logbooks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

