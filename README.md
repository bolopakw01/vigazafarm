# VigazaFarm

Aplikasi manajemen peternakan unggas berbasis Laravel untuk mencatat aktivitas kandang, produksi, pakan, kesehatan, dan alur keputusan berbasis data. Direktori `ml/` menyiapkan kerangka kerja Decision Support System (DSS) dan rekomendasi berbasis machine learning.

## Stack dan alat utama
- Backend: Laravel 12 (PHP 8.2), artisan console, queue worker, dan Tinker untuk eksperimen cepat (lihat [composer.json](composer.json)).
- Database: default SQLite untuk pengembangan; tersedia konfigurasi MySQL/MariaDB/PostgreSQL/SQL Server di [config/database.php](config/database.php).
- Frontend: Vite 7, Tailwind CSS 3 + plugin Forms, Alpine.js, dan Axios (lihat [package.json](package.json)).
- Tooling: Laravel Pint (formatting), PHPUnit 11 (testing), Laravel Pail (live log), Breeze (starter auth scaffolding), Vite dev server, dan script `composer dev` yang menjalankan server, queue listener, log stream, serta Vite secara paralel.
- ML/DSS: rangkaian notebook, pipeline, dan artefak model di [ml/README.md](ml/README.md).

## Struktur direktori ringkas
- app/: domain logic, controller, DTO, service, dan model (produksi, pakan, kesehatan, lingkungan, dll.).
- database/: migrasi, factory, seeder, dan skema SQL tambahan.
- resources/: view Blade, aset Tailwind/Vite, dan komponen UI.
- routes/: definisi route web, auth, dan console.
- ml/: workflow data science (notebook, data, artifacts, scripts, serving stubs).
- config/: konfigurasi aplikasi, database, cache, queue, mail, dsb.

## Prasyarat
- PHP 8.2+, Composer
- Node.js 18+ dan npm
- SQLite (default) atau server MySQL/MariaDB/PostgreSQL/SQL Server sesuai kebutuhan

## Menyiapkan lingkungan lokal
1) Salin env: `cp .env.example .env` lalu sesuaikan kredensial DB.
2) Install PHP dependency: `composer install`.
3) Install frontend dependency: `npm install`.
4) Generate key: `php artisan key:generate`.
5) Jalankan migrasi (opsional seed bila tersedia): `php artisan migrate`.
6) Mode dev terpadu: `composer run dev` (server, queue listener, log stream, Vite). Alternatif manual: `php artisan serve` pada satu terminal dan `npm run dev` pada terminal lain.

## Testing dan kualitas kode
- Unit/feature test: `composer test`.
- Format kode: `./vendor/bin/pint`.

## Build produksi frontend
- `npm run build` akan menghasilkan aset versi produksi yang dilayani oleh Laravel/Vite.

## Catatan ML/DSS
- Lihat panduan lengkap di [ml/README.md](ml/README.md) untuk alur eksperimen, training, dan serving.

## Lisensi
Proyek ini menggunakan lisensi MIT sebagaimana didefinisikan pada metadata Laravel skeleton..
