@extends('layout.app')

@section('title', 'Monitoring Projek')
@section('page_title', 'Monitoring Status Projek Lapangan')

@section('content')
<div class="space-y-6">

    <!-- Header Summary -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Projek Lapangan Terdaftar</h3>
            <p class="text-xs text-slate-500">Pantau perkembangan tim, lokasi titik tugas, dan aktivitas teknisi.</p>
        </div>
    </div>

    <!-- Project Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($projects as $proj)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-md transition">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[10px] font-bold text-primary-600 bg-primary-50 px-2.5 py-0.5 rounded">
                            {{ $proj->code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                            @if($proj->status === 'aktif') bg-emerald-100 text-emerald-700
                            @elseif($proj->status === 'selesai') bg-blue-100 text-blue-700
                            @else bg-amber-100 text-amber-700 @endif">
                            {{ ucfirst($proj->status) }}
                        </span>
                    </div>

                    <h4 class="text-sm font-bold text-slate-900">{{ $proj->name }}</h4>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $proj->description ?? 'Tidak ada deskripsi.' }}</p>
                    <p class="text-[11px] text-slate-400">Klien: <strong class="text-slate-600">{{ $proj->client_name ?? '-' }}</strong></p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div class="text-slate-500">
                        <span>👥 {{ $proj->assignments_count }} Anggota</span> • 
                        <span>📝 {{ $proj->logbooks_count }} Log</span>
                    </div>
                    <a href="{{ route('manager.projects.show', $proj) }}" class="font-bold text-primary-600 hover:text-primary-700">
                        Detail →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-8 bg-white rounded-2xl text-center text-xs text-slate-400 border border-slate-100">
                Belum ada projek yang dapat dipantau saat ini.
            </div>
        @endforelse
    </div>

    @if($projects->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection

