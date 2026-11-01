@extends('layout.app')

@section('title', 'Laporan Presensi')
@section('page_title', 'Rekapitulasi Laporan Presensi GPS')

@section('content')
<div class="space-y-6">

    <!-- Filter Form Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('admin.reports.attendance') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs items-end">
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
                <label class="block font-semibold text-slate-700 mb-1">Pilih Karyawan</label>
                <select name="user_id" class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Karyawan</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold transition shadow-xs">
                    🔍 Filter Data
                </button>
                <a href="{{ route('admin.reports.attendance') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-center font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Nama Karyawan</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Lokasi / Titik Presensi</th>
                        <th class="px-5 py-3.5">Masuk & GPS</th>
                        <th class="px-5 py-3.5">Pulang & GPS</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $row)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 font-bold text-slate-900">
                                {{ $row->user?->name }}
                            </td>
                            <td class="px-5 py-4 text-slate-600 font-medium">
                                {{ $row->date->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-slate-700">
                                {{ $row->attendancePoint?->name ?? 'GPS Bebas' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">
                                    {{ $row->check_in_time ?? '-' }} WIB
                                </span>
                                @if($row->check_in_latitude)
                                    <p class="font-mono text-[10px] text-slate-400 mt-1">
                                        {{ number_format($row->check_in_latitude, 5) }}, {{ number_format($row->check_in_longitude, 5) }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-[11px]">
                                    {{ $row->check_out_time ?? '-' }} WIB
                                </span>
                                @if($row->check_out_latitude)
                                    <p class="font-mono text-[10px] text-slate-400 mt-1">
                                        {{ number_format($row->check_out_latitude, 5) }}, {{ number_format($row->check_out_longitude, 5) }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    @if($row->status === 'hadir') bg-emerald-100 text-emerald-700
                                    @elseif($row->status === 'terlambat') bg-amber-100 text-amber-700
                                    @else bg-rose-100 text-rose-700 @endif">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-slate-400">
                                Tidak ada data presensi yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

