 # 🎓 ASPRAK — Sistem Manajemen Praktikum & Penilaian Terpadu (Laravel + PostgreSQL)

Aplikasi Web Manajemen Praktikum dan Penilaian Terpadu yang telah dimigrasi dari PHP Native MVC ke **Laravel 11/12** dengan engine basis data enterprise **PostgreSQL**.

---

## 🏛️ Arsitektur & Teknologi

| Komponen | Spesifikasi | Keterangan |
|---|---|---|
| **Backend Framework** | Laravel 11/12 (PHP 8.3+) | Modern Architecture, Eloquent ORM, Service Layer |
| **Database Engine** | PostgreSQL 12+ / 14 / 15 / 16 | ACID-Compliant, Strict Data Types, Sequences, Cascade Rules |
| **Frontend UI** | Blade + Tailwind CSS (Dark Theme) | Custom Theme (`#ffcc00` Gold Accent, Plus Jakarta Sans) |
| **Authentication** | Session-Based via Custom Auth & Middleware | Multi-role (`dosen`, `asisten_dosen`, `mahasiswa`) |
| **File Storage** | Public Uploads Storage (`uploads/modul`, `uploads/tugas`) | Mendukung PDF, ZIP, RAR, JPG, PNG |

---

## 📋 Fitur Berdasarkan Peran Pengguna

### 1. Dosen Pengampu (`dosen`)
- **Dashboard Kelas**: Membuat kelas praktikum baru, melihat daftar kelas aktif, dan generate kode gabung otomatis.
- **Verifikasi Asprak**: Menyetujui (*ACC*) atau menolak permohonan asisten dosen untuk mengajar di kelas.
- **Monitoring Asdos & Kelas**:
  - Pelacakan progress koreksi tugas oleh asdos secara visual (*progress bar*).
  - Matriks rekapitulasi nilai komprehensif seluruh mahasiswa (Absen, Tugas, Quiz, UTS, UAS, Nilai Akhir, Grade Huruf, Bobot).
  - Catatan Supervisi: Mengirim arahan/catatan evaluasi langsung kepada asdos dengan status tanggapan real-time.
  - Log Audit Aktivitas: Memantau rekaman jejak digital aksi asisten dosen di kelas.

### 2. Asisten Praktikum (`asisten_dosen`)
- **Pengajuan Gabung**: Bergabung ke kelas dosen menggunakan kode gabung unik dengan status menunggu verifikasi (*pending*).
- **Konfigurasi Bobot Penilaian**: Menyesuaikan persentase komponen penilaian (Absen + Tugas + Quiz + UTS + UAS = 100%) dengan validasi otomatis.
- **Input & Perhitungan Nilai Otomatis**: Form kalkulasi otomatis nilai akhir mahasiswa, grade huruf (A, B+, B, C+, C, D, E), dan bobot IP (4.00 - 0.00).
- **Manajemen Forum Kelas**: Menerbitkan materi modul praktikum atau tugas berbatas waktu (*deadline*).
- **Koreksi & Feedback Tugas**: Mengunduh submission file mahasiswa, memberi nilai angka (0-100), dan catatan perbaikan.
- **Tanggapan Supervisi**: Membalas catatan supervisi yang diberikan oleh dosen pengampu.

### 3. Mahasiswa (`mahasiswa`)
- **Pendaftaran Kelas**: Memasukkan kode gabung kelas praktikum untuk langsung terdaftar sebagai peserta aktif.
- **Akses Materi & Pengumpulan Tugas**:
  - Mengunduh materi modul perkuliahan (.pdf).
  - Mengunggah file tugas (*upload submission*) dengan deteksi otomatis status pengumpulan: **Tepat Waktu** atau **Terlambat**.
- **Forum Diskusi**: Mengikuti diskusi di postingan kelas dan memberikan komentar.
- **Transkrip & KHS Praktikum**: Melihat rincian capaian nilai (Absensi, Tugas, Quiz, UTS, UAS, Nilai Akhir Angka, Nilai Huruf, dan Bobot Mutu) beserta status kelulusan.

---

## 🗄️ Perbandingan Skema Basis Data: MySQL vs PostgreSQL

