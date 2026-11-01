@extends('layout.app')

@section('title', 'Terminal Karyawan')
@section('page_title', 'Terminal Presensi GPS & Logbook')

@section('content')
<div class="space-y-6">

    <!-- Grid: Presensi Terminal & Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Presensi GPS Terminal Card -->
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Presensi Lokasi GPS</h3>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                    {{ now()->translatedFormat('d M Y') }}
                </span>
            </div>

            <!-- GPS Status Display -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span>Status Sensor:</span>
                    <span id="gps-status" class="font-mono text-emerald-600 font-semibold">Mencari sinyal GPS...</span>
                </div>
                <div class="font-mono text-[11px] text-slate-600 space-y-0.5">
                    <p>Latitude: <span id="display-lat">-</span></p>
                    <p>Longitude: <span id="display-long">-</span></p>
                </div>
                <div id="address-box" class="text-[11px] text-slate-500 pt-1 border-t border-slate-200 hidden">
                    📍 <span id="display-address" class="italic">Mendeteksi alamat...</span>
                </div>
            </div>

            <!-- Geofence Validation Alert (API Response) -->
            <div id="geofence-alert" class="p-3.5 rounded-xl text-xs font-medium hidden">
                <p id="geofence-message"></p>
            </div>

            <!-- Action Forms -->
            @if(!$todayAttendance || !$todayAttendance->check_in_time)
                {{-- Check In Form --}}
                <form action="{{ route('karyawan.attendance.check-in') }}" method="POST" id="form-checkin" class="space-y-3">
                    @csrf
                    <input type="hidden" name="latitude" id="checkin-lat">
                    <input type="hidden" name="longitude" id="checkin-long">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Titik Lokasi Tugas *</label>
                        <select name="attendance_point_id" id="select-attendance-point" onchange="onPointSelected()" class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Pilih Titik Lokasi / Geofence</option>
                            @foreach($assignedProjects as $projek)
                                @foreach($projek->attendancePoints as $point)
                                    <option value="{{ $point->id }}" data-lat="{{ $point->latitude }}" data-lng="{{ $point->longitude }}" data-radius="{{ $point->radius_meters }}">
                                        {{ $point->name }} (Radius: {{ $point->radius_meters }}m)
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="btn-checkin" disabled
                        class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-sm shadow-sm transition">
                        📍 Check-In Masuk
                    </button>
                </form>
            @elseif(!$todayAttendance->check_out_time)
                {{-- Check Out Form --}}
                <div class="p-3 bg-emerald-50 rounded-xl text-xs text-emerald-800 border border-emerald-100">
                    ✓ Check-In berhasil tercatat pada <strong>{{ $todayAttendance->check_in_time }} WIB</strong>
                </div>

                <form action="{{ route('karyawan.attendance.check-out') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="latitude" id="checkout-lat">
                    <input type="hidden" name="longitude" id="checkout-long">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pulang (Opsional)</label>
                        <input type="text" name="notes" placeholder="Contoh: Pekerjaan selesai tepat waktu"
                            class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <button type="submit" id="btn-checkout" disabled
                        class="w-full py-3 rounded-xl bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white font-bold text-sm shadow-sm transition">
                        🏁 Check-Out Pulang
                    </button>
                </form>
            @else
                <div class="p-4 bg-emerald-50 rounded-xl text-xs text-emerald-800 border border-emerald-200 text-center font-medium">
                    🎉 Anda telah menyelesaikan presensi hari ini.<br>
                    Masuk: <strong>{{ $todayAttendance->check_in_time }}</strong> | Pulang: <strong>{{ $todayAttendance->check_out_time }}</strong>
                </div>
            @endif
        </div>

        <!-- Interactive Map API Display -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Peta Radar Presensi (Leaflet & OpenStreetMap API)</h3>
                    <p class="text-xs text-slate-500">Visualisasi posisi GPS Anda dan radius zona presensi yang ditentukan.</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-blue-500"></span> Posisi Anda</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-emerald-500"></span> Titik Geofence</span>
                </div>
            </div>

            <!-- Leaflet Map Container -->
            <div id="map-terminal" class="w-full h-80 sm:h-96 rounded-xl border border-slate-200 shadow-inner z-10"></div>
        </div>
    </div>

    <!-- Logbook Form & List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah Logbook -->
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Input Logbook Kegiatan Harian</h3>
            
            <form action="{{ route('karyawan.logbooks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Projek Terkait</label>
                    <select name="projek_id" class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Projek</option>
                        @foreach($assignedProjects as $projek)
                            <option value="{{ $projek->id }}">{{ $projek->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kegiatan</label>
                    <input type="date" name="date" value="{{ now()->toDateString() }}" required
                        class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Aktivitas</label>
                    <input type="text" name="title" required placeholder="Contoh: Kalibrasi sensor & pengujian GPS"
                        class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rincian Hasil Pekerjaan</label>
                    <textarea name="description" rows="3" required placeholder="Tuliskan detail pekerjaan dan kendala..."
                        class="w-full rounded-xl border border-slate-300 text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Progres: <span id="progress-val" class="font-bold text-primary-600">50%</span></label>
                    <input type="range" name="progress_percentage" min="0" max="100" value="50" oninput="document.getElementById('progress-val').innerText = this.value + '%'"
                        class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-xs transition">
                    Simpan & Ajukan Logbook
                </button>
            </form>
        </div>

        <!-- Riwayat Logbook -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Riwayat Logbook Terbaru</h3>
                <span class="text-xs font-medium text-slate-500">{{ count($recentLogbooks) }} kegiatan</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                @forelse($recentLogbooks as $logbook)
                    <div class="p-5 flex items-start justify-between gap-4 hover:bg-slate-50/50 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold 
                                    @if($logbook->status === 'approved') bg-emerald-100 text-emerald-700 
                                    @elseif($logbook->status === 'rejected') bg-rose-100 text-rose-700 
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($logbook->status) }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $logbook->date->format('d M Y') }}</span>
                                <span class="text-xs font-semibold text-primary-600">{{ $logbook->projek?->name ?? 'Projek Umum' }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $logbook->title }}</h4>
                            <p class="text-xs text-slate-600">{{ $logbook->description }}</p>
                            @if($logbook->review_notes)
                                <div class="mt-2 p-2.5 rounded-lg bg-amber-50 text-amber-800 text-[11px] border border-amber-200">
                                    Catatan Review: {{ $logbook->review_notes }}
                                </div>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-bold text-slate-700">{{ $logbook->progress_percentage }}%</span>
                            <p class="text-[10px] text-slate-400">Progres</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        Belum ada riwayat logbook kegiatan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    let map, userMarker, userCircle, pointMarker, geofenceCircle, polyline;
    let currentUserLat = null, currentUserLng = null;

    // Initialize Leaflet Map with OpenStreetMap Tile API
    function initMap() {
        const defaultLat = -7.2574719;
        const defaultLng = 112.7520883;

        map = L.map('map-terminal').setView([defaultLat, defaultLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Fetch registered attendance points from API
        fetchPointsFromApi();
    }

    // Call internal Maps API to fetch all active attendance points
    function fetchPointsFromApi() {
        fetch('/api/maps/attendance-points')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    res.data.forEach(pt => {
                        // Add marker & circle for each attendance point
                        const circle = L.circle([pt.latitude, pt.longitude], {
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.15,
                            radius: pt.radius_meters
                        }).addTo(map);

                        const marker = L.marker([pt.latitude, pt.longitude]).addTo(map);
                        marker.bindPopup(`<b>${pt.name}</b><br>${pt.projek_name}<br>Radius: ${pt.radius_meters}m`);
                    });
                }
            })
            .catch(err => console.error('Gagal mengambil titik GPS:', err));
    }

    // Reverse Geocoding API using OpenStreetMap Nominatim
    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('address-box').classList.remove('hidden');
                    document.getElementById('display-address').innerText = data.display_name;
                }
            })
            .catch(err => console.log('Geocoding error:', err));
    }

    // Handle when user selects an Attendance Point from the dropdown
    function onPointSelected() {
        const select = document.getElementById('select-attendance-point');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption.value || !currentUserLat || !currentUserLng) {
            return;
        }

        const pointLat = parseFloat(selectedOption.getAttribute('data-lat'));
        const pointLng = parseFloat(selectedOption.getAttribute('data-lng'));
        const radius = parseInt(selectedOption.getAttribute('data-radius'));

        // Call Maps Validation API to check radius via backend Haversine formula
        fetch('/api/maps/validate-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                latitude: currentUserLat,
                longitude: currentUserLng,
                attendance_point_id: selectedOption.value
            })
        })
        .then(res => res.json())
        .then(res => {
            const alertBox = document.getElementById('geofence-alert');
            const alertMsg = document.getElementById('geofence-message');
            alertBox.classList.remove('hidden');

            if (res.is_within_radius) {
                alertBox.className = 'p-3.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 block';
                alertMsg.innerHTML = `✅ ${res.message}`;
                document.getElementById('btn-checkin').disabled = false;
            } else {
                alertBox.className = 'p-3.5 rounded-xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200 block';
                alertMsg.innerHTML = `⚠️ ${res.message}`;
            }

            // Draw connecting line on map
            if (polyline) map.removeLayer(polyline);
            polyline = L.polyline([
                [currentUserLat, currentUserLng],
                [pointLat, pointLng]
            ], { color: res.is_within_radius ? '#10b981' : '#f43f5e', dashArray: '5, 10' }).addTo(map);

            // Fit map bounds to show both user and destination
            const bounds = L.latLngBounds([
                [currentUserLat, currentUserLng],
                [pointLat, pointLng]
            ]);
            map.fitBounds(bounds, { padding: [50, 50] });
        })
        .catch(err => console.error('Validation error:', err));
    }

    // HTML5 Geolocation API with live tracking
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentUserLat = position.coords.latitude;
                    currentUserLng = position.coords.longitude;
                    const accuracy = Math.round(position.coords.accuracy);

                    document.getElementById('display-lat').innerText = currentUserLat.toFixed(6);
                    document.getElementById('display-long').innerText = currentUserLng.toFixed(6);
                    document.getElementById('gps-status').innerText = `GPS Aktif (±${accuracy}m)`;

                    const checkinLat = document.getElementById('checkin-lat');
                    if (checkinLat) {
                        checkinLat.value = currentUserLat;
                        document.getElementById('checkin-long').value = currentUserLng;
                        document.getElementById('btn-checkin').disabled = false;
                    }

                    const checkoutLat = document.getElementById('checkout-lat');
                    if (checkoutLat) {
                        checkoutLat.value = currentUserLat;
                        document.getElementById('checkout-long').value = currentUserLng;
                        document.getElementById('btn-checkout').disabled = false;
                    }

                    // Update Map View
                    map.setView([currentUserLat, currentUserLng], 16);

                    // Add User Marker
                    if (userMarker) map.removeLayer(userMarker);
                    userMarker = L.circleMarker([currentUserLat, currentUserLng], {
                        radius: 8,
                        fillColor: '#3b82f6',
                        color: '#ffffff',
                        weight: 3,
                        opacity: 1,
                        fillOpacity: 1
                    }).addTo(map);
                    userMarker.bindPopup('<b>Posisi Anda Saat Ini</b>').openPopup();

                    // Accuracy circle
                    if (userCircle) map.removeLayer(userCircle);
                    userCircle = L.circle([currentUserLat, currentUserLng], {
                        radius: accuracy,
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.1
                    }).addTo(map);

                    // Reverse Geocoding Address API
                    reverseGeocode(currentUserLat, currentUserLng);
                },
                function(error) {
                    document.getElementById('gps-status').innerText = 'Gagal mengakses GPS: ' + error.message;
                    document.getElementById('gps-status').className = 'text-rose-600 font-semibold';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            document.getElementById('gps-status').innerText = 'Browser tidak mendukung GPS';
        }
    });
</script>
@endpush
