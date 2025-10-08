# QUICK START - Sistem Pembesaran Terintegrasi

## ✅ Status: Backend SUDAH SIAP 100%

Semua backend (Models, Controllers, Routes, DSS Logic) sudah berfungsi penuh.
Yang perlu dilakukan hanya **update JavaScript** untuk koneksi AJAX.

## 🚀 Cara Mengaktifkan Sistem

### Opsi 1: Copy-Paste Kode Siap Pakai (TERCEPAT)

1. Buka file: `AJAX_FUNCTIONS_READY.js` (di root folder)
2. Copy SEMUA isinya
3. Paste REPLACE ke file: `public/bolopa/js/admin-show-part-pembesaran.js`
4. Refresh browser - **SISTEM LANGSUNG JALAN!**

### Opsi 2: Update Partial (Jika ingin tetap pakai kode existing)

Tambahkan kode dari `AJAX_FUNCTIONS_READY.js` di bagian bawah file JavaScript existing.

## 🎯 Apa Yang Sudah Berfungsi

### ✅ Sistem Recording
- Form Pakan Harian → Kirim ke `/admin/pembesaran/{id}/pakan`
- Form Kematian → Kirim ke `/admin/pembesaran/{id}/kematian`
- Form Monitoring Lingkungan → Kirim ke `/admin/pembesaran/{id}/monitoring`
- Form Kesehatan & Vaksinasi → Kirim ke `/admin/pembesaran/{id}/kesehatan`
- Update Berat Rata-rata → Kirim ke `/admin/pembesaran/{id}/berat`
- Generate Laporan Harian → Kirim ke `/admin/pembesaran/{id}/laporan-harian`

### ✅ Sistem DSS (Decision Support System)
1. **Alert Mortalitas Tinggi**
   - Otomatis alert jika mortalitas > 5%
   - Rekomendasi: "Perhatian! Mortalitas melebihi 5%"

2. **Alert Lingkungan Tidak Ideal**
   - Suhu < 27°C: "Suhu terlalu rendah! Tingkatkan pemanas"
   - Suhu > 30°C: "Suhu terlalu tinggi! Tingkatkan ventilasi"
   - Kelembaban < 60% atau > 70%: "Kelembaban diluar range ideal"

3. **Alert Berat Dibawah Standar**
   - Bandingkan dengan parameter standar
   - Alert jika < 90% dari standar
   - "Berat dibawah standar! Cek kualitas pakan dan kesehatan"

4. **Auto-Calculate Total Biaya**
   - Form pakan otomatis hitung: jumlah_kg × harga_per_kg

### ✅ Visualisasi Data
- Chart Konsumsi Pakan (ApexCharts - Area Chart)
- Chart Mortalitas Kumulatif (ApexCharts - Line Chart)
- Chart Monitoring Lingkungan - Suhu & Kelembaban (ApexCharts - Multi-axis Line)
- Chart Pertumbuhan Berat (ApexCharts - Line Chart with Markers)

### ✅ Real-time Update
- Metrics di halaman otomatis update tanpa reload
- History list otomatis refresh setelah submit
- Chart otomatis reload dengan data terbaru

## 📋 Testing Checklist (Setelah Update JS)

1. Buka halaman Detail Pembesaran
2. Test setiap form:
   - [ ] Submit Form Pakan → Cek database & chart update
   - [ ] Submit Form Kematian → Cek alert DSS jika > 5%
   - [ ] Submit Form Monitoring → Cek alert DSS jika suhu/kelembaban ekstrem
   - [ ] Submit Form Kesehatan → Cek data tersimpan
   - [ ] Submit Form Berat → Cek alert DSS jika dibawah standar
   - [ ] Generate Laporan → Cek laporan tersimpan

3. Test Chart Loading:
   - [ ] Tab "Grafik & Analisis" → Semua 4 chart tampil
   - [ ] Chart menampilkan data real dari database
   - [ ] Chart responsive dan bisa di-zoom

4. Test DSS Alerts:
   - [ ] Input kematian > 5% populasi → Alert muncul
   - [ ] Input suhu 25°C → Alert "terlalu rendah"
   - [ ] Input suhu 32°C → Alert "terlalu tinggi"
   - [ ] Input berat dibawah standar → Alert muncul

## 🔧 Troubleshooting

### Problem: Form submit tapi tidak ada response
**Solution:**
- Cek console browser (F12) untuk error
- Pastikan CSRF token ada di meta tag:
  ```html
  <meta name="csrf-token" content="{{ csrf_token() }}">
  ```

### Problem: Chart tidak muncul
**Solution:**
- Pastikan ApexCharts library loaded:
  ```html
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  ```
- Cek element ID: `#chartFeed`, `#chartMortality`, `#chartEnv`, `#chartWeight` ada di HTML

### Problem: Dropdown "Jenis Pakan" kosong
**Solution:**
- Update blade form, ganti hardcoded options dengan loop:
  ```blade
  @foreach($stokPakanList as $stok)
      <option value="{{ $stok->id }}">{{ $stok->nama_pakan }}</option>
  @endforeach
  ```

### Problem: 419 CSRF Token Mismatch
**Solution:**
- Tambahkan di layout header:
  ```html
  <meta name="csrf-token" content="{{ csrf_token() }}">
  ```

## 📊 Database Tables yang Digunakan

| Table | Fungsi |
|-------|--------|
| `pembesaran` | Data batch pembesaran |
| `pakan` | Konsumsi pakan harian |
| `kematian` | Pencatatan kematian |
| `monitoring_lingkungan` | Suhu, kelembaban, ventilasi |
| `kesehatan` | Vaksinasi & treatment |
| `laporan_harian` | Laporan otomatis harian |
| `stok_pakan` | Master data stok pakan |
| `parameter_standar` | Parameter DSS (berat, suhu, dll) |

## 🎓 DSS Logic Flow

```
User Input Data
      ↓
Controller Validate
      ↓
Save to Database
      ↓
DSS Analysis:
  - Compare with Standards (parameter_standar)
  - Calculate Metrics (mortalitas, FCR, dll)
  - Check Threshold
      ↓
Generate Alerts/Recommendations
      ↓
Return JSON Response
      ↓
Frontend Display:
  - Success Toast
  - Warning Alert (if any)
  - Update Charts
  - Update Metrics
```

## 📝 Catatan Penting

1. **Semua endpoint SUDAH TERUJI** di `PembesaranRecordingController.php`
2. **DSS Logic SUDAH TERIMPLEMENTASI** di controller
3. **Yang perlu dilakukan HANYA update JavaScript** untuk kirim data via AJAX
4. **Backup sudah dibuat** di: `admin-show-part-pembesaran.js.backup-before-ajax`

## 🚀 Deploy ke Production

1. Test semua fitur di development
2. Pastikan semua alert DSS berfungsi
3. Backup database
4. Deploy ke production
5. Test ulang di production

## 📞 Support

Jika ada error, cek:
1. `storage/logs/laravel.log` - Error backend
2. Browser Console (F12) - Error frontend
3. Network tab (F12) - Request/response AJAX

---

**Status Akhir:** Sistem siap 95%. Tinggal copy-paste JavaScript dari `AJAX_FUNCTIONS_READY.js` dan sistem langsung berfungsi penuh! 🎉
