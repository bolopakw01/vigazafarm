# 🔧 PERBAIKAN: "Gagal Menghubungi Server"

## 📋 Masalah yang Ditemukan

Saat melakukan pencatatan (submit form), muncul error **"Gagal menghubungi server"**.

### Penyebab Utama:
1. **Field name tidak sesuai** antara form blade dan controller
   - Form menggunakan: `name="jenis_pakan"` dengan value string ("Starter", "Grower", "Layer")
   - Controller mengharapkan: `stok_pakan_id` (integer, foreign key ke tabel stok_pakan)

2. **Data stok pakan tidak ditampilkan** di dropdown
   - Controller sudah mengirim `$stokPakanList`, tapi form tidak menggunakannya

## ✅ Perbaikan yang Dilakukan

### 1. Update Form Blade (`_tab-show-pembesaran.blade.php`)
**File:** `resources/views/admin/pages/pembesaran/partials/_tab-show-pembesaran.blade.php`

**Sebelum:**
```blade
<select class="form-select" name="jenis_pakan" required>
    <option value="">-- Pilih Jenis --</option>
    <option value="Starter">Starter</option>
    <option value="Grower">Grower</option>
    <option value="Layer">Layer</option>
</select>
```

**Sesudah:**
```blade
<select class="form-select" name="stok_pakan_id" required>
    <option value="">-- Pilih Pakan --</option>
    @foreach($stokPakanList as $stok)
    <option value="{{ $stok->id }}" data-harga="{{ $stok->harga_per_kg }}">
        {{ $stok->nama_pakan }} ({{ $stok->jenis_pakan }}) - Stok: {{ number_format($stok->stok_kg, 0) }} kg
    </option>
    @endforeach
</select>
```

**Manfaat:**
- ✅ Field name sesuai dengan yang diharapkan controller (`stok_pakan_id`)
- ✅ Dropdown menampilkan data real dari database (5 jenis pakan tersedia)
- ✅ Menampilkan informasi stok tersedia
- ✅ Menyimpan `data-harga` untuk auto-fill harga

### 2. Update JavaScript (`admin-show-part-pembesaran.js`)
**File:** `public/bolopa/js/admin-show-part-pembesaran.js`

**Perubahan:**
```javascript
// Tambah auto-fill harga dari dropdown
const stokPakanSelect = pakanForm.querySelector('select[name="stok_pakan_id"]');

stokPakanSelect?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const harga = selectedOption.getAttribute('data-harga') || 0;
    if (hargaPerKg) hargaPerKg.value = harga;
    updateTotal();
});

// Fix submit data
const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/pakan`, {
    tanggal: formData.get('tanggal'),
    stok_pakan_id: parseInt(formData.get('stok_pakan_id')), // FIXED: was 'jenis_pakan'
    jumlah_kg: parseFloat(formData.get('jumlah_kg')),
    jumlah_karung: parseInt(formData.get('jumlah_karung')) || 0
});
```

**Manfaat:**
- ✅ Auto-fill harga saat memilih jenis pakan
- ✅ Mengirim data dengan field name yang benar
- ✅ Parse data type dengan benar (integer untuk ID, float untuk jumlah)

## 🧪 Cara Testing

### 1. Pastikan Server Laravel Berjalan
```powershell
cd d:\CODE\XAMPP\XAMPP-8.2.12\htdocs\vigazafarm
php artisan serve
```
Atau pastikan XAMPP Apache sudah running.

### 2. Buka Halaman Detail Pembesaran
```
http://localhost/vigazafarm/public/admin/pembesaran/{id}
```
Contoh: `http://localhost/vigazafarm/public/admin/pembesaran/1`

### 3. Klik Tab "Recording Harian"
- Tab kedua dengan icon clipboard

