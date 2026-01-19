# VigazaFarm

Platform manajemen peternakan unggas berbasis Laravel yang membantu mencatat aktivitas kandang, produksi, pakan, kesehatan, serta menyediakan kerangka Decision Support System (DSS) untuk rekomendasi berbasis data.

## Fitur singkat
- Pencatatan batch pembesaran, penetasan, produksi, dan stok pakan
- Monitor kesehatan, kematian, lingkungan, serta histori pakan/vitamin
- Alur kerja ML/DSS disiapkan di folder `ml/` untuk rekomendasi berbasis data
- Skrip terpadu `composer dev` menyalakan server, queue listener, log stream, dan Vite sekaligus

## Stack
- Backend: Laravel 12 (PHP 8.2) – lihat [composer.json](composer.json)
- Frontend: Vite 7, Tailwind CSS 3 + Forms, Alpine.js, Axios – lihat [package.json](package.json)
- Database: SQLite (default dev) atau MySQL/MariaDB/PostgreSQL/SQL Server via [config/database.php](config/database.php)
- Tooling: Laravel Pail (live log), Laravel Pint (format), PHPUnit 11 (test), Laravel Breeze (auth scaffolding)

## Prasyarat
- PHP 8.2+, Composer
- Node.js 18+ dan npm
- SQLite file local (default) atau kredensial DB server sesuai kebutuhan

## Setup cepat
```bash
cp .env.example .env            # atau salin manual di Windows
composer install
npm install
php artisan key:generate
php artisan migrate             # siapkan database
```

### Menjalankan mode pengembangan
- Satu perintah terpadu: `composer run dev`
- Manual: jalankan `php artisan serve` dan `npm run dev` di terminal terpisah; queue listener dan pail log dapat dijalankan via `php artisan queue:listen --tries=1` dan `php artisan pail --timeout=0`

### Testing dan kualitas kode
- Jalankan test: `composer test`
- Format PHP: `./vendor/bin/pint`

### Build frontend produksi
- `npm run build` akan menghasilkan aset Vite produksi yang dilayani Laravel

## Struktur ringkas
- [app](app): domain logic (model, controller, DTO, service, middleware)
- [database](database): migrasi, factory, seeder, dan skema SQL
- [resources](resources): Blade view, aset Tailwind/Vite, komponen UI
- [routes](routes): definisi route web, auth, dan console
- [ml](ml): notebook, data, artifacts, scripts, dan stub serving DSS
- [config](config): konfigurasi aplikasi, database, cache, queue, mail, dsb.

## Catatan ML/DSS
Panduan workflow eksperimen, training, dan serving tersedia di [ml/README.md](ml/README.md). Endpoint inferensi dapat diarahkan ke artefak produksi setelah pipeline siap.

## Lisensi
MIT sesuai lisensi Laravel skeleton.
