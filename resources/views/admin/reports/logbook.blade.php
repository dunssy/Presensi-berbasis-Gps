@extends('layout.app')

@section('title', 'Laporan Logbook')
@section('page_title', 'Rekapitulasi Laporan Logbook Lapangan')

@section('content')
<div class="space-y-6">

    <!-- Filter Form Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('admin.reports.logbook') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs items-end">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Projek</label>
                <select name="projek_id" class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Projek</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('projek_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold transition shadow-xs">
                    🔍 Filter Logbook
                </button>
                <a href="{{ route('admin.reports.logbook') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-center font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logbook Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal & Karyawan</th>
                        <th class="px-5 py-3.5">Projek</th>
                        <th class="px-5 py-3.5">Aktivitas Kegiatan</th>
                        <th class="px-5 py-3.5">Progres</th>
                        <th class="px-5 py-3.5">Status & Reviewer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logbooks as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-900">{{ $log->user?->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $log->date->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-primary-600">
                                {{ $log->projek?->name ?? 'Umum' }}
                            </td>
                            <td class="px-5 py-4 max-w-sm">
                                <p class="font-bold text-slate-800">{{ $log->title }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $log->description }}</p>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-700">
                                {{ $log->progress_percentage }}%
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    @if($log->status === 'approved') bg-emerald-100 text-emerald-700
                                    @elseif($log->status === 'rejected') bg-rose-100 text-rose-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($log->status) }}
                                </span>
                                @if($log->reviewer)
                                    <p class="text-[10px] text-slate-400 mt-0.5">Oleh: {{ $log->reviewer->name }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-xs text-slate-400">
                                Tidak ada logbook yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logbooks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logbooks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