### 4. Test Form Pencatatan Pakan
**Langkah:**
1. Pilih tanggal (default hari ini)
2. **Pilih Jenis Pakan** dari dropdown → Sekarang menampilkan 5 pilihan dengan stok:
   - Pakan Starter BR-1 (Starter) - Stok: 500 kg
   - Pakan Grower 511 (Grower) - Stok: 1,000 kg
   - Pakan Layer Hi-Pro-Vite 124 (Layer) - Stok: 2,000 kg
   - Pakan Layer 124 BR (Layer) - Stok: 1,500 kg
   - Pakan Organik Premium (Layer) - Stok: 250 kg
3. Input **Jumlah (kg)**: misalnya `10`
4. Otomatis field **Harga per kg** dan **Total Biaya** akan terisi
5. Klik **Simpan Pakan**

**Expected Result:**
- ✅ Muncul toast hijau: "✅ Data pakan berhasil disimpan"
- ✅ Form di-reset (kosong kembali)
- ✅ (Jika sudah ada chart) Chart pakan akan di-reload dengan data baru

**Jika masih error:**
- Buka **Browser Console** (F12 → Console)
- Screenshot error yang muncul
- Check **Network tab** untuk melihat response dari server

### 5. Verifikasi Data Tersimpan
```powershell
php artisan tinker
```
Lalu jalankan:
```php
\App\Models\Pakan::latest()->first();
```
Seharusnya menampilkan data pakan yang baru saja disimpan.

## 📊 Data yang Tersedia

### Stok Pakan di Database:
| ID | Nama Pakan | Jenis | Harga/kg | Stok |
|----|------------|-------|----------|------|
| 1 | Pakan Starter BR-1 | Starter | Rp 9,500 | 500 kg |
| 2 | Pakan Grower 511 | Grower | Rp 8,500 | 1,000 kg |
| 3 | Pakan Layer Hi-Pro-Vite 124 | Layer | Rp 7,800 | 2,000 kg |
| 4 | Pakan Layer 124 BR | Layer | Rp 7,500 | 1,500 kg |
| 5 | Pakan Organik Premium | Layer | Rp 12,000 | 250 kg |

## 🔍 Troubleshooting

### Problem: Dropdown masih menampilkan "Starter, Grower, Layer" saja
**Solution:** 
- Clear browser cache: `Ctrl + Shift + R` (hard refresh)
- Pastikan file blade sudah di-update
- Check apakah `$stokPakanList` ada di controller `show()` method

### Problem: Masih muncul "Gagal menghubungi server"
**Check:**
1. **Browser Console (F12)** - Lihat error JavaScript
2. **Network tab** - Check response dari `/admin/pembesaran/{id}/pakan`
3. **Laravel Log** - `storage/logs/laravel.log`
4. **CSRF Token** - Pastikan `<meta name="csrf-token">` ada di layout

### Problem: Error "stok_pakan_id field is required"
**Cause:** Form masih menggunakan field lama
**Solution:** Pastikan field `name="stok_pakan_id"` (bukan `jenis_pakan`)

### Problem: Error "Call to a member function getAttribute() on null"
**Cause:** JavaScript mencari element yang tidak ada
**Solution:** Hard refresh browser (`Ctrl + Shift + R`)

## 🎯 Next Steps

Setelah form pakan berhasil:

1. **Test Form Kematian** - Sudah benar, tidak perlu perbaikan
2. **Test Form Monitoring** - Sudah benar
3. **Test Form Kesehatan** - Sudah benar
4. **Test Form Berat** - Sudah benar
5. **Verify DSS Alerts** - Test dengan input data yang trigger alert:
   - Kematian > 5% → Muncul warning
   - Suhu < 27°C atau > 30°C → Muncul warning
   - Kelembaban < 60% atau > 70% → Muncul warning

## 📝 Summary

**Root Cause:** Mismatch antara field name di form dan yang diharapkan API

**Solution:** 
- Form sekarang menggunakan data dari database (`$stokPakanList`)
- Field name diubah dari `jenis_pakan` (string) menjadi `stok_pakan_id` (integer FK)
- JavaScript mengirim data dengan field name yang sesuai

**Status:** ✅ **FIXED** - Siap untuk testing!
