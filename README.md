# 📍 PT Technodrives — Sistem Presensi Berbasis GPS & Logbook Kegiatan

Aplikasi web modern berbasis **Laravel 12** dan **PHP 8.3** yang dirancang untuk mengelola presensi karyawan lapangan menggunakan validasi koordinat GPS (Geofencing Radius) serta pelaporan dan validasi logbook kegiatan harian secara real-time.

---

## 📑 Daftar Isi

- [Arsitektur & Teknologi](#-arsitektur--teknologi)
- [Peran & Hak Akses Pengguna (RBAC)](#-peran--hak-akses-pengguna-rbac)
- [Struktur Folder & Penjelasan Modul](#-struktur-folder--penjelasan-modul)
- [Fitur Utama & Alur Kerja](#-fitur-utama--alur-kerja)
- [Dokumentasi API Maps & Geofencing](#-dokumentasi-api-maps--geofencing)
- [Panduan Instalasi & Menjalankan Proyek](#-panduan-instalasi--menjalankan-proyek)
- [Akun Demo untuk Pengujian](#-akun-demo-untuk-pengujian)
- [Pengujian Otomatis (Testing)](#-pengujian-otomatis-testing)

---

## 🚀 Arsitektur & Teknologi

- **Backend Framework**: [Laravel 12](https://laravel.com/) (menggunakan arsitektur slim Laravel 12 tanpa kernel lama).
- **Bahasa Pemrograman**: PHP 8.3+.
- **Database ORM**: Eloquent ORM dengan relasi lengkap dan audit trail history.
- **Frontend Template**: Laravel Blade Template dengan layout modular [`resources/views/layout/app.blade.php`](resources/views/layout/app.blade.php).
- **CSS Utility**: [Tailwind CSS](https://tailwindcss.com/) responsif untuk desktop dan mobile.
- **Interaktivitas Frontend**: [Alpine.js](https://alpinejs.dev/) untuk modal dialog dan reaktivitas komponen.
- **Peta & Geolocation**:
  - [Leaflet JS](https://leafletjs.com/) & OpenStreetMap Tile Layer API.
  - HTML5 Geolocation API (deteksi sensor GPS presisi tinggi).
  - OpenStreetMap Nominatim Reverse Geocoding API (konversi otomatis koordinat ke nama jalan/alamat).
- **Autentikasi**: Laravel Session Authentication dengan proteksi Brute Force Rate Limiter.
- **Kualitas Kode**: Laravel Pint & PHPUnit (100% test passing).

---

## 👥 Peran & Hak Akses Pengguna (RBAC)

Sistem menerapkan **Role-Based Access Control (RBAC)** melalui [`RoleMiddleware`](app/Http/Middleware/RoleMiddleware.php) yang didaftarkan di [`bootstrap/app.php`](bootstrap/app.php):

| Role | Akses Rute | Tanggung Jawab Utama |
| :--- | :--- | :--- |
| **🛡️ Administrator** | `/admin/*` | Mengelola master projek, titik koordinat GPS (geofence), master data pengguna/karyawan, penugasan tim ke projek, dan rekapitulasi laporan presensi & logbook. |
| **📋 Project Manager** | `/manager/*` | Memantau sebaran dan progres projek lapangan, melihat status presensi tim, serta mereview / mengevaluasi logbook teknisi (Setujui / Tolak dengan catatan revisi). |
| **📍 Karyawan / Teknisi** | `/karyawan/*` | Melakukan presensi masuk & pulang dengan validasi radius GPS di lokasi tugas, input logbook harian, dan memantau riwayat persetujuan pekerjaan. |

---

## 📂 Struktur Folder & Penjelasan Modul

Berikut struktur direktori utama pada proyek ini:

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                   # Controller khusus modul Administrator
│   │   │   │   ├── AssignmentController.php       # Penugasan anggota ke projek
│   │   │   │   ├── AttendancePointController.php  # CRUD titik lokasi GPS & geofence
│   │   │   │   ├── EmployeeController.php         # CRUD pengguna & role akses
│   │   │   │   └── ProjectController.php          # CRUD data kontrak projek
│   │   │   ├── Api/                     # REST API Controller
│   │   │   │   └── MapController.php              # API titik GPS & kalkulasi radius Haversine
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php             # Login session & logout terproteksi
│   │   │   ├── Employee/                # Controller untuk teknisi lapangan
│   │   │   │   ├── AttendanceController.php       # Check-In & Check-Out berbasis GPS
│   │   │   │   └── LogbookController.php          # Pengajuan logbook kegiatan harian
│   │   │   ├── Manager/                 # Controller untuk Project Manager
│   │   │   │   ├── LogbookReviewController.php    # Validasi & penolakan revisi logbook
│   │   │   │   └── ProjectMonitoringController.php# Monitoring projek & sebaran titik tugas
│   │   │   ├── DashboardController.php  # Agregasi data dashboard setiap role
│   │   │   └── ReportController.php     # Rekapitulasi laporan presensi & logbook
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php       # Middleware pemfilter hak akses role (403 Forbidden)
│   │   └── Requests/                    # Form Request untuk isolasi validasi input
│   │       ├── Admin/StoreProjekRequest.php
│   │       ├── Attendance/AttendanceCheckInRequest.php
│   │       ├── Auth/LoginRequest.php
│   │       └── Logbook/StoreLogbookRequest.php
│   └── Models/                          # Model Eloquent ORM
│       ├── Attendance.php               # Data kehadiran, jam masuk/pulang, titik GPS
│       ├── AttendancePoint.php          # Titik geofence: latitude, longitude, radius (m)
│       ├── Logbook.php                  # Judul aktivitas, deskripsi, progres (%), status
│       ├── LogbookAttachment.php        # Berkas lampiran laporan logbook
│       ├── LogbookHistory.php           # Audit trail riwayat status logbook
│       ├── ProjectAssignment.php        # Pivot penugasan user ke projek
│       ├── Projek.php                   # Data master proyek
│       └── User.php                     # Pengguna sistem dengan helper role
│
├── bootstrap/
│   └── app.php                          # Konfigurasi routing, middleware alias, dan exception
│
├── database/
│   ├── factories/                       # Factory data acak untuk testing & seeder
│   ├── migrations/                      # Skema migrasi database relasional
│   └── seeders/
│       └── DatabaseSeeder.php           # Data inisialisasi awal akun dan projek contoh
│
├── resources/
│   └── views/                           # Frontend Blade Templates
│       ├── landing.blade.php            # Landing Page Publik Interaktif (Alpine.js, Radar Simulator)
│       ├── layout/
│       │   └── app.blade.php            # Master Shell: Sidebar, Topbar, Flash Message, Profil
│       ├── auth/
│       │   └── login.blade.php          # Halaman login modern dengan tombol instan demo
│       ├── admin/                       # Tampilan admin (Dashboard, CRUD Projek, Karyawan, GPS)
│       ├── manager/                     # Tampilan PM (Monitoring & Approval Logbook)
│       └── karyawan/                    # Tampilan Teknisi (Radar GPS Presensi & Form Logbook)
│
├── routes/
│   ├── web.php                          # Rute antarmuka pengguna berbasis session & middleware
│   ├── api.php                          # Endpoint REST API untuk Peta & validasi Geofence
│   └── console.php                      # Artisan command routes
│
└── tests/
    └── Feature/                         # Automated Feature Tests (32 skenario pengujian)
```

---

## 🛠️ Fitur Utama & Alur Kerja

### 1. Presensi Berbasis Radius Lokasi (Geofencing GPS)
- Karyawan membuka dashboard presensi. Sensor GPS browser membaca koordinat posisi secara presisi.
- Koordinat otomatis di-reverse geocode melalui OpenStreetMap API untuk menampilkan alamat jalan saat ini.
- Karyawan memilih titik tugas proyek. Peta menampilkan visualisasi posisi karyawan, titik pusat lokasi tugas, dan lingkaran radius geofence.
- Sistem memanggil API kalkulasi jarak. Jika posisi berada **di dalam radius toleransi** (misal 100 meter), tombol Check-In aktif. Jika di luar radius, tombol dicegah dan menampilkan notifikasi jarak.

### 2. Logbook Kegiatan & Validasi Bertingkat (Approval Workflow)
- Karyawan mengisi laporan aktivitas pekerjaan harian, persentase progres, dan kendala lapangan.
- Logbook masuk dalam antrean dengan status `submitted`.
- Project Manager meninjau daftar logbook:
  - **Setujui (`approved`)**: Pekerjaan diterima dan dicatat ke riwayat proyek.
  - **Tolak / Minta Revisi (`rejected`)**: PM wajib menyertakan catatan evaluasi agar teknisi mengetahui bagian yang harus diperbaiki.
- Setiap perpindahan status tercatat otomatis pada tabel `logbook_histories` (audit trail).

---

## 🗺️ Dokumentasi API Maps & Geofencing

Aplikasi menyediakan endpoint REST API publik/internal untuk integrasi peta:

### 1. Ambil Seluruh Titik Presensi
```http
GET /api/maps/attendance-points
```
**Parameter Query (Opsional)**: `?projek_id=1`  
**Contoh Respon**:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Head Office & Data Center Surabaya",
      "projek_id": 1,
      "projek_name": "Implementasi Jaringan Fiber Optik Surabaya",
      "projek_code": "PRJ-2026-001",
      "latitude": -7.2574719,
      "longitude": 112.7520883,
      "radius_meters": 150,
      "address": "Jl. Pemuda No. 45, Genteng, Surabaya, Jawa Timur"
    }
  ]
}
```

### 2. Validasi Radius Lokasi Pengguna
```http
POST /api/maps/validate-location
Content-Type: application/json
```
**Payload Request**:
```json
{
  "latitude": -7.2575000,
  "longitude": 112.7521000,
  "attendance_point_id": 1
}
```
**Respon**:
```json
{
  "status": "success",
  "is_within_radius": true,
  "distance_meters": 3.39,
  "radius_meters": 150,
  "message": "Posisi Anda berada di dalam zona radius presensi (3.4m dari titik pusat).",
  "point": {
    "id": 1,
    "name": "Head Office & Data Center Surabaya",
    "latitude": -7.2574719,
    "longitude": 112.7520883,
    "radius_meters": 150
  }
}
```

---

## 💻 Panduan Instalasi & Menjalankan Proyek

### Prasyarat
- PHP >= 8.3
- Composer >= 2.0
- SQLite atau MySQL/MariaDB

### Langkah Instalasi

1. **Clone repositori**:
   ```bash
   git clone <url-repositori-anda>
   cd Presensi-berbasis-Gps
   ```

2. **Pasang dependensi Composer**:
   ```bash
   composer install
   ```

3. **Salin file lingkungan (.env)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Jalankan Migrasi Database & Seeder Awal**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser pada: `http://localhost:8000`

---

## 🔑 Akun Demo untuk Pengujian

Database Seeder telah menyiapkan 3 akun pengujian dengan hak akses berbeda:

| Role | Alamat Email | Password | URL Dashboard |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@technodrives.com` | `password` | `http://localhost:8000/admin` |
| **Project Manager** | `manager@technodrives.com` | `password` | `http://localhost:8000/manager` |
| **Karyawan / Teknisi** | `karyawan@technodrives.com` | `password` | `http://localhost:8000/karyawan` |

*(Pada halaman login tersedia tombol cepat untuk mengisi kredensial masing-masing role secara otomatis).*

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini telah dilengkapi dengan rangkaian pengujian fitur otomatis menyeluruh (*automated feature tests*) menggunakan PHPUnit:

```bash
php artisan test
```

### Rangkuman Cakupan Uji:
- **`AuthTest`**: Pengujian autentikasi session, rate limiter brute force, proteksi akun nonaktif, logout, dan redirection berbasis peran.
- **`RoleMiddlewareTest`**: Pengujian proteksi isolasi rute (memastikan status 200 untuk peran berhak dan 403 Forbidden untuk peran tidak sah).
- **`AttendanceTest`**: Pengujian alur check-in/out GPS, pencegahan check-in ganda di hari yang sama, dan validasi koordinat sensor.
- **`LogbookTest`**: Pengujian pengajuan logbook oleh teknisi serta aksi persetujuan dan penolakan revisi oleh manager.
- **`ProjectManagementTest`**: Pengujian CRUD proyek dan pendaftaran titik koordinat geofence oleh admin.
- **`MapApiTest`**: Pengujian endpoint REST API peta dan formula kalkulasi jarak Haversine.
- **`ViewRenderingTest`**: Pengujian rendering seluruh view Blade agar bebas dari galat sintaks atau variabel tak terdefinisi.

### Pemformatan Kode (Style Guide):
Untuk memastikan standar kode Laravel yang konsisten:
```bash
vendor/bin/pint
```
