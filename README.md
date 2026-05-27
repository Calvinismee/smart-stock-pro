# SmartStock Pro

SmartStock Pro adalah aplikasi web manajemen inventaris untuk mengelola produk, stok per gudang, transaksi barang masuk/keluar, transfer antar gudang, laporan, notifikasi stok minimum, dan audit log.

Aplikasi ini dikembangkan untuk menggantikan proses pencatatan stok manual berbasis spreadsheet menjadi sistem inventaris berbasis web yang lebih terpusat, aman, dan mudah dipantau.

## Table of Contents

- [Fitur Utama](#fitur-utama)
- [Role Pengguna](#role-pengguna)
- [Aturan Utama Sistem](#aturan-utama-sistem)
- [Tech Stack](#tech-stack)
- [System Requirement](#system-requirement)
- [Instalasi](#instalasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Demo](#akun-demo)
- [Command Penting](#command-penting)
- [Struktur Folder](#struktur-folder)
- [Dokumentasi Proyek](#dokumentasi-proyek)
- [Catatan Penggunaan](#catatan-penggunaan)
- [Pengembang](#pengembang)
- [Lisensi](#lisensi)

## Fitur Utama

- Login multi-role
- Dashboard inventaris
- My Warehouse untuk Staf Gudang
- Master produk global
- Monitoring stok per produk per gudang
- CRUD produk, kategori, gudang, supplier, dan user
- Transaksi barang masuk dan barang keluar
- Transfer barang antar gudang
- Alert stok minimum dan notifikasi aplikasi
- Import CSV/Excel dengan background job
- Export laporan
- Audit log dan error log
- FIFO stock batch
- Peta lokasi gudang

## Role Pengguna

| Role | Deskripsi |
|---|---|
| Admin | Mengelola seluruh sistem, user, master data, transaksi, laporan, audit log, dan error log. |
| Manajer Gudang | Mengelola dan memantau inventaris seluruh gudang. |
| Staf Gudang | Melakukan operasional barang masuk, barang keluar, dan transfer dari gudang yang ditugaskan. |
| Viewer | Melihat dashboard, produk, stok, laporan, dan peta gudang secara read-only. |

## Aturan Utama Sistem

- Produk bersifat global dan tidak dimiliki oleh satu gudang tertentu.
- Kuantitas stok produk disimpan per gudang melalui `inventory_stocks`.
- Staf Gudang hanya dapat mengelola stok pada gudang yang ditugaskan.
- Viewer hanya memiliki akses read-only.
- Barang keluar dan transfer menggunakan mekanisme FIFO melalui `stock_batches`.
- Import data diproses melalui queue/background job.

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Frontend | Inertia.js + React |
| Styling | Tailwind CSS |
| Database | PostgreSQL |
| Authentication | Laravel Session Auth |
| Queue | Laravel Queue |
| Chart | Recharts |
| Map | Leaflet / React Leaflet |
| Import/Export | Laravel Excel |
| Monitoring | Laravel Pulse |
| Build Tool | Vite |

## System Requirement

- PHP 8.3+
- Composer 2.x
- Node.js 20+
- npm 10+
- PostgreSQL
- Git

Cek versi dependency:

```bash
php -v
composer -V
node -v
npm -v
psql --version
git --version
```

## Instalasi

Clone repository:

```bash
git clone <repository-url>
cd smart-stock-pro
```

Install dependency backend:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

Salin file environment:

```bash
cp .env.example .env
```

Untuk Windows Command Prompt:

```cmd
copy .env.example .env
```

Untuk PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Buat database PostgreSQL:

```bash
psql -U postgres
```

```sql
CREATE DATABASE smart_stock_pro;
\q
```

Sesuaikan konfigurasi database di `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_stock_pro
DB_USERNAME=postgres
DB_PASSWORD=
```

Jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
```

Buat storage link:

```bash
php artisan storage:link
```

Bersihkan cache konfigurasi:

```bash
php artisan optimize:clear
```

## Menjalankan Aplikasi

Jalankan Laravel:

```bash
php artisan serve
```

Jalankan Vite:

```bash
npm run dev
```

Jalankan queue worker:

```bash
php artisan queue:work
```

Akses aplikasi melalui:

```text
http://127.0.0.1:8000
```

## Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@smartstock.test | password |
| Manajer Gudang | manager@smartstock.test | password |
| Staf Gudang | staff@smartstock.test | password |
| Viewer | viewer@smartstock.test | password |

## Command Penting

Menjalankan aplikasi:

```bash
php artisan serve
npm run dev
php artisan queue:work
```

Reset database dan seed ulang:

```bash
php artisan migrate:fresh --seed
```

Menjalankan test:

```bash
php artisan test
```

Jika menggunakan Pest:

```bash
./vendor/bin/pest
```

Build frontend production:

```bash
npm run build
```

Membersihkan cache:

```bash
php artisan optimize:clear
```

Melihat failed jobs:

```bash
php artisan queue:failed
```

Menjalankan ulang failed jobs:

```bash
php artisan queue:retry all
```

## Struktur Folder

```bash
smart-stock-pro/
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Services/
│   ├── Jobs/
│   ├── Imports/
│   └── Exports/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── js/
│   └── views/
├── routes/
│   └── web.php
├── storage/
├── tests/
├── docs/
└── README.md
```

## Dokumentasi Proyek

Dokumentasi lengkap tersedia pada folder `docs/`.

| Dokumen | Isi |
|---|---|
| `01-dokumen-teknis-smartstock-pro.pdf` | Requirement analysis, use case, activity diagram, ERD, arsitektur, tools, skalabilitas, keamanan, access matrix, audit log, dan feature specification. |
| `02-dokumen-testing-migrasi-manajemen-proyek.pdf` | Test plan, test case, bug log, UAT, migrasi, rollback, cutover, Git workflow, impact analysis, timeline, WBS, risk register, dan change request. |
| `03-dokumentasi-pengguna-smartstock-pro.pdf` | User guide, FAQ, troubleshooting guide, dan dokumentasi route internal aplikasi. |

## Catatan Penggunaan

- Jalankan `php artisan queue:work` agar proses import CSV/Excel berjalan.
- Gunakan `php artisan migrate:fresh --seed` jika ingin mengulang database dari awal.
- Staf Gudang akan diarahkan ke halaman `My Warehouse`.
- Viewer hanya dapat melihat data dalam mode read-only.

## Pengembang

Proyek ini dikembangkan secara individu.

| Nama | Role |
|---|---|
| Julius Calvin Kurniadi | Full Stack Developer |

## Lisensi

Proyek ini dibuat untuk kebutuhan pembelajaran, demonstrasi, dan asesmen proyek pengembangan web.
