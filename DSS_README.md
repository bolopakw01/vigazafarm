# Decision Support System - VigazaFarm
## Sistem Monitoring Terintegrasi untuk Agribisnis Burung Puyuh

---

## 📝 Deskripsi

Sistem ini adalah Decision Support System (DSS) terintegrasi yang dirancang khusus untuk monitoring dan pengelolaan agribisnis burung puyuh secara komprehensif. Sistem ini membantu pemilik dan operator dalam:

- ✅ Monitoring produksi telur secara real-time
- ✅ Tracking kesehatan dan vaksinasi
- ✅ Manajemen pakan dan inventori
- ✅ Analisis keuangan dan profitabilitas
- ✅ Rekomendasi berbasis data (DSS)
- ✅ Alert otomatis untuk kondisi kritis
- ✅ Laporan harian dan bulanan

---

## 🚀 Fitur Utama

### 1. **Manajemen Kandang**
- Multiple kandang dengan berbagai tipe (penetasan, pembesaran, produksi, karantina)
- Tracking kapasitas dan status kandang
- Monitoring kondisi lingkungan per kandang

### 2. **Sistem Batch/Periode Produksi**
- Tracking per batch dari DOC hingga afkir
- Monitoring fase pertumbuhan
- Populasi real-time

### 3. **Manajemen Pakan**
- Inventori pakan dengan multiple jenis
- Tracking konsumsi harian
- Alert stok menipis
- Analisis FCR (Feed Conversion Ratio)

### 4. **Monitoring Kesehatan**
- Jadwal vaksinasi
- Recording pengobatan
- Tracking kematian dengan penyebab
- Alert kondisi kesehatan

### 5. **Keuangan Terintegrasi**
- Tracking pemasukan (penjualan telur & burung)
- Tracking pengeluaran (pakan, obat, operasional)
- Analisis profitabilitas per batch
- Laporan keuangan bulanan

### 6. **Decision Support System**
- Analisis HDP (Hen Day Production)
- Analisis FCR
- Analisis mortalitas
- Rekomendasi otomatis berdasarkan parameter standar
- Prioritas tindakan

### 7. **Reporting & Analytics**
- Dashboard real-time
- Laporan harian otomatis
- Grafik trends produksi
- Export data ke Excel/PDF

---

## 📦 Instalasi

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Laravel 11.x
- Node.js & NPM (untuk frontend assets)

### Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/bolopakw01/vigazafarm.git
cd vigazafarm
```

#### 2. Install Dependencies
```bash
composer install
npm install
```

#### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vigazafarm
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Migrasi Database
```bash
# Fresh installation dengan data seed
php artisan migrate:fresh --seed

# Atau migration biasa
php artisan migrate
```

#### 6. Seed Data (Opsional)
```bash
# Seed semua data
php artisan db:seed

