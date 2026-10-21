    <!-- ========================================== -->
    <!-- MODULAR ALPINE.JS APPLICATION LOGIC        -->
    <!-- ========================================== -->
    <script>
        function adminApp() {
            return {
                darkMode: localStorage.getItem('td_theme') === 'dark',
                sidebarCollapsed: false,
                mobileSidebarOpen: false,
                currentRole: 'admin_hr',
                activePage: 'dashboard',
                jakartaTime: '08:05:12',
                monitoringSearch: '',
                logbookFilter: 'all',
                selectedLogbookIds: [],
                toasts: [],

                // Maps & Charts instances
                employeeMap: null,
                employeeMarker: null,
                pointCircle: null,
                adminMap: null,
                adminMarker: null,
                adminCircle: null,
                trendChart: null,
                doughnutChart: null,

                // Global Confirm Modal
                confirmModal: {
                    open: false,
                    title: '',
                    message: '',
                    confirmText: 'Ya, Lanjutkan',
                    variant: 'primary',
                    onConfirm: null
                },

                // Slide-over PM Review state
                slideOver: {
                    open: false,
                    logbook: null,
                    reviewNote: '',
                    error: ''
                },

                // Simulated GPS State (Bandung Site Point: -6.902481, 107.618810, radius 200m)
                gpsState: {
                    scenario: 'inside',
                    targetLat: -6.902481,
                    targetLng: 107.618810,
                    userLat: -6.901850,
                    userLng: 107.619150,
                    distance: 80,
                    accuracy: 14,
                    permissionDenied: false
                },

                attendanceSession: {
                    state: 'not_checked_in', // not_checked_in | checked_in | completed
                    loading: false,
                    checkInTime: null,
                    checkInDistance: null,
                    checkOutTime: null,
                    durationText: ''
                },

                // Admin Attendance Point Form
                pointForm: {
                    searchQuery: '',
                    name: 'Site Bandung — Gedung Sate Tower',
                    type: 'project',
                    project: 'Implementasi Sistem Informasi ABC',
                    latitude: -6.902481,
                    longitude: 107.618810,
                    radius_m: 200
                },

                // Seeded Attendance Points
                attendancePoints: [
                    { id: 1, name: 'Kantor Pusat PT Technodrives', project: 'Kantor Pusat (Internal)', latitude: -6.229728, longitude: 106.827148, radius_m: 100, is_active: true },
                    { id: 2, name: 'Site Bandung — Gedung Sate Tower', project: 'Implementasi Sistem Informasi ABC', latitude: -6.902481, longitude: 107.618810, radius_m: 200, is_active: true },
                    { id: 3, name: 'DRC Site Sentul (Expired/Inactive)', project: 'Implementasi Sistem Informasi ABC', latitude: -6.563812, longitude: 106.854102, radius_m: 150, is_active: false }
                ],

                // Seeded Daily Monitoring Data
                monitoringRows: [
                    { id: 1, employee: 'Andi Pratama', role: 'Lead System Integrator', point: 'Site Bandung (200m)', check_in: '07:54:18', check_out: '17:05:40', distance: 80, accuracy: 14, attendance_status: 'present', logbook_status: 'submitted' },
                    { id: 2, employee: 'Budi Santoso', role: 'Backend Engineer', point: 'Site Bandung (200m)', check_in: '08:22:05', check_out: null, distance: 115, accuracy: 22, attendance_status: 'late', logbook_status: 'needs_revision' },
                    { id: 3, employee: 'Deni Kurniawan', role: 'Network Engineer', point: 'Site Bandung (200m)', check_in: '07:58:02', check_out: '17:12:11', distance: 64, accuracy: 11, attendance_status: 'present', logbook_status: 'approved' }
                ],

                // Seeded Logbooks
                logbooks: [
                    {
                        id: 101,
                        employee: 'Andi Pratama',
                        log_date: '21 Nov 2027',
                        start_time: '08:00',
                        end_time: '17:00',
                        location_name: 'Site Bandung — Ruang Server Lt. 3',
                        activity: 'Konfigurasi database cluster MySQL 8 dan pengujian replikasi antar node untuk modul autentikasi.',
                        result: 'Cluster database berjalan stabil dengan failover otomatis di bawah 3 detik.',
                        obstacle: 'Akses port firewall sempat tertutup oleh tim jaringan klien selama 45 menit.',
                        next_plan: 'Melanjutkan integrasi API Single Sign-On dengan sistem internal klien.',
                        status: 'submitted',
                        revision_count: 0,
                        history: [
                            { action: 'Draft dibuat oleh karyawan', actor: 'Andi Pratama', time: '21 Nov 2027 16:40 WIB', note: null },
                            { action: 'Logbook dikirim untuk validasi PM', actor: 'Andi Pratama', time: '21 Nov 2027 17:06 WIB', note: null }
                        ]
                    },
                    {
                        id: 102,
                        employee: 'Budi Santoso',
                        log_date: '21 Nov 2027',
                        start_time: '08:30',
                        end_time: '17:15',
                        location_name: 'Site Bandung — Ruang Rapat Utama',
                        activity: 'UAT modul pelaporan ekspor Excel dan PDF bersama user divisi operasional.',
                        result: '8 dari 10 skenario uji disetujui oleh user.',
                        obstacle: 'Format tanda tangan digital pada PDF perlu penyesuaian margin.',
                        next_plan: 'Perbaikan template DomPDF dan pengujian ulang.',
                        status: 'needs_revision',
                        revision_count: 1,
                        history: [
                            { action: 'Logbook dikirim untuk validasi PM', actor: 'Budi Santoso', time: '21 Nov 2027 17:20 WIB', note: null },
                            { action: 'Dikembalikan oleh Project Manager (Perlu Revisi)', actor: 'Rendra Wijaya (PM)', time: '21 Nov 2027 18:05 WIB', note: 'Mohon lampirkan berita acara UAT yang sudah diparaf user.' }
                        ]
                    },
                    {
                        id: 103,
                        employee: 'Deni Kurniawan',
                        log_date: '20 Nov 2027',
                        start_time: '08:00',
                        end_time: '17:10',
                        location_name: 'Site Bandung — Data Center',
                        activity: 'Pemasangan perangkat router mikrotik dan penarikan kabel UTP Cat6.',
                        result: 'Seluruh titik akses jaringan di lantai 2 dan 3 telah terhubung.',
                        obstacle: null,
                        next_plan: 'Pengujian bandwidth dan beban trafik.',
                        status: 'approved',
                        revision_count: 0,
                        history: [
                            { action: 'Logbook dikirim untuk validasi PM', actor: 'Deni Kurniawan', time: '20 Nov 2027 17:15 WIB', note: null },
                            { action: 'Disetujui oleh Project Manager', actor: 'Rendra Wijaya (PM)', time: '20 Nov 2027 19:00 WIB', note: 'Dokumentasi lengkap dan sesuai target.' }
                        ]
                    }
                ],

                calendarDays: [
                    { day: '1', status: 'present' }, { day: '2', status: 'present' }, { day: '3', status: 'late' },
                    { day: '4', status: 'present' }, { day: '5', status: 'present' }, { day: '6', status: 'weekend' }, { day: '7', status: 'weekend' },
                    { day: '8', status: 'present' }, { day: '9', status: 'incomplete' }, { day: '10', status: 'present' },
                    { day: '11', status: 'present' }, { day: '12', status: 'present' }, { day: '13', status: 'weekend' }, { day: '14', status: 'weekend' }
                ],

                navItems: [
                    { id: 'dashboard', label: 'Beranda & Analitik', icon: 'layout-dashboard', roles: ['admin_hr', 'project_manager', 'employee'] },
                    { id: 'attendance_terminal', label: 'Terminal Presensi GPS', icon: 'map-pin', roles: ['admin_hr', 'employee'], badge: 'LIVE', badgeColor: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' },
                    { id: 'logbook_review', label: 'Logbook & Validasi PM', icon: 'book-open-check', roles: ['admin_hr', 'project_manager', 'employee'], badge: '2' },
                    { id: 'attendance_points', label: 'Master Titik Presensi', icon: 'compass', roles: ['admin_hr'] },
                    { id: 'styleguide', label: 'UI Styleguide (Local)', icon: 'palette', roles: ['admin_hr', 'project_manager', 'employee'] }
                ],

                initApp() {
                    // Start live WIB Clock
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // Initialize Haversine calculation
                    this.recalculateHaversine();

                    // Initialize Charts on next tick
                    this.$nextTick(() => {
                        this.initCharts();
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                updateClock() {
                    const now = new Date();
                    this.jakartaTime = now.toLocaleTimeString('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    }).replace(/\./g, ':');
                },

                get filteredNavItems() {
                    return this.navItems.filter(item => item.roles.includes(this.currentRole));
                },

                get currentUser() {
                    if (this.currentRole === 'admin_hr') {
                        return { name: 'Siti Rahmawati', nip: 'NIP. 19900412-HR01', initials: 'SR' };
                    }
                    if (this.currentRole === 'project_manager') {
                        return { name: 'Rendra Wijaya', nip: 'NIP. 19881103-PM01', initials: 'RW' };
                    }
                    return { name: 'Andi Pratama', nip: 'NIP. 19960719-ENG1', initials: 'AP' };
                },

                get roleBadgeLabel() {
                    const map = {
                        admin_hr: 'Role: Admin / HR',
                        project_manager: 'Role: Project Manager',
                        employee: 'Role: Karyawan Proyek'
                    };
                    return map[this.currentRole];
                },

                get currentPageTitle() {
                    const found = this.navItems.find(i => i.id === this.activePage);
                    return found ? found.label : 'Dashboard';
                },

                get pendingReviewCount() {
                    return this.logbooks.filter(l => l.status === 'submitted').length;
                },

                get needsRevisionCount() {
                    return this.logbooks.filter(l => l.status === 'needs_revision').length;
                },

                get filteredMonitoring() {
                    if (!this.monitoringSearch.trim()) return this.monitoringRows;
                    const q = this.monitoringSearch.toLowerCase();
                    return this.monitoringRows.filter(r => r.employee.toLowerCase().includes(q) || r.role.toLowerCase().includes(q));
                },

                get filteredLogbooks() {
                    if (this.logbookFilter === 'all') return this.logbooks;
                    return this.logbooks.filter(l => l.status === this.logbookFilter);
                },

                get attendanceRejectionReason() {
                    if (this.gpsState.permissionDenied) {
                        return 'Izin akses lokasi ditolak oleh browser. Aktifkan GPS untuk melanjutkan.';
                    }
                    if (this.gpsState.accuracy > 100) {
                        return `Akurasi GPS terlalu rendah (±${this.gpsState.accuracy}m). Maksimal toleransi akurasi adalah 100m.`;
                    }
                    if (this.gpsState.distance > 200) {
                        return `Check-in ditolak: Jarak Anda saat ini ${this.gpsState.distance} m dari titik presensi (batas radius maksimal 200 m).`;
                    }
                    return null;
                },

                get attendanceButtonLabel() {
                    if (this.attendanceSession.state === 'not_checked_in') return '📍 Check-In Sekarang (Validasi Server)';
                    if (this.attendanceSession.state === 'checked_in') return '🏁 Check-Out & Hitung Durasi Kerja';
                    return '✓ Presensi Hari Ini Selesai';
                },

                handleRoleChange() {
                    if (this.currentRole === 'employee') {
                        this.switchPage('attendance_terminal');
                    } else if (this.currentRole === 'project_manager') {
                        this.switchPage('logbook_review');
                    } else {
                        this.switchPage('dashboard');
                    }
                    this.triggerToast('info', 'Role Beralih', `Tampilan dan hak akses disesuaikan ke ${this.roleBadgeLabel}.`);
                },

                switchPage(pageId) {
                    this.activePage = pageId;
                    this.mobileSidebarOpen = false;

                    this.$nextTick(() => {
                        if (pageId === 'attendance_terminal') {
                            this.initEmployeeMap();
                        } else if (pageId === 'attendance_points') {
                            this.initAdminPointMap();
                        } else if (pageId === 'dashboard') {
                            this.initCharts();
                        }
                    });
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('td_theme', this.darkMode ? 'dark' : 'light');
                    this.$nextTick(() => this.initCharts());
                },

                // Haversine Formula (mirroring app/Services/GeoService.php)
                calculateHaversineMeters(lat1, lon1, lat2, lon2) {
                    const R = 6371000; // Earth radius in meters
                    const toRad = deg => (deg * Math.PI) / 180;
                    const dLat = toRad(lat2 - lat1);
                    const dLon = toRad(lon2 - lon1);
                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return Math.round(R * c);
                },

                recalculateHaversine() {
                    this.gpsState.distance = this.calculateHaversineMeters(
                        this.gpsState.userLat,
                        this.gpsState.userLng,
                        this.gpsState.targetLat,
                        this.gpsState.targetLng
                    );
                },

                simulateGpsScenario(type) {
                    this.gpsState.scenario = type;
                    this.gpsState.permissionDenied = false;

                    if (type === 'inside') {
                        // ~80m away from Gedung Sate Bandung
                        this.gpsState.userLat = -6.901850;
                        this.gpsState.userLng = 107.619150;
                        this.gpsState.accuracy = 14;
                    } else if (type === 'outside') {
                        // ~750m away
                        this.gpsState.userLat = -6.908500;
                        this.gpsState.userLng = 107.622000;
                        this.gpsState.accuracy = 18;
                    } else if (type === 'poor_accuracy') {
                        this.gpsState.userLat = -6.901850;
                        this.gpsState.userLng = 107.619150;
                        this.gpsState.accuracy = 145;
                    } else if (type === 'denied') {
                        this.gpsState.permissionDenied = true;
                    }

                    this.recalculateHaversine();
                    this.updateEmployeeMapObjects();
                },

                initEmployeeMap() {
                    const container = document.getElementById('employeeAttendanceMap');
                    if (!container) return;

                    if (!this.employeeMap) {
                        this.employeeMap = L.map('employeeAttendanceMap').setView([this.gpsState.targetLat, this.gpsState.targetLng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(this.employeeMap);

                        // Target Attendance Point Marker
                        L.marker([this.gpsState.targetLat, this.gpsState.targetLng])
                            .addTo(this.employeeMap)
                            .bindPopup('<b>Titik Presensi Proyek</b><br>Site Bandung (Radius 200m)');

                        // Radius Circle
                        this.pointCircle = L.circle([this.gpsState.targetLat, this.gpsState.targetLng], {
                            radius: 200,
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.18
                        }).addTo(this.employeeMap);

                        // Employee Live Position Marker
                        this.employeeMarker = L.circleMarker([this.gpsState.userLat, this.gpsState.userLng], {
                            radius: 9,
                            color: '#ffffff',
                            weight: 3,
                            fillColor: '#2563eb',
                            fillOpacity: 1
                        }).addTo(this.employeeMap).bindPopup('Posisi GPS Anda Saat Ini');
                    }

                    setTimeout(() => {
                        this.employeeMap.invalidateSize();
                        this.updateEmployeeMapObjects();
                    }, 150);
                },

                updateEmployeeMapObjects() {
                    if (!this.employeeMap) return;
                    const inside = this.gpsState.distance <= 200 && this.gpsState.accuracy <= 100;
                    const color = inside ? '#10b981' : '#ef4444';

                    this.pointCircle.setStyle({ color, fillColor: color });
                    this.employeeMarker.setLatLng([this.gpsState.userLat, this.gpsState.userLng]);
                    this.fitEmployeeMap();
                },

                fitEmployeeMap() {
                    if (!this.employeeMap) return;
                    const bounds = L.latLngBounds([
                        [this.gpsState.targetLat, this.gpsState.targetLng],
                        [this.gpsState.userLat, this.gpsState.userLng]
                    ]);
                    this.employeeMap.fitBounds(bounds.pad(0.35));
                },

                submitAttendanceAction() {
                    if (this.attendanceRejectionReason) return;
                    this.attendanceSession.loading = true;

                    setTimeout(() => {
                        this.attendanceSession.loading = false;
                        if (this.attendanceSession.state === 'not_checked_in') {
                            this.attendanceSession.state = 'checked_in';
                            this.attendanceSession.checkInTime = this.jakartaTime + ' WIB';
                            this.attendanceSession.checkInDistance = this.gpsState.distance;
                            this.attendanceSession.durationText = 'Sedang Bekerja (0j 0m)';
                            this.triggerToast('success', 'Check-In Berhasil!', `Tercatat pada ${this.attendanceSession.checkInTime} dengan jarak ${this.gpsState.distance}m dari titik.`);
                        } else if (this.attendanceSession.state === 'checked_in') {
                            this.attendanceSession.state = 'completed';
                            this.attendanceSession.checkOutTime = this.jakartaTime + ' WIB';
                            this.attendanceSession.durationText = 'Durasi: 512 Menit (8j 32m)';
                            this.triggerToast('success', 'Check-Out Berhasil!', 'Durasi kerja hari ini (512 menit) telah disimpan.');
                        }
                    }, 500);
                },

                // Admin Interactive Map Picker
                initAdminPointMap() {
                    const container = document.getElementById('adminPointMap');
                    if (!container) return;

                    if (!this.adminMap) {
                        this.adminMap = L.map('adminPointMap').setView([this.pointForm.latitude, this.pointForm.longitude], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(this.adminMap);

                        this.adminMarker = L.marker([this.pointForm.latitude, this.pointForm.longitude], {
                            draggable: true
                        }).addTo(this.adminMap);

                        this.adminCircle = L.circle([this.pointForm.latitude, this.pointForm.longitude], {
                            radius: this.pointForm.radius_m,
                            color: '#2563eb',
                            fillColor: '#3b82f6',
                            fillOpacity: 0.2
                        }).addTo(this.adminMap);

                        // Drag marker updates inputs
                        this.adminMarker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.pointForm.latitude = Number(pos.lat.toFixed(6));
                            this.pointForm.longitude = Number(pos.lng.toFixed(6));
                            this.adminCircle.setLatLng(pos);
                        });

                        // Click map moves marker
                        this.adminMap.on('click', (e) => {
                            this.pointForm.latitude = Number(e.latlng.lat.toFixed(6));
                            this.pointForm.longitude = Number(e.latlng.lng.toFixed(6));
                            this.syncAdminMapFromInputs();
                        });
                    }

                    setTimeout(() => {
                        this.adminMap.invalidateSize();
                        this.syncAdminMapFromInputs();
                    }, 150);
                },

                syncAdminMapFromInputs() {
                    if (!this.adminMap || !this.pointForm.latitude || !this.pointForm.longitude) return;
                    const latLng = [this.pointForm.latitude, this.pointForm.longitude];
                    this.adminMarker.setLatLng(latLng);
                    this.adminCircle.setLatLng(latLng);
                    this.adminCircle.setRadius(Number(this.pointForm.radius_m) || 100);
                    this.adminMap.panTo(latLng);
                },

                async searchNominatimAddress() {
                    if (!this.pointForm.searchQuery.trim()) return;
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.pointForm.searchQuery)}&limit=1`);
                        const data = await res.json();
                        if (data && data.length > 0) {
                            this.pointForm.latitude = Number(parseFloat(data[0].lat).toFixed(6));
                            this.pointForm.longitude = Number(parseFloat(data[0].lon).toFixed(6));
                            this.syncAdminMapFromInputs();
                            this.triggerToast('success', 'Lokasi Ditemukan', data[0].display_name.slice(0, 80) + '...');
                        } else {
                            this.triggerToast('warning', 'Tidak Ditemukan', 'Coba gunakan kata kunci kota atau gedung yang lebih spesifik.');
                        }
                    } catch (e) {
                        this.triggerToast('error', 'Gagal Mencari Alamat', 'Pastikan koneksi internet aktif untuk mengakses Nominatim OSM.');
                    }
                },

                useCurrentLocationForAdminPoint() {
                    this.pointForm.latitude = -6.902481;
                    this.pointForm.longitude = 107.618810;
                    this.syncAdminMapFromInputs();
                    this.triggerToast('info', 'Koordinat Diperbarui', 'Menggunakan titik lokasi GPS perangkat saat ini.');
                },

                saveAttendancePoint() {
                    this.attendancePoints.unshift({
                        id: Date.now(),
                        name: this.pointForm.name,
                        project: this.pointForm.project,
                        latitude: Number(this.pointForm.latitude),
                        longitude: Number(this.pointForm.longitude),
                        radius_m: Number(this.pointForm.radius_m),
                        is_active: true
                    });
                    this.triggerToast('success', 'Titik Presensi Disimpan', `${this.pointForm.name} (Radius ${this.pointForm.radius_m}m) berhasil ditambahkan.`);
                },

                confirmTogglePoint(pt) {
                    this.confirmModal = {
                        open: true,
                        title: pt.is_active ? 'Nonaktifkan Titik Presensi?' : 'Aktifkan Kembali Titik Presensi?',
                        message: `Apakah Anda yakin ingin mengubah status aktif untuk "${pt.name}"? Riwayat presensi lama tetap tersimpan secara permanen.`,
                        confirmText: pt.is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
                        variant: pt.is_active ? 'danger' : 'primary',
                        onConfirm: () => {
                            pt.is_active = !pt.is_active;
                            this.triggerToast('success', 'Status Diperbarui', `Status titik ${pt.name} berhasil diubah.`);
                        }
                    };
                },

                // Logbook Inspection & PM Review Actions
                inspectLogbook(lb) {
                    this.slideOver.logbook = lb;
                    this.slideOver.reviewNote = lb.review_note || '';
                    this.slideOver.error = '';
                    this.slideOver.open = true;
                },

                approveSingleLogbook() {
                    const lb = this.slideOver.logbook;
                    if (!lb) return;
                    lb.status = 'approved';
                    lb.history.push({
                        action: 'Disetujui oleh Project Manager',
                        actor: this.currentUser.name,
                        time: 'Baru saja (' + this.jakartaTime + ' WIB)',
                        note: this.slideOver.reviewNote || 'Disetujui sesuai target harian.'
                    });
                    this.slideOver.open = false;
                    this.triggerToast('success', 'Logbook Disetujui', `Logbook milik ${lb.employee} telah berstatus Approved.`);
                },

                returnLogbookForRevision() {
                    if (!this.slideOver.reviewNote.trim()) {
                        this.slideOver.error = 'Catatan review wajib diisi saat mengembalikan logbook untuk revisi.';
                        return;
                    }
                    const lb = this.slideOver.logbook;
                    lb.status = 'needs_revision';
                    lb.revision_count += 1;
                    lb.history.push({
                        action: 'Dikembalikan untuk Revisi',
                        actor: this.currentUser.name,
                        time: 'Baru saja (' + this.jakartaTime + ' WIB)',
                        note: this.slideOver.reviewNote
                    });
                    this.slideOver.open = false;
                    this.triggerToast('warning', 'Logbook Dikembalikan', `Permintaan revisi dikirimkan ke ${lb.employee}.`);
                },

                toggleSelectAllLogbooks(e) {
                    if (e.target.checked) {
                        this.selectedLogbookIds = this.filteredLogbooks
                            .filter(l => l.status === 'submitted')
                            .map(l => l.id);
                    } else {
                        this.selectedLogbookIds = [];
                    }
                },

                bulkApproveLogbooks() {
                    const count = this.selectedLogbookIds.length;
                    this.logbooks.forEach(lb => {
                        if (this.selectedLogbookIds.includes(lb.id)) {
                            lb.status = 'approved';
                        }
                    });
                    this.selectedLogbookIds = [];
                    this.triggerToast('success', 'Bulk Approval Selesai', `${count} logbook telah disetujui sekaligus.`);
                },

                openNewLogbookModal() {
                    this.logbooks.unshift({
                        id: Date.now(),
                        employee: 'Andi Pratama',
                        log_date: '21 Nov 2027',
                        start_time: '08:00',
                        end_time: '17:00',
                        location_name: 'Site Bandung — Gedung Sate Tower',
                        activity: 'Integrasi modul GeoService Haversine dan pengujian unit test Pest.',
                        result: 'Seluruh 14 pengujian unit dan fitur lulus 100%.',
                        obstacle: null,
                        next_plan: 'Deployment ke staging server PT Technodrives.',
                        status: 'submitted',
                        revision_count: 0,
                        history: [
                            { action: 'Logbook baru dikirimkan', actor: 'Andi Pratama', time: 'Baru saja', note: null }
                        ]
                    });
                    this.triggerToast('success', 'Logbook Terkirim', 'Logbook harian baru berhasil ditambahkan ke antrean review PM.');
                },

                executeConfirm() {
                    if (typeof this.confirmModal.onConfirm === 'function') {
                        this.confirmModal.onConfirm();
                    }
                    this.confirmModal.open = false;
                },

                triggerToast(type, title, message) {
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, type, title, message, visible: true });
                    setTimeout(() => this.dismissToast(id), 4500);
                },

                dismissToast(id) {
                    const idx = this.toasts.findIndex(t => t.id === id);
                    if (idx !== -1) this.toasts.splice(idx, 1);
                },

                badgeClass(status) {
                    const map = {
                        draft: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                        submitted: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
                        approved: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                        needs_revision: 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
                        present: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                        late: 'bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-300',
                        incomplete: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'
                    };
                    return map[status] || map.draft;
                },

                badgeLabel(status) {
                    const map = {
                        draft: 'Draft',
                        submitted: 'Menunggu Review',
                        approved: 'Disetujui',
                        needs_revision: 'Perlu Revisi',
                        present: 'Hadir Tepat Waktu',
                        late: 'Terlambat',
                        incomplete: 'Belum Check-Out'
                    };
                    return map[status] || status;
                },

                getIconSvg(name) {
                    const icons = {
                        'layout-dashboard': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
                        'map-pin': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
                        'book-open-check': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 3H2v15h7c1.7 0 3 1.3 3 3V7c0-2.2-1.8-4-4-4Z"/><path d="m16 12 2 2 4-4"/><path d="M22 6V3h-6c-2.2 0-4 1.8-4 4v14c0-1.7 1.3-3 3-3h7v-2.3"/></svg>',
                        'compass': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
                        'palette': '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>'
                    };
                    return icons[name] || '';
                },

                initCharts() {
                    const textColor = this.darkMode ? '#94a3b8' : '#64748b';
                    const gridColor = this.darkMode ? 'rgba(148, 163, 184, 0.1)' : 'rgba(148, 163, 184, 0.2)';

                    const ctxTrend = document.getElementById('attendanceTrendChart');
                    if (ctxTrend) {
                        if (this.trendChart) this.trendChart.destroy();
                        this.trendChart = new Chart(ctxTrend, {
                            type: 'line',
                            data: {
                                labels: ['15 Nov', '16 Nov', '17 Nov', '18 Nov', '19 Nov', '20 Nov', '21 Nov'],
                                datasets: [
                                    {
                                        label: 'Hadir Tepat Waktu',
                                        data: [3, 3, 2, 3, 2, 3, 2],
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                        tension: 0.35,
                                        fill: true
                                    },
                                    {
                                        label: 'Terlambat',
                                        data: [0, 0, 1, 0, 1, 0, 1],
                                        borderColor: '#f59e0b',
                                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                        tension: 0.35,
                                        fill: true
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { labels: { color: textColor, font: { size: 11 } } } },
                                scales: {
                                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                                    y: { min: 0, max: 4, ticks: { stepSize: 1, color: textColor }, grid: { color: gridColor } }
                                }
                            }
                        });
                    }

                    const ctxDoughnut = document.getElementById('logbookStatusChart');
                    if (ctxDoughnut) {
                        if (this.doughnutChart) this.doughnutChart.destroy();
                        this.doughnutChart = new Chart(ctxDoughnut, {
                            type: 'doughnut',
                            data: {
                                labels: ['Disetujui', 'Menunggu Review', 'Perlu Revisi', 'Draft'],
                                datasets: [{
                                    data: [14, 4, 2, 1],
                                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#94a3b8'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '68%',
                                plugins: {
                                    legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 }, padding: 14 } }
                                }
                            }
                        });
                    }
                }
            };
        }
    </script>

    </body>
</html>