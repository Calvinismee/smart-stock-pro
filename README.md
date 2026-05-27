# SmartStock Pro

SmartStock Pro adalah sistem manajemen inventaris berbasis web yang digunakan untuk mengelola data produk, stok per gudang, transaksi barang masuk, transaksi barang keluar, transfer antar gudang, laporan inventaris, notifikasi stok minimum, dan audit log aktivitas pengguna.

Sistem ini dibuat untuk membantu PT Maju Bersama Digital dalam menggantikan proses pencatatan stok manual berbasis spreadsheet menjadi sistem inventaris berbasis web yang lebih terpusat, aman, dan mudah dipantau.

---

## Table of Contents

1. [Deskripsi Proyek](#1-deskripsi-proyek)
2. [Tujuan Sistem](#2-tujuan-sistem)
3. [Fitur Utama](#3-fitur-utama)
4. [Aturan Penting Sistem](#4-aturan-penting-sistem)
5. [Role Pengguna](#5-role-pengguna)
6. [Hak Akses Role](#6-hak-akses-role)
7. [Tech Stack](#7-tech-stack)
8. [System Requirement](#8-system-requirement)
9. [Instruksi Setup dan Instalasi](#9-instruksi-setup-dan-instalasi)
10. [Menjalankan Aplikasi](#10-menjalankan-aplikasi)
11. [Akun Demo](#11-akun-demo)
12. [Cara Pengoperasian Sistem](#12-cara-pengoperasian-sistem)
13. [Struktur Folder Project](#13-struktur-folder-project)
14. [Database](#14-database)
15. [Dokumentasi Route Internal](#15-dokumentasi-route-internal)
16. [Testing](#16-testing)
17. [Dokumentasi Pendukung](#17-dokumentasi-pendukung)
18. [Security](#18-security)
19. [Import dan Migrasi Data](#19-import-dan-migrasi-data)
20. [Deployment](#20-deployment)
21. [Status Pemenuhan Requirement](#21-status-pemenuhan-requirement)
22. [Kontributor / Pengembang](#22-kontributor--pengembang)
23. [Lisensi](#23-lisensi)

---

## 1. Deskripsi Proyek

PT Maju Bersama Digital merupakan perusahaan distribusi barang elektronik yang memiliki 5 gudang di beberapa kota besar Indonesia, yaitu Jakarta, Surabaya, Bandung, Medan, dan Makassar.

Sebelumnya, proses pengelolaan inventaris masih dilakukan secara manual menggunakan spreadsheet. Hal tersebut menyebabkan beberapa permasalahan, seperti ketidaksesuaian data stok, keterlambatan pembuatan laporan, tidak adanya peringatan stok minimum, serta sulitnya koordinasi transfer barang antar gudang.

SmartStock Pro dikembangkan sebagai solusi untuk mengelola inventaris secara lebih terstruktur melalui aplikasi web. Sistem ini mendukung pengelolaan produk, kategori, gudang, supplier, stok per gudang, transaksi stok, transfer antar gudang, import data, export laporan, notifikasi, dan pencatatan audit log.

---

## 2. Tujuan Sistem

Tujuan dibuatnya SmartStock Pro adalah:

1. Mengurangi kesalahan pencatatan stok akibat penggunaan spreadsheet manual.
2. Mempermudah pengelolaan data produk, kategori, gudang, dan supplier.
3. Mempercepat proses pencatatan barang masuk dan barang keluar.
4. Mempermudah proses transfer barang antar gudang.
5. Menyediakan monitoring stok produk pada setiap gudang.
6. Memberikan peringatan ketika stok berada di bawah batas minimum.
7. Menyediakan laporan inventaris yang dapat digunakan oleh manajemen.
8. Meningkatkan keamanan sistem melalui autentikasi multi-role.
9. Mencatat aktivitas penting pengguna melalui audit log.
10. Mendukung proses import data dari CSV/Excel.

---

## 3. Fitur Utama

Fitur utama yang tersedia pada SmartStock Pro:

1. Login multi-role.
2. Dashboard inventaris.
3. Halaman My Warehouse untuk Staf Gudang.
4. Master produk global.
5. Monitoring stok per produk per gudang.
6. CRUD produk.
7. CRUD kategori.
8. CRUD gudang.
9. CRUD supplier.
10. Manajemen user.
11. Transaksi barang masuk.
12. Transaksi barang keluar.
13. Transfer barang antar gudang.
14. Alert stok minimum.
15. Notifikasi aplikasi.
16. Import CSV/Excel.
17. Export laporan.
18. Audit log aktivitas pengguna.
19. Error log / monitoring aplikasi.
20. FIFO stock batch.
21. Background job / queue untuk import.
22. Galeri gambar produk.
23. Peta lokasi gudang.

---

## 4. Aturan Penting Sistem

Beberapa aturan utama dalam sistem:

1. Produk bersifat global dan tidak dimiliki oleh satu gudang tertentu.
2. Kuantitas stok produk disimpan per gudang melalui tabel `inventory_stocks`.
3. Satu produk dapat tersedia di beberapa gudang dengan jumlah stok yang berbeda.
4. Staf Gudang hanya dapat mengelola stok pada gudang yang ditugaskan.
5. Admin dan Manajer Gudang dapat memantau seluruh gudang.
6. Viewer hanya memiliki akses baca atau read-only.
7. Barang keluar dan transfer menggunakan mekanisme FIFO berdasarkan `stock_batches`.
8. Import data dijalankan melalui background job agar tidak mengganggu tampilan pengguna.

---

## 5. Role Pengguna

| Role | Deskripsi |
|---|---|
| Admin | Pengguna dengan hak akses penuh untuk mengelola seluruh sistem, user, master data, transaksi, laporan, audit log, dan error log. |
| Manajer Gudang | Pengguna yang bertanggung jawab mengelola dan memantau inventaris seluruh gudang, termasuk transaksi stok, transfer, import, dan laporan. |
| Staf Gudang | Pengguna operasional yang bertugas mencatat barang masuk, barang keluar, dan transfer dari gudang yang ditugaskan. |
| Viewer | Pengguna read-only yang dapat melihat dashboard, produk, stok, laporan, dan peta gudang tanpa mengubah data. |

---

## 6. Hak Akses Role

| Fitur | Admin | Manajer Gudang | Staf Gudang | Viewer |
|---|---|---|---|---|
| Dashboard utama | Ya | Ya | Tidak | Read-only |
| My Warehouse | Opsional | Opsional | Ya | Tidak |
| Produk | CRUD penuh | Tambah/Edit/Lihat | Lihat | Lihat |
| Kategori | CRUD penuh | CRUD | Tidak | Tidak |
| Gudang | CRUD penuh | Lihat | Tidak | Lihat |
| Supplier | CRUD penuh | CRUD | Tidak | Tidak |
| Monitoring stok | Semua gudang | Semua gudang | Gudang sendiri | Read-only |
| Barang masuk | Semua gudang | Semua gudang | Gudang sendiri | Tidak |
| Barang keluar | Semua gudang | Semua gudang | Gudang sendiri | Tidak |
| Transfer gudang | Semua gudang | Semua gudang | Dari gudang sendiri | Tidak |
| Import data | Ya | Ya | Tidak | Tidak |
| Export laporan | Ya | Ya | Tidak/Terbatas | Tidak/Read-only sesuai konfigurasi |
| Laporan | Ya | Ya | Terbatas | Read-only |
| Notifikasi | Ya | Ya | Ya | Tidak/Terbatas |
| Peta gudang | Ya | Ya | Tidak/Opsional | Ya |
| Audit log | Ya | Tidak | Tidak | Tidak |
| Error log | Ya | Tidak | Tidak | Tidak |
| Manajemen user | Ya | Tidak | Tidak | Tidak |

---

## 7. Tech Stack

Teknologi yang digunakan dalam pengembangan SmartStock Pro:

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Frontend | Inertia.js + React |
| Styling | Tailwind CSS |
| Database | PostgreSQL |
| Authentication | Laravel Session Authentication |
| Authorization | Role-based access control dan warehouse-based access control |
| Queue | Laravel Queue dengan database driver |
| Chart | Recharts |
| Map | Leaflet / React Leaflet |
| Import/Export | Laravel Excel |
| Report/PDF | DomPDF atau library laporan yang digunakan |
| Monitoring | Laravel Pulse |
| File Storage | Laravel Storage |
| Build Tool | Vite |

---

## 8. System Requirement

Sebelum menjalankan aplikasi, pastikan perangkat sudah memiliki kebutuhan berikut:

1. PHP 8.3 atau lebih baru.
2. Composer 2.x.
3. Node.js 20.x atau lebih baru.
4. npm 10.x atau lebih baru.
5. PostgreSQL.
6. Git.
7. Browser modern seperti Google Chrome, Microsoft Edge, atau Mozilla Firefox.

---

## 9. Instruksi Setup dan Instalasi

Bagian ini menjelaskan langkah-langkah instalasi SmartStock Pro dari awal sampai aplikasi dapat dijalankan di komputer lokal.

### 9.1 Prasyarat

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer 2.x
- Node.js 20.x atau lebih baru
- npm 10.x atau lebih baru
- PostgreSQL
- Git

Cek versi dependency dengan command berikut:

```bash
php -v
composer -V
node -v
npm -v
psql --version
git --version
```

### 9.2 Clone Repository

Clone repository project:

```bash
git clone <repository-url>
cd smart-stock-pro
```

Contoh:

```bash
git clone https://github.com/username/smart-stock-pro.git
cd smart-stock-pro
```

### 9.3 Install Dependency Backend

Install dependency Laravel menggunakan Composer:

```bash
composer install
```

Jika ingin install dependency production tanpa package development:

```bash
composer install --optimize-autoloader --no-dev
```

### 9.4 Install Dependency Frontend

Install dependency frontend menggunakan npm:

```bash
npm install
```

### 9.5 Setup File Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Jika menggunakan Windows Command Prompt:

```cmd
copy .env.example .env
```

Jika menggunakan PowerShell:

```powershell
Copy-Item .env.example .env
```

### 9.6 Generate Application Key

Jalankan command berikut:

```bash
php artisan key:generate
```

### 9.7 Konfigurasi Database

Buat database PostgreSQL dengan nama `smart_stock_pro`.

Jika menggunakan terminal PostgreSQL:

```bash
psql -U postgres
```

Lalu jalankan:

```sql
CREATE DATABASE smart_stock_pro;
```

Keluar dari PostgreSQL:

```sql
\q
```

Setelah database dibuat, ubah konfigurasi database pada file `.env`:

```env
APP_NAME="SmartStock Pro"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_stock_pro
DB_USERNAME=postgres
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi PostgreSQL masing-masing perangkat.

### 9.8 Konfigurasi Queue

SmartStock Pro menggunakan queue untuk proses background seperti import CSV/Excel.

Pastikan konfigurasi queue pada file `.env` menggunakan database driver:

```env
QUEUE_CONNECTION=database
```

Jika tabel jobs belum tersedia, jalankan:

```bash
php artisan queue:table
php artisan migrate
```

Catatan: jika migration tabel jobs sudah tersedia dari project, cukup jalankan `php artisan migrate`.

### 9.9 Jalankan Migrasi dan Seeder

Jalankan migrasi database dan isi data awal:

```bash
php artisan migrate:fresh --seed
```

Command ini akan:

1. Menghapus seluruh tabel lama.
2. Membuat ulang tabel berdasarkan migration.
3. Mengisi data awal seperti user demo, gudang, kategori, produk, stok, dan data pendukung lainnya.

Jika tidak ingin menghapus data lama, gunakan:

```bash
php artisan migrate --seed
```

### 9.10 Buat Storage Link

Agar file upload seperti gambar produk dapat diakses dari browser, jalankan:

```bash
php artisan storage:link
```

### 9.11 Clear dan Cache Konfigurasi

Setelah konfigurasi `.env` selesai, jalankan command berikut untuk membersihkan cache:

```bash
php artisan optimize:clear
```

Jika ingin membuat cache konfigurasi untuk production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Untuk development, cukup gunakan:

```bash
php artisan optimize:clear
```

---

## 10. Menjalankan Aplikasi

### 10.1 Jalankan Backend Laravel

Jalankan server Laravel:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

### 10.2 Jalankan Frontend Vite

Buka terminal baru, lalu jalankan:

```bash
npm run dev
```

Frontend akan berjalan melalui Vite dan terhubung dengan aplikasi Laravel.

### 10.3 Jalankan Queue Worker

Buka terminal baru, lalu jalankan:

```bash
php artisan queue:work
```

Queue worker diperlukan untuk memproses background job, seperti import data CSV/Excel.

Jika ingin menjalankan queue dengan informasi lebih detail:

```bash
php artisan queue:work --tries=3 --timeout=120
```

Jika ingin menjalankan queue sekali proses:

```bash
php artisan queue:work --once
```

### 10.4 Jalankan Scheduler

Jika sistem menggunakan scheduler, jalankan command berikut pada terminal baru:

```bash
php artisan schedule:work
```

Untuk production, scheduler biasanya dijalankan melalui cron:

```bash
* * * * * cd /path/to/smart-stock-pro && php artisan schedule:run >> /dev/null 2>&1
```

### 10.5 Menjalankan Semua Service Saat Development

Saat development, minimal buka 3 terminal:

Terminal 1 untuk Laravel:

```bash
php artisan serve
```

Terminal 2 untuk Vite:

```bash
npm run dev
```

Terminal 3 untuk queue worker:

```bash
php artisan queue:work
```

Jika scheduler digunakan, buka terminal 4:

```bash
php artisan schedule:work
```

---

## 11. Akun Demo

Setelah menjalankan seeder, gunakan akun berikut untuk login:

| Role | Email | Password |
|---|---|---|
| Admin | admin@smartstock.test | password |
| Manajer Gudang | manager@smartstock.test | password |
| Staf Gudang | staff@smartstock.test | password |
| Viewer | viewer@smartstock.test | password |

Jika tersedia akun staf per gudang:

| Role | Email | Password |
|---|---|---|
| Staf Gudang Jakarta | staff_jakarta@smartstock.test | password |
| Staf Gudang Makassar | staff_makassar@smartstock.test | password |

---

## 12. Cara Pengoperasian Sistem

### 12.1 Login ke Sistem

Langkah penggunaan:

1. Buka aplikasi melalui browser.
2. Masuk ke halaman login.
3. Masukkan email dan password sesuai akun demo atau akun yang sudah dibuat.
4. Klik tombol login.
5. Sistem akan mengarahkan pengguna ke halaman sesuai role.

Arah halaman setelah login:

| Role | Halaman Awal |
|---|---|
| Admin | Dashboard utama |
| Manajer Gudang | Dashboard utama |
| Staf Gudang | My Warehouse |
| Viewer | Dashboard utama read-only |

### 12.2 Menggunakan Dashboard

Dashboard digunakan untuk melihat ringkasan kondisi inventaris.

Pengguna yang dapat mengakses:

- Admin
- Manajer Gudang
- Viewer dalam mode read-only

Informasi yang ditampilkan:

1. Total produk.
2. Total gudang.
3. Total stok.
4. Jumlah produk stok minimum.
5. Grafik stok atau transaksi.
6. Ringkasan nilai inventaris.
7. Notifikasi stok minimum.
8. Peta lokasi gudang.

Staf Gudang tidak menggunakan dashboard utama, tetapi menggunakan halaman My Warehouse.

### 12.3 Menggunakan My Warehouse

Halaman My Warehouse digunakan oleh Staf Gudang untuk mengelola operasional gudang yang ditugaskan.

Informasi yang ditampilkan:

1. Nama gudang yang ditugaskan.
2. Ringkasan stok gudang.
3. Produk dengan stok minimum.
4. Transaksi barang masuk terakhir.
5. Transaksi barang keluar terakhir.
6. Transfer gudang terakhir.
7. Tombol cepat barang masuk, barang keluar, dan transfer.

Aturan:

- Staf Gudang hanya dapat melihat dan mengelola data gudang yang ditugaskan.
- Staf Gudang tidak dapat mengubah stok gudang lain.

### 12.4 Mengelola Produk

Produk merupakan master data global.

Langkah umum:

1. Buka menu Produk.
2. Lihat daftar produk yang tersedia.
3. Gunakan pencarian atau filter jika diperlukan.
4. Admin dan Manajer Gudang dapat menambah atau mengubah produk.
5. Staf Gudang dan Viewer hanya dapat melihat data produk.

Data produk meliputi:

- SKU.
- Nama produk.
- Kategori.
- Supplier.
- Harga.
- Minimum stok.
- Status aktif.
- Gambar produk.

### 12.5 Monitoring Stok per Gudang

Monitoring stok digunakan untuk melihat jumlah stok produk pada masing-masing gudang.

Langkah penggunaan:

1. Buka menu Monitoring Stok atau Inventory Stocks.
2. Pilih filter gudang jika diperlukan.
3. Pilih filter kategori atau status stok jika tersedia.
4. Lihat jumlah stok setiap produk pada setiap gudang.
5. Perhatikan status stok seperti aman, menipis, atau kritis.

Aturan akses:

- Admin dan Manajer Gudang dapat melihat stok semua gudang.
- Staf Gudang hanya dapat melihat stok gudang yang ditugaskan.
- Viewer dapat melihat stok dalam mode read-only.

### 12.6 Mencatat Barang Masuk

Barang masuk digunakan untuk menambah stok produk ke gudang.

Langkah penggunaan:

1. Buka menu Barang Masuk.
2. Klik tombol tambah transaksi barang masuk.
3. Pilih produk.
4. Pilih gudang tujuan.
5. Pilih supplier jika diperlukan.
6. Masukkan jumlah barang.
7. Masukkan tanggal transaksi.
8. Isi catatan jika diperlukan.
9. Klik simpan.
10. Sistem akan menambahkan stok pada gudang terkait.
11. Sistem akan membuat batch stok baru untuk kebutuhan FIFO.
12. Sistem mencatat aktivitas ke audit log.

Aturan:

- Admin dan Manajer Gudang dapat mencatat barang masuk untuk semua gudang.
- Staf Gudang hanya dapat mencatat barang masuk untuk gudang yang ditugaskan.
- Viewer tidak dapat mencatat barang masuk.

### 12.7 Mencatat Barang Keluar

Barang keluar digunakan untuk mengurangi stok produk dari gudang.

Langkah penggunaan:

1. Buka menu Barang Keluar.
2. Klik tombol tambah transaksi barang keluar.
3. Pilih produk.
4. Pilih gudang asal.
5. Masukkan jumlah barang keluar.
6. Masukkan tanggal transaksi.
7. Isi catatan jika diperlukan.
8. Klik simpan.
9. Sistem memeriksa ketersediaan stok.
10. Jika stok mencukupi, sistem mengurangi stok berdasarkan metode FIFO.
11. Jika stok berada di bawah minimum, sistem membuat notifikasi stok minimum.
12. Sistem mencatat aktivitas ke audit log.

Aturan:

- Stok tidak boleh menjadi negatif.
- Barang keluar akan ditolak jika stok tidak mencukupi.
- Staf Gudang hanya dapat mencatat barang keluar dari gudang yang ditugaskan.
- Viewer tidak dapat mencatat barang keluar.

### 12.8 Transfer Antar Gudang

Transfer gudang digunakan untuk memindahkan stok dari satu gudang ke gudang lain.

Langkah penggunaan:

1. Buka menu Transfer Gudang.
2. Klik tombol tambah transfer.
3. Pilih gudang asal.
4. Pilih gudang tujuan.
5. Pilih produk.
6. Masukkan jumlah barang.
7. Masukkan tanggal transfer.
8. Isi catatan jika diperlukan.
9. Klik simpan atau proses transfer.
10. Sistem memeriksa stok gudang asal.
11. Sistem memproses pengurangan stok dari gudang asal.
12. Sistem mencatat proses transfer.
13. Jika transfer menggunakan status in-transit, stok akan ditambahkan ke gudang tujuan setelah transfer diterima atau diselesaikan.
14. Sistem mencatat aktivitas ke audit log.

Aturan:

- Gudang asal dan gudang tujuan tidak boleh sama.
- Stok gudang asal harus mencukupi.
- Staf Gudang hanya dapat melakukan transfer dari gudang yang ditugaskan.
- Viewer tidak dapat melakukan transfer.

### 12.9 Import Data CSV/Excel

Import digunakan untuk memasukkan data secara massal dari file CSV/Excel.

Langkah penggunaan:

1. Buka menu Import.
2. Pilih jenis import, misalnya produk atau stok.
3. Download template jika tersedia.
4. Isi data sesuai format template.
5. Upload file CSV/Excel.
6. Klik tombol import.
7. Sistem akan mengirim proses import ke background job.
8. Jalankan queue worker agar proses import berjalan.
9. Setelah selesai, sistem akan memberikan notifikasi hasil import.

Catatan:

- File harus mengikuti format template.
- Jika queue worker tidak berjalan, import tidak akan diproses.
- Data tidak valid akan ditolak atau ditandai sebagai gagal import sesuai validasi sistem.

### 12.10 Export dan Melihat Laporan

Laporan digunakan untuk melihat data inventaris.

Jenis laporan yang dapat tersedia:

1. Laporan stok produk.
2. Laporan stok minimum.
3. Laporan barang masuk.
4. Laporan barang keluar.
5. Laporan transfer gudang.
6. Laporan nilai inventaris.

Langkah penggunaan:

1. Buka menu Laporan.
2. Pilih jenis laporan.
3. Tentukan filter, seperti gudang, produk, atau rentang tanggal.
4. Lihat hasil laporan.
5. Jika memiliki akses, klik export untuk mengunduh laporan.

Aturan:

- Admin dan Manajer Gudang dapat melihat dan export laporan.
- Viewer hanya dapat melihat laporan dalam mode read-only.
- Staf Gudang dapat dibatasi hanya pada data gudang yang ditugaskan.

### 12.11 Melihat Notifikasi

Notifikasi digunakan untuk menampilkan informasi penting dari sistem.

Contoh notifikasi:

1. Stok berada di bawah batas minimum.
2. Proses import berhasil.
3. Proses import gagal.
4. Terjadi error pada proses tertentu.

Langkah penggunaan:

1. Klik ikon notifikasi atau buka menu Notifikasi.
2. Lihat daftar notifikasi.
3. Baca detail notifikasi.
4. Tandai sebagai sudah dibaca jika tersedia.

### 12.12 Melihat Peta Gudang

Peta gudang digunakan untuk melihat lokasi gudang perusahaan.

Langkah penggunaan:

1. Buka menu Peta Gudang.
2. Sistem menampilkan marker lokasi gudang.
3. Klik marker untuk melihat informasi gudang.
4. Informasi yang dapat ditampilkan meliputi nama gudang, kota, dan ringkasan stok.

### 12.13 Melihat Audit Log

Audit log digunakan untuk melihat aktivitas penting pengguna.

Pengguna yang dapat mengakses:

- Admin

Informasi yang dicatat:

1. Login.
2. Logout.
3. Tambah data.
4. Ubah data.
5. Hapus data.
6. Transaksi barang masuk.
7. Transaksi barang keluar.
8. Transfer gudang.
9. Import data.
10. Export laporan.

### 12.14 Melihat Error Log

Error log digunakan untuk melihat gangguan atau error aplikasi.

Pengguna yang dapat mengakses:

- Admin

Informasi yang ditampilkan:

1. Pesan error.
2. Tingkat severity.
3. Waktu kejadian.
4. Status penyelesaian.
5. Detail teknis jika tersedia.

---

## 13. Struktur Folder Project

```bash
smart-stock-pro/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
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
│   │   ├── Components/
│   │   ├── Layouts/
│   │   └── Pages/
│   └── views/
├── routes/
│   └── web.php
├── storage/
├── tests/
│   └── Feature/
├── docs/
└── README.md
```

---

## 14. Database

Tabel utama yang digunakan:

- `users`
- `categories`
- `warehouses`
- `suppliers`
- `products`
- `inventory_stocks`
- `stock_batches`
- `stock_transactions`
- `stock_transfers`
- `notifications`
- `audit_logs`
- `error_logs`
- `import_logs`
- `jobs`

Relasi utama:

1. `products` menyimpan data master produk secara global.
2. `warehouses` menyimpan data gudang.
3. `inventory_stocks` menghubungkan produk dengan gudang dan menyimpan jumlah stok per gudang.
4. `stock_batches` menyimpan batch stok untuk perhitungan FIFO.
5. `stock_transactions` mencatat transaksi barang masuk dan barang keluar.
6. `stock_transfers` mencatat transfer barang antar gudang.
7. `users` dapat memiliki `warehouse_id` jika role pengguna adalah Staf Gudang.
8. `audit_logs` mencatat aktivitas penting pengguna.
9. `notifications` menyimpan notifikasi sistem.

Lokasi migration:

```bash
database/migrations/
```

Lokasi seeder:

```bash
database/seeders/
```

Lokasi ERD:

```bash
docs/01-dokumen-teknis-smartstock-pro.pdf
```

---

## 15. Dokumentasi Route Internal

SmartStock Pro merupakan aplikasi monolith berbasis Laravel, Inertia.js, dan React. Sistem tidak menyediakan API publik untuk aplikasi pihak ketiga. Seluruh proses data dilakukan melalui route internal aplikasi yang terhubung dengan controller Laravel dan halaman Inertia.

| Modul | Route | Method | Hak Akses |
|---|---|---|---|
| Login | `/login` | GET/POST | Semua pengguna |
| Logout | `/logout` | POST | Pengguna login |
| Dashboard | `/dashboard` | GET | Admin, Manajer Gudang, Viewer |
| My Warehouse | `/my-warehouse` | GET | Staf Gudang |
| Produk | `/products` | GET | Admin, Manajer Gudang, Staf Gudang, Viewer |
| Monitoring Stok | `/inventory-stocks` | GET | Admin, Manajer Gudang, Staf Gudang, Viewer |
| Barang Masuk | `/stock-transactions/in/create` | GET | Admin, Manajer Gudang, Staf Gudang |
| Barang Keluar | `/stock-transactions/out/create` | GET | Admin, Manajer Gudang, Staf Gudang |
| Transfer Gudang | `/stock-transfers` | GET | Admin, Manajer Gudang, Staf Gudang |
| Import | `/import` | GET | Admin, Manajer Gudang |
| Laporan | `/reports` | GET | Admin, Manajer Gudang, Viewer |
| Export | `/exports/{type}` | GET | Admin, Manajer Gudang |
| Notifikasi | `/notifications` | GET | Admin, Manajer Gudang, Staf Gudang |
| Peta Gudang | `/warehouse-map` | GET | Admin, Manajer Gudang, Viewer |
| Audit Log | `/audit-logs` | GET | Admin |
| Error Log | `/error-logs` | GET | Admin |
| User Management | `/users` | GET | Admin |

Catatan:

1. Viewer hanya dapat mengakses route dalam mode read-only.
2. Staf Gudang hanya dapat mengakses data stok pada gudang yang ditugaskan.
3. Route mutasi data dilindungi dengan middleware, policy, dan validasi backend.
4. Import data diproses melalui background job.

---

## 16. Testing

Menjalankan seluruh test:

```bash
php artisan test
```

Jika menggunakan Pest:

```bash
./vendor/bin/pest
```

Menjalankan test tertentu:

```bash
php artisan test --filter=AuthenticationTest
```

atau:

```bash
./vendor/bin/pest --filter=AuthenticationTest
```

Modul yang diuji:

1. Login dan logout.
2. Role access control.
3. Warehouse scope untuk Staf Gudang.
4. Viewer read-only access.
5. Product CRUD.
6. Inventory stock monitoring.
7. Barang masuk.
8. Barang keluar.
9. Transfer gudang.
10. FIFO stock batch.
11. Import queue.
12. Notifikasi stok minimum.
13. Audit log.
14. Dashboard access.
15. My Warehouse access.

---

## 17. Dokumentasi Pendukung

Dokumen proyek tersedia pada folder `docs/`:

- `01-dokumen-teknis-smartstock-pro.pdf`
- `02-dokumen-testing-migrasi-manajemen-proyek.pdf`
- `03-dokumentasi-pengguna-smartstock-pro.pdf`

Isi dokumen:

| Dokumen | Isi |
|---|---|
| Dokumen Teknis | Requirement analysis, use case, activity diagram, ERD, arsitektur, tools, skalabilitas, keamanan, access matrix, audit log, feature specification |
| Dokumen Testing, Migrasi, dan Manajemen Proyek | Test plan, test case, bug log, UAT, migrasi, rollback, cutover, Git workflow, impact analysis, timeline, WBS, risk register |
| Dokumentasi Pengguna | User guide, FAQ, troubleshooting guide, dan dokumentasi route internal aplikasi |

---

## 18. Security

SmartStock Pro menerapkan beberapa mekanisme keamanan:

1. Password hashing menggunakan bcrypt.
2. Validasi kekuatan password.
3. Role-based access control.
4. Warehouse-based access control untuk Staf Gudang.
5. CSRF protection bawaan Laravel.
6. XSS prevention melalui React escaping.
7. SQL Injection prevention melalui Eloquent ORM dan Query Builder.
8. Session timeout.
9. Audit log untuk aktivitas penting.
10. Proteksi route menggunakan middleware dan policy.
11. Viewer dibatasi hanya untuk akses read-only.

---

## 19. Import dan Migrasi Data

SmartStock Pro mendukung import data dari CSV/Excel untuk membantu migrasi dari sistem lama berbasis spreadsheet.

Data yang dapat diimport:

1. Produk.
2. Stok awal.

Proses import dijalankan melalui background job agar tidak menyebabkan timeout pada UI.

Tahapan migrasi:

1. Menyiapkan template spreadsheet.
2. Melakukan mapping field dari spreadsheet lama ke struktur SmartStock Pro.
3. Melakukan validasi format file.
4. Melakukan import data.
5. Memvalidasi hasil import.
6. Memeriksa data gagal import jika ada.
7. Melakukan rollback jika migrasi gagal.

Dokumen migrasi, field mapping, validasi data, dan rollback plan tersedia pada dokumen pendukung.

---

## 20. Deployment

Tahapan deployment:

1. Pull source code dari repository.
2. Install dependency backend menggunakan Composer.
3. Install dependency frontend menggunakan npm.
4. Setup file `.env`.
5. Generate application key.
6. Jalankan migration dan seeder.
7. Jalankan storage link.
8. Build frontend assets.
9. Jalankan queue worker.
10. Konfigurasi scheduler jika digunakan.
11. Konfigurasi web server.
12. Aktifkan monitoring aplikasi.
13. Lakukan smoke testing setelah deployment.

Contoh command production:

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan queue:work
```

---

## 21. Status Pemenuhan Requirement

| Modul | Status |
|---|---|
| Autentikasi dan keamanan | Selesai |
| Dashboard dan monitoring | Selesai |
| Manajemen data inventaris | Selesai |
| Notifikasi dan alert | Selesai |
| Transfer antar gudang | Selesai |
| Import CSV/Excel | Selesai |
| Background job / queue | Selesai |
| FIFO stock batch | Selesai |
| Audit log | Selesai |
| Monitoring error/log aplikasi | Selesai |
| Migrasi data | Tersedia dalam dokumen |
| Dokumentasi pengguna | Tersedia |
| Testing | Dalam proses / Selesai |

---

## 22. Kontributor / Pengembang

Proyek ini dikembangkan secara individu.

| Nama | Role |
|---|---|
| Julius Calvin Kurniadi | Full Stack Developer |

---

## 23. Lisensi

Proyek ini dibuat untuk kebutuhan pembelajaran, demonstrasi, dan asesmen proyek pengembangan web.

Jika ingin menambahkan lisensi formal, gunakan salah satu lisensi open-source seperti MIT License.

---

## 24. Ringkasan Command Setup Cepat

Gunakan command berikut untuk setup cepat dari awal:

```bash
git clone <repository-url>
cd smart-stock-pro

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed
php artisan storage:link
php artisan optimize:clear

php artisan serve
```

Pada terminal lain:

```bash
npm run dev
```

Pada terminal lain untuk queue:

```bash
php artisan queue:work
```
