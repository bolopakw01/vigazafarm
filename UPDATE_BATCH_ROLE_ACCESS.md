# Update Dokumentasi - Batch Auto-Generate & Role Access Control

## 🎯 Perubahan yang Telah Diimplementasikan

### 1. **Auto-Generate Batch Unik**

#### Format Batch
```
PTN-YYYYMMDD-XXX
```
- **PTN**: Penetasan (prefix)
- **YYYYMMDD**: Tanggal pembuatan (contoh: 20251002)
- **XXX**: Random number 000-999

#### Contoh Batch
```
PTN-20251002-001
PTN-20251002-152
PTN-20251003-789
```

#### Implementasi
- **Controller**: `PenetasanController@generateUniqueBatch()`
- **Validasi**: Loop hingga menemukan batch yang unik
- **Auto**: Dijalankan otomatis saat `store()` dipanggil

#### Tampilan di Form
**Create Form:**
```
┌──────────────────────────────────────────┐
│ ℹ️ Kode Batch: Akan di-generate        │
│   otomatis saat data disimpan           │
│   (Format: PTN-YYYYMMDD-XXX)           │
└──────────────────────────────────────────┘
```

**Edit Form:**
```
┌──────────────────────────────────────────┐
│ 📋 Kode Batch: PTN-20251002-001        │
└──────────────────────────────────────────┘
```

---

### 2. **Edit Status di Override (Owner Only)**

#### Desain Sederhana
Form edit sekarang menampilkan dropdown status yang sederhana:

```
┌─────────────────────────────────────────┐
│ 🛡️ Kontrol Owner [Owner Only]          │
├─────────────────────────────────────────┤
│ ⚠️ Fitur ini memungkinkan Owner untuk  │
│    mengubah status secara manual       │
│                                         │
│ Override Status:                        │
│ [Dropdown]                              │
│   -- Gunakan status otomatis --        │
│   Proses                                │
│   Aktif                                 │
│   Selesai                               │
│   Gagal                                 │
│                                         │
│ Status saat ini: [Badge Aktif]         │
└─────────────────────────────────────────┘
```

#### Fitur
- **Dropdown sederhana** tanpa emoji berlebihan
- **Opsi default** untuk menggunakan status otomatis
- **Badge** menunjukkan status saat ini
- **Border merah** menandakan kontrol sensitif
- **Alert warning** mengingatkan Owner

---

### 3. **Role Access Control - Operator**

#### Sidebar Menu

**Owner:**
```
┌─────────────────┐
│ [Operasional] [Master] ← Kedua tab tersedia
│                 │
│ Dashboard       │
│                 │
│ === Operasional ===
│ 🥚 Penetasan    │
│ 🐣 Pembesaran   │
│ 📦 Produksi     │
│                 │
│ === Master ===  │
│ 🏠 Kandang      │
│ 👥 Karyawan     │
└─────────────────┘
```

**Operator:**
```
┌─────────────────┐
│ [Operasional]   ← Hanya tab Operasional
│                 │
│ Dashboard       │
│                 │
│ === Operasional ===
│ 🥚 Penetasan    │
│ 🐣 Pembesaran   │
│ 📦 Produksi     │
│                 │
│ (Master tersembunyi)
└─────────────────┘
```

#### Route Protection

**Middleware:** `owner`

**Protected Routes:**
```php
Route::middleware('owner')->group(function () {
    Route::get('/admin/kandang', ...);
    Route::get('/admin/karyawan', ...);
});
```

**Error Response:**
```
HTTP 403 Forbidden
"Akses ditolak. Hanya Owner yang dapat mengakses halaman ini."
```

---

## 📁 File yang Dimodifikasi

### Controller
**File:** `app/Http/Controllers/PenetasanController.php`

**Perubahan:**
1. ✅ Menambahkan method `generateUniqueBatch()`
2. ✅ Auto-generate batch di `store()`
3. ✅ Menghapus validasi batch manual
4. ✅ Update logic status override

**Method Baru:**
```php
private function generateUniqueBatch()
{
    do {
        $date = date('Ymd');
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $batch = "PTN-{$date}-{$random}";
        $exists = Penetasan::where('batch', $batch)->exists();
    } while ($exists);
    
    return $batch;
}
```

### Middleware
**File:** `app/Http/Middleware/EnsureUserIsOwner.php`

