@extends('layout.app')

@section('title', 'Detail Projek')
@section('page_title', 'Detail Projek: ' . $project->name)

@section('content')
<div class="space-y-6">

    <!-- Project Information Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <span class="font-mono text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded">
                    {{ $project->code }}
                </span>
                <h3 class="text-lg font-bold text-slate-900 mt-2">{{ $project->name }}</h3>
                <p class="text-xs text-slate-500">Klien: {{ $project->client_name ?? '-' }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold self-start sm:self-auto
                @if($project->status === 'aktif') bg-emerald-100 text-emerald-700
                @elseif($project->status === 'selesai') bg-blue-100 text-blue-700
                @else bg-amber-100 text-amber-700 @endif">
                Status: {{ ucfirst($project->status) }}
            </span>
        </div>

        <p class="text-xs text-slate-700 leading-relaxed">{{ $project->description ?? 'Tidak ada keterangan tambahan.' }}</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-2">
            <div>
                <p class="text-slate-400 font-semibold uppercase text-[10px]">Tanggal Mulai</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-semibold uppercase text-[10px]">Tanggal Selesai</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $project->end_date ? $project->end_date->format('d M Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-semibold uppercase text-[10px]">Total Anggota</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $project->users->count() }} Orang</p>
            </div>
            <div>
                <p class="text-slate-400 font-semibold uppercase text-[10px]">Titik Lokasi GPS</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ $project->attendancePoints->count() }} Titik</p>
            </div>
        </div>
    </div>

    <!-- Peta Titik Geofence Projek (Leaflet API) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Peta Radar Titik Presensi Projek (Leaflet & OpenStreetMap API)</h4>
            <span class="text-xs text-slate-400 font-medium">{{ $project->attendancePoints->count() }} Titik Geofence</span>
        </div>
        <div id="project-map" class="w-full h-72 sm:h-80 rounded-xl border border-slate-200 z-10"></div>
    </div>

    <!-- Grid: Anggota Tim & Titik GPS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Team Members -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-900">Teknisi & Anggota Tim</h4>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($project->users as $member)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $member->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $member->department ?? 'Staff' }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                            {{ ucfirst($member->pivot->role_in_project ?? 'member') }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Belum ada anggota yang ditugaskan ke projek ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Attendance Points List -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-900">Daftar Titik Geofence</h4>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($project->attendancePoints as $pt)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $pt->name }}</p>
                            <p class="font-mono text-[10px] text-slate-400">{{ number_format($pt->latitude, 5) }}, {{ number_format($pt->longitude, 5) }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-50 text-primary-700">
                            Radius: {{ $pt->radius_meters }}m
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Belum ada titik presensi yang ditautkan ke projek ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const projectPoints = @json($project->attendancePoints);

        const defaultLat = projectPoints.length > 0 ? projectPoints[0].latitude : -7.2574719;
        const defaultLng = projectPoints.length > 0 ? projectPoints[0].longitude : 112.7520883;

        const map = L.map('project-map').setView([defaultLat, defaultLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (projectPoints.length > 0) {
            const markers = [];
            projectPoints.forEach(pt => {
                const circle = L.circle([pt.latitude, pt.longitude], {
                    color: '#2563eb',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.15,
                    radius: pt.radius_meters
                }).addTo(map);

                const marker = L.marker([pt.latitude, pt.longitude]).addTo(map);
                marker.bindPopup(`<b>${pt.name}</b><br>Radius: ${pt.radius_meters}m<br>${pt.address || ''}`);
                markers.push([pt.latitude, pt.longitude]);
            });

            map.fitBounds(L.latLngBounds(markers), { padding: [50, 50] });
        }
    });
</script>
@endpush
