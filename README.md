# SmartStock Pro

SmartStock Pro adalah aplikasi web manajemen inventaris yang digunakan untuk mengelola produk, stok per gudang, transaksi barang masuk, transaksi barang keluar, transfer antar gudang, laporan, notifikasi stok minimum, dan audit log aktivitas pengguna.

Aplikasi ini dikembangkan untuk membantu PT Maju Bersama Digital dalam menggantikan proses pencatatan stok manual berbasis spreadsheet menjadi sistem inventaris berbasis web yang lebih terpusat, aman, dan mudah dipantau.

---

## 1. Deskripsi Proyek

PT Maju Bersama Digital merupakan perusahaan distribusi barang elektronik yang memiliki beberapa gudang di kota besar Indonesia, yaitu Jakarta, Surabaya, Bandung, Medan, dan Makassar.

Sebelumnya, proses pengelolaan stok masih dilakukan secara manual menggunakan spreadsheet sehingga sering terjadi ketidaksesuaian data, keterlambatan laporan, tidak adanya peringatan stok minimum, dan sulitnya koordinasi transfer barang antar gudang.

SmartStock Pro dibuat untuk menyelesaikan masalah tersebut dengan menyediakan sistem inventaris yang mendukung pengelolaan produk, monitoring stok per gudang, transaksi stok, transfer gudang, import data, laporan, notifikasi, dan audit log.

---

## 2. Fitur Utama

- Login multi-role
- Dashboard inventaris
- Halaman My Warehouse untuk Staf Gudang
- Master produk global
- Monitoring stok per produk per gudang
- CRUD produk
- CRUD kategori
- CRUD gudang
- CRUD supplier
- Manajemen user
- Transaksi barang masuk
- Transaksi barang keluar
- Transfer barang antar gudang
- Alert stok minimum
- Notifikasi aplikasi
- Import CSV/Excel
- Export laporan
- Audit log aktivitas pengguna
- Error log / monitoring aplikasi
- FIFO stock batch
- Background job / queue untuk proses import
- Galeri gambar produk
- Peta lokasi gudang

---

## 3. Aturan Penting Sistem

- Produk bersifat global dan tidak dimiliki oleh satu gudang tertentu.
- Kuantitas stok produk disimpan per gudang melalui tabel `inventory_stocks`.
- Setiap produk dapat tersedia di beberapa gudang dengan jumlah stok yang berbeda.
- Staf Gudang hanya dapat mengelola stok pada gudang yang ditugaskan.
- Admin dan Manajer Gudang dapat memantau seluruh gudang.
- Viewer hanya memiliki akses baca atau read-only.
- Barang keluar dan transfer menggunakan mekanisme FIFO berdasarkan `stock_batches`.
- Import data dijalankan melalui background job agar tidak mengganggu tampilan pengguna.

---

## 4. Role Pengguna

| Role | Deskripsi |
|---|---|
| Admin | Pengguna dengan hak akses penuh untuk mengelola seluruh sistem, user, master data, transaksi, laporan, audit log, dan error log. |
| Manajer Gudang | Pengguna yang bertanggung jawab mengelola dan memantau inventaris seluruh gudang, termasuk transaksi stok, transfer, import, dan laporan. |
| Staf Gudang | Pengguna operasional yang bertugas mencatat barang masuk, barang keluar, dan transfer dari gudang yang ditugaskan. |
| Viewer | Pengguna read-only yang dapat melihat dashboard, produk, stok, laporan, dan peta gudang tanpa mengubah data. |

---

## 5. Hak Akses Role

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

## 6. Tech Stack

- Backend: Laravel 13
- Frontend: Inertia.js + React
- Styling: Tailwind CSS
- Database: PostgreSQL
- Authentication: Laravel Session Authentication
- Authorization: Role-based access control dan warehouse-based access control
- Queue: Laravel Queue dengan database driver
- Chart: Recharts
- Map: Leaflet / React Leaflet
- Import/Export: Laravel Excel
- PDF/Report: DomPDF atau library laporan yang digunakan
- Monitoring: Laravel Pulse
- File Storage: Laravel Storage
- Build Tool: Vite

---

## 7. System Requirement

- PHP 8.3 atau lebih baru
- Composer 2.x
- Node.js 20.x atau lebih baru
- npm 10.x atau lebih baru
- PostgreSQL
- Git
- Browser modern seperti Chrome, Edge, atau Firefox

---

## 8. Cara Instalasi

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

Generate application key:

```bash
php artisan key:generate
```

Atur konfigurasi database pada file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_stock_pro
DB_USERNAME=postgres
DB_PASSWORD=
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate:fresh --seed
```

Buat symbolic link untuk storage:

```bash
php artisan storage:link
```

---

## 9. Cara Menjalankan Aplikasi

Jalankan backend Laravel:

```bash
php artisan serve
```

Jalankan frontend Vite:

```bash
npm run dev
```

Jalankan queue worker:

```bash
php artisan queue:work
```

Jalankan scheduler jika digunakan:

```bash
php artisan schedule:work
```

Build frontend untuk production:

```bash
npm run build
```

---

## 10. Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@smartstock.test | Password123! |
| Manajer Gudang | manager@smartstock.test | Password123! |
| Staf Gudang | staff@smartstock.test | Password123! |
| Viewer | viewer@smartstock.test | Password123! |

Jika tersedia akun staf per gudang:

| Role | Email | Password |
|---|---|---|
| Staf Gudang Jakarta | staff_jakarta@smartstock.test | Password123! |
| Staf Gudang Makassar | staff_makassar@smartstock.test | Password123! |

---

## 11. Struktur Folder Project

```text
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