**Fungsi:**
```php
public function handle(Request $request, Closure $next): Response
{
    if (auth()->check() && auth()->user()->peran === 'owner') {
        return $next($request);
    }
    
    abort(403, 'Akses ditolak. Hanya Owner yang dapat mengakses halaman ini.');
}
```

### Bootstrap
**File:** `bootstrap/app.php`

**Middleware Alias:**
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'owner' => \App\Http\Middleware\EnsureUserIsOwner::class,
    ]);
})
```

### Routes
**File:** `routes/web.php`

**Struktur:**
```php
Route::middleware('auth')->group(function () {
    // Master routes (Owner only)
    Route::middleware('owner')->group(function () {
        Route::get('/admin/kandang', ...);
        Route::get('/admin/karyawan', ...);
    });
    
    // Operational routes (All)
    Route::get('/admin/penetasan', ...);
    Route::get('/admin/pembesaran', ...);
    Route::get('/admin/produksi', ...);
});
```

### Views

**File:** `resources/views/admin/pages/penetasan/create-penetasan.blade.php`
- ✅ Menghapus input batch manual
- ✅ Menambahkan info alert auto-generate

**File:** `resources/views/admin/pages/penetasan/edit-penetasan.blade.php`
- ✅ Menampilkan batch sebagai informasi read-only
- ✅ Menambahkan dropdown status override sederhana (owner only)
- ✅ Styling dengan border merah dan alert warning

**File:** `resources/views/admin/partials/sidebar.blade.php`
- ✅ Menyembunyikan tab Master untuk operator
- ✅ Menyembunyikan menu Kandang & Karyawan untuk operator
- ✅ Menggunakan `@if(auth()->user()->peran === 'owner')`

---

## 🧪 Testing Checklist

### Batch Auto-Generate
- [ ] Create data penetasan baru
- [ ] Pastikan batch ter-generate otomatis (PTN-YYYYMMDD-XXX)
- [ ] Create beberapa data di hari yang sama
- [ ] Pastikan semua batch unik (angka random berbeda)
- [ ] Check di tabel index batch muncul

### Status Override (Owner)
- [ ] Login sebagai Owner
- [ ] Edit data penetasan
- [ ] Lihat section "Kontrol Owner"
- [ ] Ubah status menggunakan dropdown
- [ ] Save dan verify status berubah

### Role Access (Operator)
- [ ] Login sebagai Operator
- [ ] Cek sidebar - tab Master tidak muncul
- [ ] Menu Kandang & Karyawan tidak terlihat
- [ ] Try akses `/admin/kandang` langsung
- [ ] Harus muncul error 403
- [ ] Cek hanya bisa akses Operasional

### Role Access (Owner)
- [ ] Login sebagai Owner
- [ ] Cek sidebar - tab Master muncul
- [ ] Menu Kandang & Karyawan terlihat
- [ ] Bisa akses semua menu tanpa error

---

## 📊 Summary

| Fitur | Status | Deskripsi |
|-------|--------|-----------|
| Auto-generate Batch | ✅ | Format PTN-YYYYMMDD-XXX, unique check |
| Batch di Create Form | ✅ | Info alert, auto saat save |
| Batch di Edit Form | ✅ | Display read-only |
| Status Override | ✅ | Dropdown sederhana, owner only |
| Sidebar Operator | ✅ | Hanya Operasional tab |
| Route Protection | ✅ | Middleware owner untuk Master |
| Error 403 | ✅ | Operator tidak bisa akses Master |

---

## 🚀 Deployment Notes

**Tidak Perlu Migration Baru** - Kolom batch sudah ada dari migration sebelumnya

**Yang Perlu Dilakukan:**
1. ✅ Pull code terbaru
2. ✅ Clear cache: `php artisan cache:clear`
3. ✅ Clear route cache: `php artisan route:clear`
4. ✅ Clear view cache: `php artisan view:clear`
5. ✅ Test dengan user Owner
6. ✅ Test dengan user Operator

**Environment:**
- Tidak ada perubahan `.env`
- Tidak ada dependency baru

---

## 🎨 UI Changes Summary

**Before:**
- Batch input manual di form
- Status edit di modal SweetAlert
- Operator bisa lihat menu Master

**After:**
- Batch auto-generate (no input)
- Status override di form edit (sederhana)
- Operator hanya lihat Operasional

---

Semua perubahan telah diimplementasikan dan siap untuk testing! 🎉