# Atau seed specific seeder
php artisan db:seed --class=ParameterStandarSeeder
php artisan db:seed --class=KandangSeeder
php artisan db:seed --class=StokPakanSeeder
```

#### 7. Build Assets
```bash
npm run build
# atau untuk development
npm run dev
```

#### 8. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 👤 Default Users

Setelah seeding, gunakan kredensial berikut:

**Owner Account:**
- Username: `lopa123`
- Password: `lopa123`
- Role: Owner
- Email: bolopa@gmail.com

**Operator Account:**
- Username: `op1`
- Password: `op1`
- Role: Operator
- Email: op1@gmail.com

---

## 📊 Struktur Database

Database terdiri dari 20+ tabel yang saling berelasi:

### Tabel Master
1. `pengguna` - User management
2. `kandang` - Housing management
3. `batch_produksi` - Production batch tracking
4. `stok_pakan` - Feed inventory
5. `parameter_standar` - Standard parameters for DSS

### Tabel Transaksi
6. `penetasan` - Hatching records
7. `pembesaran` - Growing phase
8. `produksi` - Production phase
9. `telur` - Daily egg production
10. `pakan` - Daily feed consumption
11. `kematian` - Mortality records
12. `transaksi_pakan` - Feed transactions
13. `kesehatan` - Health & vaccination

### Tabel Keuangan
14. `keuangan` - Financial transactions
15. `penjualan_telur` - Egg sales
16. `penjualan_burung` - Quail sales

### Tabel DSS & Monitoring
17. `monitoring_lingkungan` - Environment monitoring
18. `analisis_rekomendasi` - DSS recommendations
19. `laporan_harian` - Daily reports
20. `alert` - Alert notifications

Lihat dokumentasi lengkap di: [DATABASE_DOCUMENTATION.md](DATABASE_DOCUMENTATION.md)

---

## 🔧 Konfigurasi

### Parameter Standar

Parameter standar untuk DSS sudah di-seed otomatis dan dapat dimodifikasi sesuai kebutuhan:

**Fase DOC (0-14 hari):**
- Konsumsi pakan: 3-7 gram/ekor/hari
- Suhu kandang: 32-38°C
- Kelembaban: 60-70%
- Mortalitas: < 5%

**Fase Grower (15-35 hari):**
- Konsumsi pakan: 10-20 gram/ekor/hari
- Berat badan: 80-120 gram
- Suhu kandang: 25-30°C
- Mortalitas: < 3%

**Fase Layer (36+ hari):**
- Konsumsi pakan: 18-26 gram/ekor/hari
- HDP: 70-95%
- FCR: 2.0-3.0
- Berat telur: 10-14 gram
- Mortalitas: < 2% per bulan

### Alert System

Alert otomatis akan muncul untuk kondisi:
- Stok pakan < 100 kg (critical)
- Mortalitas > threshold
- HDP < 70%
- FCR > 3.0
- Suhu/kelembaban di luar range
- Pakan mendekati kadaluarsa

---

## 📱 API Endpoints (Coming Soon)

REST API untuk integrasi dengan mobile app atau sistem lain.

```
GET  /api/dashboard
GET  /api/batches
GET  /api/batches/{id}
POST /api/laporan-harian
GET  /api/alerts
...
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=BatchProductionTest
```

---

## 📈 KPI & Metrics

Sistem menghitung KPI berikut secara otomatis:

### 1. Feed Conversion Ratio (FCR)
```
FCR = Total Pakan (kg) / (Total Telur × Berat Telur (kg))
```
Target: 2.0 - 3.0

### 2. Hen Day Production (HDP)
```
HDP (%) = (Jumlah Telur / Jumlah Burung) × 100
```
Target: 70-95%

### 3. Mortalitas
```
Mortalitas (%) = (Total Mati / Populasi Awal) × 100
```
Target: < 2% per bulan

### 4. Grade A Percentage
```
Grade A (%) = (Telur Grade A / Total Telur) × 100
```
Target: > 85%

---

## 🛠️ Maintenance

### Backup Database
```bash
# Manual backup
php artisan db:backup

# Setup automatic daily backup
# Add to cron: 0 2 * * * cd /path/to/project && php artisan db:backup
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Update Application
```bash
git pull origin main
composer install
npm install
php artisan migrate
php artisan cache:clear
npm run build
```

---

## 🐛 Troubleshooting

### Issue: Migration Failed
```bash
# Reset database
php artisan migrate:fresh --seed
```

### Issue: Permission Denied
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: FCR Calculation Wrong
Pastikan:
1. Data pakan harian ter-record dengan benar
2. Data telur harian ter-record
3. Berat rata-rata telur sudah diisi

---

## 📚 Dokumentasi Lengkap

- [Database Documentation](DATABASE_DOCUMENTATION.md)
- [API Documentation](API_DOCUMENTATION.md) (Coming Soon)
- [User Manual](USER_MANUAL.md) (Coming Soon)

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first.

### Development Workflow
```bash
# Create feature branch
git checkout -b feature/nama-fitur

# Make changes and commit
git add .
git commit -m "Add: Fitur baru XYZ"

# Push and create PR
git push origin feature/nama-fitur
```

---

## 📄 License

This project is licensed under the MIT License.

---

## 💬 Support

Untuk pertanyaan atau bantuan:
- **Issue Tracker**: [GitHub Issues](https://github.com/bolopakw01/vigazafarm/issues)
- **Email**: support@vigazafarm.com
- **Documentation**: [Wiki](https://github.com/bolopakw01/vigazafarm/wiki)

---

## 🎯 Roadmap

### Phase 1 (Current)
- ✅ Database design & migration
- ✅ Basic CRUD operations
- ✅ Authentication & authorization
- ⏳ Dashboard & reporting

### Phase 2
- ⏳ Mobile app (Flutter)
- ⏳ REST API
- ⏳ Advanced analytics
- ⏳ Export to Excel/PDF

### Phase 3
- ⏳ IoT integration (sensors)
- ⏳ Machine learning predictions
- ⏳ Multi-farm support
- ⏳ WhatsApp notifications

---

## 👏 Credits

Developed by:
- **Developer**: [Your Name]
- **Organization**: VigazaFarm
- **Year**: 2025

Built with:
- Laravel 11
- MySQL
- TailwindCSS
- Alpine.js

---

**© 2025 VigazaFarm - Decision Support System untuk Monitoring Agribisnis Burung Puyuh**