## 12. Database

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
- `products` menyimpan data master produk secara global.
- `warehouses` menyimpan data gudang.
- `inventory_stocks` menghubungkan produk dengan gudang dan menyimpan jumlah stok per gudang.
- `stock_batches` menyimpan batch stok untuk perhitungan FIFO.
- `stock_transactions` mencatat transaksi barang masuk dan barang keluar.
- `stock_transfers` mencatat transfer barang antar gudang.
- `users` dapat memiliki `warehouse_id` jika role pengguna adalah Staf Gudang.
- `audit_logs` mencatat aktivitas penting pengguna.
- `notifications` menyimpan notifikasi sistem seperti stok minimum dan hasil import.

Lokasi migration:
`database/migrations/`

Lokasi seeder:
`database/seeders/`

Lokasi ERD:
`docs/01-dokumen-teknis-smartstock-pro.pdf`

---

## 13. Dokumentasi Route Internal

SmartStock Pro merupakan aplikasi monolith berbasis Laravel, Inertia.js, dan React. Sistem tidak menyediakan API publik untuk aplikasi pihak ketiga. Seluruh proses data dilakukan melalui route internal aplikasi yang terhubung dengan controller Laravel dan halaman Inertia.

| Modul | Route | Method | Hak Akses |
|---|---|---|---|
| Login | `/login` | GET/POST | Semua pengguna |
| Logout | `/logout` | POST | Pengguna login |
| Dashboard | `/dashboard` | GET | Admin, Manajer Gudang, Viewer |
| My Warehouse | `/my-warehouse` | GET | Staf Gudang |
| Produk | `/products` | GET | Admin, Manajer Gudang, Staf Gudang, Viewer |
| Produk Create | `/products/create` | GET | Admin, Manajer Gudang |
| Produk Store | `/products` | POST | Admin, Manajer Gudang |
| Produk Detail | `/products/{id}` | GET | Admin, Manajer Gudang, Staf Gudang, Viewer |
| Produk Edit | `/products/{id}/edit` | GET | Admin, Manajer Gudang |
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

**Catatan:**
- Viewer hanya dapat mengakses route dalam mode read-only.
- Staf Gudang hanya dapat mengakses data stok pada gudang yang ditugaskan.
- Route mutasi data dilindungi dengan middleware, policy, dan validasi backend.
- Import data diproses melalui background job.

---

## 14. Testing

Menjalankan seluruh test:
```bash
php artisan test
```

Jika menggunakan Pest:
```bash
./vendor/bin/pest
```

Modul yang diuji:
- Login dan logout
- Role access control
- Warehouse scope untuk Staf Gudang
- Viewer read-only access
- Product CRUD
- Inventory stock monitoring
- Barang masuk
- Barang keluar
- Transfer gudang
- FIFO stock batch
- Import queue
- Notifikasi stok minimum
- Audit log
- Dashboard access
- My Warehouse access

---

## 15. Dokumentasi Pendukung

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

## 16. Security

SmartStock Pro menerapkan beberapa mekanisme keamanan:
- Password hashing menggunakan bcrypt.
- Validasi kekuatan password.
- Role-based access control.
- Warehouse-based access control untuk Staf Gudang.
- CSRF protection bawaan Laravel.
- XSS prevention melalui React escaping.
- SQL Injection prevention melalui Eloquent ORM dan Query Builder.
- Session timeout.
- Audit log untuk aktivitas penting.
- Proteksi route menggunakan middleware dan policy.
- Viewer dibatasi hanya untuk akses read-only.

---

## 17. Import dan Migrasi Data

SmartStock Pro mendukung import data dari CSV/Excel untuk membantu migrasi dari sistem lama berbasis spreadsheet.

Data yang dapat diimport:
- Produk
- Stok awal

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

## 18. Deployment

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

## 19. Status Pemenuhan Requirement

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

## 20. Screenshot Aplikasi

Tambahkan screenshot fitur utama pada bagian ini.

Daftar screenshot yang disarankan:
- Halaman login
- Dashboard utama Admin/Manajer Gudang
- My Warehouse Staf Gudang
- Data produk
- Monitoring stok per gudang
- Data gudang
- Barang masuk
- Barang keluar
- Transfer antar gudang
- Laporan
- Notifikasi
- Peta gudang
- Audit log
- Error log

Contoh format:

### Login
![Login](docs/screenshots/login.png)

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### My Warehouse
![My Warehouse](docs/screenshots/my-warehouse.png)

---

## 21. Kontributor / Pengembang

Proyek ini dikembangkan secara individu.

| Nama | Role |
|---|---|
| Julius Calvin Kurniadi | Full Stack Developer |

---

## 22. Lisensi

Proyek ini dibuat untuk kebutuhan pembelajaran, demonstrasi, dan asesmen proyek pengembangan web.

Jika ingin menambahkan lisensi formal, gunakan salah satu lisensi open-source seperti MIT License.