| Fitur | Implementasi MySQL Lama | Optimasi PostgreSQL Baru | Manfaat / Alasan Arsitektural |
|---|---|---|---|
| **Primary Key ID** | `INT AUTO_INCREMENT` | `BIGSERIAL` / Sequence | Mencegah *overflow* kapasitas ID pada skala data besar. |
| **Sequence Sync** | Otomatis di server | `setval('table_col_seq', MAX(col))` | Menghindari konflik duplikasi ID saat data disuntikkan secara manual/seeder. |
| **Tipe Nilai Akademik** | `FLOAT` | `NUMERIC(5,2)` | Menghilangkan *floating-point rounding error* (misal `79.999999` vs `80.00`). |
| **Enum / Role Constraints** | `ENUM(...)` | `VARCHAR(20) CHECK (role IN (...))` | Standar ANSI SQL yang portabel dan mudah diperluas tanpa *DDL table rebuild lock*. |
| **Integritas Relasi** | Standar FK | `FOREIGN KEY ... ON DELETE CASCADE` | Membersihkan data anak secara atomik saat data induk dihapus (mencegah *orphaned records*). |
| **Upsert Handling** | `ON DUPLICATE KEY UPDATE` | Eloquent `updateOrCreate()` | Abstraksi level aplikasi yang aman dari *race condition* lintas DBMS. |

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
- PHP >= 8.3 (dengan ekstensi `pdo_pgsql`, `pgsql`, `fileinfo`, `mbstring`)
- PostgreSQL Server 12+ (sudah tersedia di Laragon: `C:\laragon\bin\postgresql`)
- Composer (manajer paket PHP)

### 2. Konfigurasi Environment (`.env`)
Pastikan file `.env` di folder `laravel-asprak` telah terkonfigurasi dengan kredensial PostgreSQL Anda:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_penilaian
DB_USERNAME=postgres
DB_PASSWORD=root
```
*(Catatan: Sesuaikan `DB_PASSWORD` dengan password user `postgres` di Laragon / sistem Anda, default laragon biasanya kosong atau `root`)*.

### 3. Membuat Basis Data & Menjalankan Migrasi
Buka terminal PowerShell di folder `laravel-asprak/`:

```powershell
# Jalankan migrasi dan isi data awal otomatis (Seeder)
php artisan migrate:fresh --seed
```

Atau jika ingin mengimpor skrip SQL murni PostgreSQL secara langsung:
```powershell
& "C:\laragon\bin\postgresql\postgresql-14.5-1\bin\psql.exe" -U postgres -d db_penilaian -f ../db_penilaian_postgresql.sql
```

### 4. Menjalankan Server Lokal
```powershell
php artisan serve
```
Akses aplikasi melalui peramban: `http://localhost:8000`

---

## 🔑 Kredensial Akun Bawaan (Demo Logins)

Untuk kemudahan pengujian, halaman Login telah dilengkapi fitur **1-Click Demo Login** untuk setiap peran:

| Peran | Username | Password | Nama Lengkap |
|---|---|---|---|
| **Dosen** | `dosen1` | `admin` | Dr. Hendra, M.Kom. |
| **Asisten Dosen** | `asprak1` | `asprak` | Rian Hidayat (Asprak) |
| **Mahasiswa** | `230101001` | `mhs` | Budi Santoso |

---

## 📁 Struktur Direktori Laravel

```
laravel-asprak/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Autentikasi Login, Register, Logout
│   │   │   ├── DosenController.php       # Dashboard Dosen, ACC Asdos, Supervisi, Monitoring
│   │   │   ├── AsprakController.php      # Dashboard Asdos, Gabung Kelas, Penilaian, Bobot
│   │   │   ├── MahasiswaController.php   # Dashboard Mahasiswa, Gabung Kelas, Transkrip Nilai
│   │   │   └── ForumController.php       # Forum Kelas, Posting, Komentar, Upload Tugas, Koreksi
│   │   └── Middleware/
│   │       └── RoleMiddleware.php        # Multi-role route guard
│   └── Models/                           # 16 Model Eloquent dengan relasi lengkap
├── database/
│   ├── migrations/                       # 10 File skrip migrasi schema PostgreSQL
│   └── seeders/
│       └── DatabaseSeeder.php            # Seeder data awal & sinkronisasi Sequence PostgreSQL
├── resources/
│   └── views/
│       ├── layouts/app.blade.php         # Layout tema gelap (Tailwind CSS, Navbar, Modal & Toast)
│       ├── auth/                         # View Login & Registrasi
│       ├── dosen/                        # View Dashboard & Monitoring Dosen
│       ├── asprak/                       # View Dashboard & Penilaian Asdos
│       ├── mahasiswa/                    # View Dashboard & KHS Mahasiswa
│       └── forum/                        # View Forum Diskusi & Detail Koreksi Tugas
├── public/
│   ├── assets/                           # Logo, maskot (.png, .jpg), dan styling
│   └── uploads/                          # Direktori berkas unggahan modul dan tugas mahasiswa
└── routes/
    └── web.php                           # 32 Rute web terstruktur dengan role protection
```
