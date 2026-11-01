@extends('layout.app')

@section('title', 'Titik Presensi GPS')
@section('page_title', 'Kelola Titik Presensi GPS (Geofence)')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Titik Lokasi Presensi (Geofence Radius)</h3>
            <p class="text-xs text-slate-500">Tentukan koordinat latitude, longitude, dan batas radius meter check-in karyawan via peta interaktif.</p>
        </div>
        <button @click="createModal = true; $nextTick(() => initModalMap())" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>Tambah Titik Presensi</span>
        </button>
    </div>

    <!-- Interactive Overview Map Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Peta Sebaran Titik Presensi (OpenStreetMap & Leaflet API)</h4>
            <span class="text-xs text-slate-400 font-medium">{{ count($points) }} Titik Terdaftar</span>
        </div>
        <div id="overview-map" class="w-full h-72 sm:h-80 rounded-xl border border-slate-200 z-10"></div>
    </div>

    <!-- Table Points -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Nama Titik & Alamat</th>
                        <th class="px-5 py-3.5">Projek Terkait</th>
                        <th class="px-5 py-3.5">Koordinat (Lat, Long)</th>
                        <th class="px-5 py-3.5">Radius Geofence</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($points as $point)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-900">{{ $point->name }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $point->address ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700">
                                {{ $point->projek?->name ?? 'Semua Projek (Umum)' }}
                            </td>
                            <td class="px-5 py-4 font-mono text-[11px] text-slate-600">
                                {{ number_format($point->latitude, 6) }}, {{ number_format($point->longitude, 6) }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[11px]">
                                    ± {{ $point->radius_meters }} meter
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $point->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $point->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('admin.attendance-points.destroy', $point) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus titik presensi ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-slate-400">
                                Belum ada titik lokasi presensi yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($points->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $points->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Titik Presensi dengan Map Picker -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.outside="createModal = false" class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-xl border border-slate-100 my-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tambah Titik Presensi Baru</h3>
                    <p class="text-[11px] text-slate-500">Klik pada peta di bawah untuk menentukan titik koordinat secara langsung.</p>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            
            <form action="{{ route('admin.attendance-points.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <!-- Interactive Map Picker -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-semibold text-slate-700">Pilih Lokasi Pada Peta *</label>
                        <button type="button" onclick="getCurrentAdminLocation()" class="text-[11px] font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                            <span>📍</span>
                            <span>Gunakan Lokasi GPS Saya</span>
                        </button>
                    </div>
                    <div id="modal-map" class="w-full h-56 rounded-xl border border-slate-300 z-10"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lokasi / Site *</label>
                        <input type="text" name="name" required placeholder="Contoh: Site Tower Alpha"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Projek Terkait (Opsional)</label>
                        <select name="projek_id" class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Semua Projek / Lokasi Umum</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Latitude *</label>
                        <input type="number" step="any" name="latitude" id="input-lat" required placeholder="-7.257471"
                            onchange="updatePickerCircle()"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Longitude *</label>
                        <input type="number" step="any" name="longitude" id="input-lng" required placeholder="112.752088"
                            onchange="updatePickerCircle()"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Radius (Meter) *</label>
                        <input type="number" name="radius_meters" id="input-radius" value="100" min="10" max="5000" required
                            oninput="updatePickerCircle()"
                            class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" id="input-address" rows="2" placeholder="Alamat jalan atau petunjuk lokasi..."
                        class="w-full rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded text-primary-600 focus:ring-primary-500">
                        <span class="font-semibold text-slate-700">Aktifkan Titik Ini</span>
                    </label>

                    <div class="flex gap-2">
                        <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white font-bold hover:bg-primary-700 shadow-xs">
                            Simpan Titik GPS
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let overviewMap, modalMap, pickerMarker, pickerCircle;

    document.addEventListener('DOMContentLoaded', function() {
        initOverviewMap();
    });

    // Overview Map showing all points from API
    function initOverviewMap() {
        overviewMap = L.map('overview-map').setView([-7.2574719, 112.7520883], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(overviewMap);

        // Fetch points from Maps API
        fetch('/api/maps/attendance-points')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    const markers = [];
                    res.data.forEach(pt => {
                        const circle = L.circle([pt.latitude, pt.longitude], {
                            color: '#2563eb',
                            fillColor: '#3b82f6',
                            fillOpacity: 0.15,
                            radius: pt.radius_meters
                        }).addTo(overviewMap);

                        const marker = L.marker([pt.latitude, pt.longitude]).addTo(overviewMap);
                        marker.bindPopup(`<b>${pt.name}</b><br>${pt.projek_name}<br>Radius: ${pt.radius_meters}m`);
                        markers.push([pt.latitude, pt.longitude]);
                    });

                    if (markers.length > 0) {
                        overviewMap.fitBounds(L.latLngBounds(markers), { padding: [40, 40] });
                    }
                }
            })
            .catch(err => console.error(err));
    }

    // Modal Map Picker
    function initModalMap() {
        if (!modalMap) {
            modalMap = L.map('modal-map').setView([-7.2574719, 112.7520883], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(modalMap);

            // Click event on map to place marker
            modalMap.on('click', function(e) {
                setPickerLocation(e.latlng.lat, e.latlng.lng);
            });
        }
        setTimeout(() => modalMap.invalidateSize(), 300);
    }

    function setPickerLocation(lat, lng) {
        document.getElementById('input-lat').value = lat.toFixed(6);
        document.getElementById('input-lng').value = lng.toFixed(6);

        if (!pickerMarker) {
            pickerMarker = L.marker([lat, lng], { draggable: true }).addTo(modalMap);
            pickerMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                setPickerLocation(pos.lat, pos.lng);
            });
        } else {
            pickerMarker.setLatLng([lat, lng]);
        }

        updatePickerCircle();

        // Reverse Geocoding API to auto-fill address
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    const addrField = document.getElementById('input-address');
                    if (addrField && !addrField.value) {
                        addrField.value = data.display_name;
                    }
                }
            });
    }

    function updatePickerCircle() {
        const lat = parseFloat(document.getElementById('input-lat').value);
        const lng = parseFloat(document.getElementById('input-lng').value);
        const radius = parseInt(document.getElementById('input-radius').value) || 100;

        if (isNaN(lat) || isNaN(lng)) return;

        if (!pickerCircle) {
            pickerCircle = L.circle([lat, lng], {
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(modalMap);
        } else {
            pickerCircle.setLatLng([lat, lng]);
            pickerCircle.setRadius(radius);
        }
    }

    function getCurrentAdminLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                modalMap.setView([lat, lng], 16);
                setPickerLocation(lat, lng);
            });
        }
    }
</script>
@endpush
