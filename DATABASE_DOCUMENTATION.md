# Dokumentasi Database - Decision Support System Terintegrasi
## Monitoring Agribisnis Burung Puyuh (VigazaFarm)

### Tanggal: 1 Oktober 2025
### Versi: 2.0

---

## 📋 Daftar Isi
1. [Gambaran Umum](#gambaran-umum)
2. [Struktur Database](#struktur-database)
3. [Tabel-tabel Utama](#tabel-tabel-utama)
4. [Relasi Database](#relasi-database)
5. [Fitur Decision Support System](#fitur-decision-support-system)
6. [Cara Migrasi](#cara-migrasi)

---

## 🎯 Gambaran Umum

Database ini dirancang khusus untuk Decision Support System (DSS) Terintegrasi yang mendukung monitoring dan pengelolaan agribisnis burung puyuh secara komprehensif. Sistem ini mencakup:

- **Manajemen Kandang** - Pengelolaan berbagai jenis kandang
- **Manajemen Batch Produksi** - Tracking per periode/batch produksi
- **Manajemen Inventori Pakan** - Kontrol stok dan transaksi pakan
- **Monitoring Kesehatan** - Vaksinasi, pengobatan, dan karantina
- **Manajemen Keuangan** - Tracking pemasukan dan pengeluaran
- **Penjualan** - Penjualan telur dan burung
- **Monitoring Lingkungan** - Suhu, kelembaban, cahaya
- **Analisis & Rekomendasi** - DSS untuk pengambilan keputusan
- **Laporan Harian** - Summary aktivitas harian
- **Sistem Alert** - Notifikasi otomatis

---

## 🗄️ Struktur Database

### Tabel Master

#### 1. **pengguna** (users)
Tabel untuk manajemen pengguna sistem.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| nama | varchar | Nama pengguna |
| nama_pengguna | varchar | Username login |
| surel | varchar | Email |
| kata_sandi | varchar | Password (encrypted) |
| peran | enum | 'owner', 'operator' |
| foto_profil | varchar | Path foto profil |
| dibuat_pada | timestamp | Created at |
| diperbarui_pada | timestamp | Updated at |

#### 2. **kandang**
Tabel untuk manajemen kandang/housing.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kode_kandang | varchar(50) | Kode unik kandang |
| nama_kandang | varchar(100) | Nama kandang |
| kapasitas_maksimal | integer | Kapasitas maksimal |
| tipe_kandang | enum | 'penetasan', 'pembesaran', 'produksi', 'karantina' |
| status | enum | 'aktif', 'maintenance', 'kosong' |
| keterangan | text | Catatan tambahan |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

#### 3. **batch_produksi**
Tabel untuk tracking batch/periode produksi.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kode_batch | varchar(50) | Kode unik batch |
| kandang_id | bigint | Foreign key ke kandang |
| tanggal_mulai | date | Tanggal mulai batch |
| tanggal_akhir | date | Tanggal akhir batch |
| jumlah_awal | integer | Jumlah burung awal |
| jumlah_saat_ini | integer | Jumlah burung saat ini |
| fase | enum | 'DOC', 'grower', 'layer', 'afkir' |
| status | enum | 'aktif', 'selesai', 'dibatalkan' |
| catatan | text | |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

#### 4. **stok_pakan**
Tabel untuk manajemen inventori pakan.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kode_pakan | varchar(50) | Kode unik pakan |
| nama_pakan | varchar(100) | Nama pakan |
| jenis_pakan | varchar(50) | Starter, Grower, Layer |
| merek | varchar(100) | Merek pakan |
| harga_per_kg | decimal(10,2) | Harga per kilogram |
| stok_kg | decimal(10,2) | Stok dalam kg |
| stok_karung | integer | Stok dalam karung |
| berat_per_karung | decimal(8,2) | Berat per karung (kg) |
| tanggal_kadaluarsa | date | Tanggal kadaluarsa |
| supplier | varchar(100) | Nama supplier |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

---

### Tabel Transaksi

#### 5. **penetasan**
Tabel untuk tracking proses penetasan telur.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kandang_id | bigint | Foreign key ke kandang |
| tanggal_simpan_telur | date | Tanggal telur disimpan |
| jumlah_telur | integer | Jumlah telur |
| tanggal_menetas | date | Tanggal menetas |
| jumlah_menetas | integer | Jumlah yang menetas |
| jumlah_doc | integer | Day Old Chick |
| suhu_penetasan | decimal(5,2) | Suhu saat penetasan (°C) |
| kelembaban_penetasan | decimal(5,2) | Kelembaban (%) |
| telur_tidak_fertil | integer | Telur tidak fertil |
| persentase_tetas | decimal(5,2) | % keberhasilan tetas |
| catatan | text | |
| dibuat_pada | timestamp | |
| diperbarui_pada | timestamp | |

#### 6. **pembesaran**
Tabel untuk tracking fase pembesaran.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kandang_id | bigint | Foreign key ke kandang |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal_masuk | date | Tanggal masuk |
| jumlah_anak_ayam | integer | Jumlah DOC |
| jenis_kelamin | enum | 'betina', 'jantan' |
| tanggal_siap | date | Tanggal siap produksi |
| jumlah_siap | integer | Jumlah siap |
| umur_hari | integer | Umur dalam hari |
| berat_rata_rata | decimal(8,2) | Berat rata-rata (gram) |
| catatan | text | |
| dibuat_pada | timestamp | |
| diperbarui_pada | timestamp | |

#### 7. **produksi**
Tabel untuk tracking periode produksi telur.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kandang_id | bigint | Foreign key ke kandang |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal_mulai | date | Tanggal mulai produksi |
| jumlah_indukan | integer | Jumlah burung layer |
| umur_mulai_produksi | integer | Umur mulai (hari) |
| tanggal_akhir | date | Tanggal akhir |
| status | enum | 'aktif', 'selesai' |
| catatan | text | |
| dibuat_pada | timestamp | |
| diperbarui_pada | timestamp | |

#### 8. **telur**
Tabel untuk recording produksi telur harian.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| produksi_id | bigint | Foreign key ke produksi |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal | date | Tanggal produksi |
| jumlah | integer | Total telur |
| telur_grade_a | integer | Grade A |
| telur_grade_b | integer | Grade B |
| telur_grade_c | integer | Grade C |
| telur_retak | integer | Telur retak |
| berat_rata_rata | decimal(5,2) | Berat rata-rata (gram) |
| dibuat_pada | timestamp | |
| diperbarui_pada | timestamp | |

#### 9. **pakan**
Tabel untuk recording konsumsi pakan harian.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| produksi_id | bigint | Foreign key ke produksi |
| stok_pakan_id | bigint | Foreign key ke stok_pakan |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal | date | Tanggal konsumsi |
| jumlah_kg | decimal(8,2) | Jumlah dalam kg |
| jumlah_karung | integer | Jumlah karung |
| harga_per_kg | decimal(10,2) | Harga per kg |
| total_biaya | decimal(12,2) | Total biaya |
| dibuat_pada | timestamp | |
| diperbarui_pada | timestamp | |

#### 10. **kematian**
Tabel untuk recording kematian burung.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| produksi_id | bigint | Foreign key ke produksi |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal | date | Tanggal kematian |
| jumlah | integer | Jumlah yang mati |
| penyebab | enum | 'penyakit', 'stress', 'kecelakaan', 'usia', 'tidak_diketahui' |
| keterangan | text | Detail penyebab |
| dibuat_pada | timestamp | |
| diperbarui_pada | timestamp | |

#### 11. **transaksi_pakan**
Tabel untuk tracking transaksi pakan (beli, pakai, dll).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| stok_pakan_id | bigint | Foreign key ke stok_pakan |
| batch_produksi_id | bigint | Foreign key ke batch |
| tipe_transaksi | enum | 'pembelian', 'penggunaan', 'penyesuaian', 'pengembalian' |
| tanggal | date | Tanggal transaksi |
| jumlah_kg | decimal(10,2) | Jumlah dalam kg |
| jumlah_karung | integer | Jumlah karung |
| harga_total | decimal(12,2) | Total harga |
| keterangan | text | |
| pengguna_id | bigint | Foreign key ke pengguna |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 12. **kesehatan**
Tabel untuk recording aktivitas kesehatan dan vaksinasi.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal | date | Tanggal kegiatan |
| tipe_kegiatan | enum | 'vaksinasi', 'pengobatan', 'pemeriksaan_rutin', 'karantina' |
| nama_vaksin_obat | varchar(100) | Nama vaksin/obat |
| jumlah_burung | integer | Jumlah burung |
| gejala | text | Gejala yang diamati |
| diagnosa | text | Diagnosa |
| tindakan | text | Tindakan yang dilakukan |
| biaya | decimal(10,2) | Biaya |
| petugas | varchar(100) | Nama petugas |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### Tabel Keuangan & Penjualan

#### 13. **keuangan**
Tabel untuk tracking keuangan perusahaan.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| tanggal | date | Tanggal transaksi |
| kategori | enum | 'pemasukan', 'pengeluaran' |
| jenis | enum | Lihat list di migration |
| jumlah | decimal(12,2) | Jumlah uang |
| batch_produksi_id | bigint | Foreign key ke batch |
| keterangan | text | |
| nomor_bukti | varchar(50) | Nomor bukti transaksi |
| pengguna_id | bigint | Foreign key ke pengguna |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

#### 14. **penjualan_telur**
Tabel untuk recording penjualan telur.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kode_transaksi | varchar(50) | Kode transaksi unik |
| tanggal | date | Tanggal penjualan |
| batch_produksi_id | bigint | Foreign key ke batch |
| jumlah_butir | integer | Jumlah telur |
| harga_per_butir | decimal(8,2) | Harga per butir |
| total_harga | decimal(12,2) | Total harga |
| pembeli | varchar(100) | Nama pembeli |
| kontak_pembeli | varchar(50) | Kontak pembeli |
| status_pembayaran | enum | 'lunas', 'belum_lunas', 'cicilan' |
| catatan | text | |
| pengguna_id | bigint | Foreign key ke pengguna |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 15. **penjualan_burung**
Tabel untuk recording penjualan burung.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kode_transaksi | varchar(50) | Kode transaksi unik |
| tanggal | date | Tanggal penjualan |
| batch_produksi_id | bigint | Foreign key ke batch |
| kategori | enum | 'DOC', 'grower', 'layer', 'afkir', 'jantan' |
| jumlah_ekor | integer | Jumlah burung |
| berat_rata_rata | decimal(8,2) | Berat rata-rata (gram) |
| harga_per_ekor | decimal(10,2) | Harga per ekor |
| total_harga | decimal(12,2) | Total harga |
| pembeli | varchar(100) | Nama pembeli |
| kontak_pembeli | varchar(50) | Kontak pembeli |
| status_pembayaran | enum | 'lunas', 'belum_lunas', 'cicilan' |
| catatan | text | |
| pengguna_id | bigint | Foreign key ke pengguna |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### Tabel Monitoring & DSS

#### 16. **monitoring_lingkungan**
Tabel untuk monitoring kondisi lingkungan kandang.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| kandang_id | bigint | Foreign key ke kandang |
| batch_produksi_id | bigint | Foreign key ke batch |
| waktu_pencatatan | datetime | Waktu pencatatan |
| suhu | decimal(5,2) | Suhu (°C) |
| kelembaban | decimal(5,2) | Kelembaban (%) |
| intensitas_cahaya | decimal(8,2) | Cahaya (lux) |
| kondisi_ventilasi | varchar(50) | Status ventilasi |
| catatan | text | |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 17. **parameter_standar**
Tabel untuk parameter standar DSS.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| fase | enum | 'DOC', 'grower', 'layer' |
| parameter | varchar(100) | Nama parameter |
| nilai_minimal | decimal(10,2) | Nilai minimal |
| nilai_optimal | decimal(10,2) | Nilai optimal |
| nilai_maksimal | decimal(10,2) | Nilai maksimal |
| satuan | varchar(20) | Satuan pengukuran |
| keterangan | text | |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 18. **analisis_rekomendasi**
Tabel untuk menyimpan hasil analisis dan rekomendasi DSS.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal_analisis | date | Tanggal analisis |
| jenis_analisis | varchar(100) | FCR, Mortalitas, dll |
| nilai_aktual | decimal(10,2) | Nilai aktual |
| nilai_standar | decimal(10,2) | Nilai standar |
| status | enum | 'baik', 'perhatian', 'kritis' |
| analisis | text | Hasil analisis |
| rekomendasi | text | Rekomendasi tindakan |
| prioritas | enum | 'rendah', 'sedang', 'tinggi', 'urgent' |
| target_tindakan | date | Target tanggal tindakan |
| status_tindakan | enum | 'pending', 'dalam_proses', 'selesai', 'diabaikan' |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 19. **laporan_harian**
Tabel untuk summary laporan harian per batch.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| batch_produksi_id | bigint | Foreign key ke batch |
| tanggal | date | Tanggal laporan |
| jumlah_burung | integer | Populasi burung |
| produksi_telur | integer | Jumlah telur |
| jumlah_kematian | integer | Jumlah kematian |
| konsumsi_pakan_kg | decimal(10,2) | Konsumsi pakan (kg) |
| fcr | decimal(5,2) | Feed Conversion Ratio |
| hen_day_production | decimal(5,2) | HDP (%) |
| mortalitas_kumulatif | decimal(5,2) | Mortalitas (%) |
| catatan_kejadian | text | |
| pengguna_id | bigint | Foreign key ke pengguna |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 20. **alert**
Tabel untuk sistem alert/notifikasi.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| tipe_alert | enum | 'stok_pakan', 'kesehatan', dll |
| tingkat_urgency | enum | 'info', 'warning', 'critical' |
| judul | varchar(200) | Judul alert |
| pesan | text | Pesan alert |
| batch_produksi_id | bigint | Foreign key ke batch |
| kandang_id | bigint | Foreign key ke kandang |
| sudah_dibaca | boolean | Status baca |
| waktu_dibaca | datetime | Waktu dibaca |
| pengguna_id | bigint | Foreign key ke pengguna |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 🔗 Relasi Database

### Relasi Utama:

1. **kandang** ← **batch_produksi** (one-to-many)
2. **batch_produksi** ← **penetasan, pembesaran, produksi** (one-to-many)
3. **batch_produksi** ← **pakan, telur, kematian** (one-to-many)
4. **batch_produksi** ← **laporan_harian** (one-to-many)
5. **batch_produksi** ← **analisis_rekomendasi** (one-to-many)
6. **stok_pakan** ← **transaksi_pakan** (one-to-many)
7. **pengguna** ← **keuangan, penjualan_telur, penjualan_burung** (one-to-many)

---

## 💡 Fitur Decision Support System

### 1. **Analisis FCR (Feed Conversion Ratio)**
```sql
FCR = Total Pakan (kg) / (Total Telur × Berat Rata-rata Telur (kg))
```

### 2. **Analisis Hen Day Production (HDP)**
```sql
HDP (%) = (Jumlah Telur Hari Ini / Jumlah Burung) × 100
```

### 3. **Analisis Mortalitas**
```sql
Mortalitas (%) = (Total Kematian / Populasi Awal) × 100
```

### 4. **Monitoring Stok Pakan**
- Alert otomatis jika stok < threshold
- Prediksi kebutuhan pakan berdasarkan konsumsi historis

### 5. **Analisis Profitabilitas**
- Tracking biaya vs pendapatan per batch
- ROI calculation

### 6. **Rekomendasi Tindakan**
- Berdasarkan parameter standar
- Status: baik, perhatian, kritis
- Prioritas tindakan

---

## 🚀 Cara Migrasi

### 1. Fresh Migration (Database Baru)
```bash
php artisan migrate:fresh --seed
```

### 2. Migration (Database Existing)
```bash
php artisan migrate
php artisan db:seed --class=ParameterStandarSeeder
php artisan db:seed --class=KandangSeeder
php artisan db:seed --class=StokPakanSeeder
```

### 3. Rollback (Jika Ada Masalah)
```bash
php artisan migrate:rollback --step=1
```

---

## 📊 Indikator Kinerja Utama (KPI)

### Fase DOC (0-14 hari)
- **Mortalitas**: Target < 5%
- **Konsumsi Pakan**: 3-7 gram/ekor/hari
- **Suhu Kandang**: 32-38°C
- **Kelembaban**: 60-70%

### Fase Grower (15-35 hari)
- **Mortalitas**: Target < 3%
- **Konsumsi Pakan**: 10-20 gram/ekor/hari
- **Berat Badan**: 80-120 gram
- **Suhu Kandang**: 25-30°C

### Fase Layer (36+ hari)
- **Mortalitas**: Target < 2% per bulan
- **HDP**: 70-95%
- **FCR**: 2.0-3.0
- **Konsumsi Pakan**: 18-26 gram/ekor/hari
- **Berat Telur**: 10-14 gram
- **Grade A**: > 85%

---

## 🔐 Keamanan

- Soft deletes untuk data penting
- Foreign key constraints
- User authentication & authorization (owner/operator)
- Audit trail (created_at, updated_at, pengguna_id)

---

## 📝 Catatan Implementasi

1. **Indexes**: Tambahkan index pada kolom yang sering di-query
2. **Backup**: Setup automatic backup database harian
3. **Performance**: Monitor query performance, gunakan eager loading
4. **Validation**: Implement business logic validation di Model/Controller
5. **API**: Pertimbangkan membuat REST API untuk mobile app

---

## 🆘 Support & Maintenance

Untuk pertanyaan atau issue terkait database, silakan hubungi:
- **Developer**: [Your Name]
- **Email**: [Your Email]
- **Repository**: github.com/bolopakw01/vigazafarm

---

**© 2025 VigazaFarm - Decision Support System untuk Monitoring Agribisnis Burung Puyuh**
