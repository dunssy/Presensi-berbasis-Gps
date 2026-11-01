@extends('layout.app')

@section('title', 'Review Logbook')
@section('page_title', 'Validasi & Evaluasi Logbook Karyawan')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Pengajuan Logbook Lapangan</h3>
            <p class="text-xs text-slate-500">Tinjau hasil kerja teknisi dan berikan persetujuan atau catatan evaluasi revisi.</p>
        </div>
    </div>

    <!-- Logbook Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($logbooks as $logbook)
                <div class="p-5 hover:bg-slate-50/50 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                @if($logbook->status === 'approved') bg-emerald-100 text-emerald-700
                                @elseif($logbook->status === 'rejected') bg-rose-100 text-rose-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ ucfirst($logbook->status) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500">{{ $logbook->date->format('d M Y') }}</span>
                            <span class="text-xs font-semibold text-primary-600">{{ $logbook->projek?->name ?? 'Projek Umum' }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $logbook->title }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $logbook->description }}</p>
                        
                        <div class="flex items-center gap-4 text-[11px] text-slate-400 pt-1">
                            <span>Diajukan oleh: <strong class="text-slate-700">{{ $logbook->user?->name }}</strong></span>
                            <span>Progres: <strong class="text-primary-600 font-bold">{{ $logbook->progress_percentage }}%</strong></span>
                            @if($logbook->review_notes)
                                <span class="text-amber-700 font-medium">Catatan: {{ $logbook->review_notes }}</span>
                            @endif
                        </div>
                    </div>

                    @if($logbook->status === 'submitted')
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
                    @else
                        <div class="text-right shrink-0">
                            <span class="text-[11px] text-slate-400">
                                Selesai ditinjau<br>
                                {{ $logbook->reviewed_at ? $logbook->reviewed_at->format('d M Y H:i') : '' }}
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada pengajuan logbook.
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

