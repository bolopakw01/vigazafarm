# Sistem Status Penetasan

## Overview
Sistem status penetasan otomatis mengelola status data penetasan berdasarkan waktu dan kondisi tertentu.

## Status yang Tersedia

### 1. **Proses** (Default)
- **Warna**: Abu-abu
- **Kondisi**: Baru saja input data (hari yang sama)
- **Deskripsi**: Data baru dimasukkan dan menunggu untuk masuk tahap inkubasi aktif

### 2. **Aktif**
- **Warna**: Biru (Info)
- **Kondisi**: Sudah lebih dari 1 hari sejak tanggal simpan telur
- **Deskripsi**: Proses penetasan sedang berjalan

### 3. **Selesai**
- **Warna**: Hijau (Success)
- **Kondisi**: Sudah ada tanggal menetas dan data hasil penetasan lengkap
- **Deskripsi**: Proses penetasan telah selesai dan telur sudah menetas

### 4. **Gagal**
- **Warna**: Merah (Danger)
- **Kondisi**: Di-set manual oleh Owner
- **Deskripsi**: Proses penetasan gagal atau dibatalkan

## Alur Kerja Status

```
┌─────────┐     1 hari    ┌────────┐   Telur Menetas   ┌─────────┐
│ PROSES  │ ────────────> │ AKTIF  │ ───────────────> │ SELESAI │
└─────────┘               └────────┘                   └─────────┘
     │                         │                             │
     │                         │                             │
     └─────────────────────────┴─────────────────────────────┘
                               │
                               │ Owner Override
                               ▼
                         ┌─────────┐
                         │  GAGAL  │
                         └─────────┘
```

## Fitur untuk Owner

Owner memiliki hak khusus untuk mengubah status secara manual:
- Dapat mengubah status dari mana saja ke mana saja
- Berguna untuk kasus:
  - Penetasan gagal di tengah jalan
  - Koreksi data yang salah
  - Pembatalan proses penetasan

### Cara Mengubah Status (Owner Only)
1. Buka halaman **Data Penetasan**
2. Pada kolom Status, klik tombol **Edit** (ikon pensil)
3. Pilih status baru yang diinginkan
4. Klik **Simpan**

## Automation

### Scheduled Task
Sistem secara otomatis mengubah status dari **Proses** ke **Aktif** setiap hari pada jam **00:01**.

**Command:**
```bash
php artisan penetasan:update-status
```

**Schedule:**
- Frekuensi: Daily
- Waktu: 00:01
- Lokasi: `bootstrap/app.php`

### Manual Run
Untuk menjalankan update status secara manual:
```bash
php artisan penetasan:update-status
```

## Tampilan di Tabel

Kolom yang ditampilkan di halaman index:
1. **Nomor** - Nomor urut
2. **Kandang** - Nama kandang penetasan
3. **Tanggal Simpan** - Tanggal telur disimpan untuk ditetaskan
4. **Jumlah Telur** - Total telur yang ditetaskan
5. **Tanggal Menetas** - Tanggal telur menetas (jika sudah)
6. **Status** - Status penetasan dengan badge berwarna
7. **Aksi** - Tombol detail, edit, dan hapus

## File Terkait

### Migration
- `database/migrations/2025_10_02_000001_add_status_to_penetasan_table.php`

### Model
- `app/Models/Penetasan.php`

### Controller
- `app/Http/Controllers/PenetasanController.php`
  - Method `updateStatus()` untuk update status via AJAX

### Command
- `app/Console/Commands/UpdatePenetasanStatus.php`

### Routes
- `routes/web.php`
  - `PATCH /admin/penetasan/{penetasan}/status` untuk update status

### Views
- `resources/views/admin/pages/penetasan/index-penetasan.blade.php`
  - Menampilkan badge status
  - Modal untuk ubah status (owner only)

### Seeders
- `database/seeders/UpdatePenetasanStatusSeeder.php` - One-time seeder untuk update existing data

## Notes

- Status default saat create data baru adalah **Proses**
- Jika form edit diisi dengan tanggal menetas, status otomatis berubah menjadi **Selesai**
- Operator tidak dapat mengubah status secara manual
- Owner dapat override status kapan saja melalui modal status
