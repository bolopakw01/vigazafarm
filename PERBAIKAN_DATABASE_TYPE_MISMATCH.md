# 🔧 PERBAIKAN: Database Type Mismatch untuk `batch_produksi_id`

## 📋 Masalah yang Ditemukan

Saat submit form pencatatan pakan, muncul error SQL:

```
SQLSTATE[22007]: Invalid datetime format: 1366 
Incorrect integer value: 'PB-20251006-001' for column `vigazafarm_db`.`pakan`.`batch_produksi_id` at row 1

SQL: insert into `pakan` (...) values (PB-20251006-001, ...)
```

### Root Cause Analysis:

**Type Mismatch di Database:**

| Tabel | Kolom | Tipe Data (Sebelum) | Value Expected |
|-------|-------|---------------------|----------------|
| `pembesaran` | `batch_produksi_id` | ✅ **`varchar(50)`** | `'PB-20251006-001'` (string) |
| `pakan` | `batch_produksi_id` | ❌ **`bigint`** | Integer saja |

**Masalah:**
- Controller mencoba insert string `'PB-20251006-001'` ke kolom `bigint`
- MySQL tidak bisa convert string ke integer → Error

**Mengapa ini terjadi:**
- Kemungkinan migration lama membuat `batch_produksi_id` sebagai foreign key integer
- Tapi tabel `pembesaran` menggunakan string sebagai primary key untuk batch

---

## ✅ Solusi: Database Migration

### 1. Buat Migration File
**Command:**
```bash
php artisan make:migration fix_pakan_batch_produksi_id_type --table=pakan
```

**File created:** `database/migrations/2025_10_06_172633_fix_pakan_batch_produksi_id_type.php`

### 2. Migration Content

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pakan', function (Blueprint $table) {
            // Drop foreign key jika ada
            $table->dropForeign(['batch_produksi_id']);
            
            // Ubah tipe kolom dari bigint ke varchar
            $table->string('batch_produksi_id', 50)->nullable()->change();
            
            // Re-add foreign key constraint (optional)
            // $table->foreign('batch_produksi_id')
            //       ->references('batch_produksi_id')
            //       ->on('pembesaran')
            //       ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pakan', function (Blueprint $table) {
            // Kembalikan ke bigint jika rollback
            $table->dropForeign(['batch_produksi_id']);
            $table->unsignedBigInteger('batch_produksi_id')->nullable()->change();
        });
    }
};
```

### 3. Run Migration
**Command:**
```bash
php artisan migrate
```

**Output:**
```
INFO  Running migrations.
2025_10_06_172633_fix_pakan_batch_produksi_id_type ........... 211.05ms DONE
```

### 4. Verify Change
**Command:**
```bash
php artisan tinker --execute="echo Schema::getColumnType('pakan', 'batch_produksi_id');"
```

**Output:**
```
varchar  ✅
```

---

## 🔍 Technical Details

### Before Migration:

**Table: `pakan`**
```sql
Field: batch_produksi_id
Type: bigint(20) unsigned
Null: YES
```

**Problem:**
```php
Pakan::create([
    'batch_produksi_id' => 'PB-20251006-001',  // ❌ String to bigint
    // ...
]);
// Error: Incorrect integer value
```

### After Migration:

**Table: `pakan`**
```sql
Field: batch_produksi_id
Type: varchar(50)
Null: YES
```

**Now works:**
```php
Pakan::create([
    'batch_produksi_id' => 'PB-20251006-001',  // ✅ String to varchar
    // ...
]);
// Success!
```

---

## 🧪 Testing

### 1. Verify Database Structure

**Check column type:**
```bash
php artisan tinker
```

```php
Schema::getColumnType('pakan', 'batch_produksi_id');
// Output: "varchar"
```

**Check full column details:**
```php
DB::select('DESCRIBE pakan');
// Look for batch_produksi_id row:
// Type: varchar(50)
```

### 2. Test Form Submission

**Navigate to:**
```
http://localhost/vigazafarm/public/admin/pembesaran/4
```

**Submit Form Pakan:**
1. Tab "Recording Harian"
2. Pilih pakan: Pakan Grower 511
3. Jumlah kg: `0.07` (70 gram)
4. Klik "Simpan Pakan"

**Expected Result:**
- ✅ Toast hijau: "Data pakan berhasil disimpan"
- ✅ No SQL errors
- ✅ Data tersimpan di database

### 3. Verify Data in Database

**Via Tinker:**
```bash
php artisan tinker
```

```php
$pakan = \App\Models\Pakan::latest()->first();
dd([
    'id' => $pakan->id,
    'batch_produksi_id' => $pakan->batch_produksi_id,  // Should be string
    'stok_pakan_id' => $pakan->stok_pakan_id,
    'jumlah_kg' => $pakan->jumlah_kg,
    'tanggal' => $pakan->tanggal
]);
```

**Expected Output:**
```php
[
    "id" => 1,
    "batch_produksi_id" => "PB-20251006-001",  // ✅ String value
    "stok_pakan_id" => 2,
    "jumlah_kg" => "0.07",
    "tanggal" => "2025-10-06"
]
```

---

## 🐛 Troubleshooting

### Problem: Migration fails with "Unknown column"

**Error:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'batch_produksi_id'
```

**Solution:**
Check if column exists:
```bash
php artisan tinker --execute="Schema::hasColumn('pakan', 'batch_produksi_id');"
```

If `false`, kolom tidak ada, migration tidak perlu dijalankan.

### Problem: Foreign key constraint error

**Error:**
```
SQLSTATE[23000]: Cannot drop index 'pakan_batch_produksi_id_foreign': needed in a foreign key constraint
```

**Solution:**
Migration sudah include `dropForeign()` sebelum `change()`. Jika masih error, cek nama foreign key:

```bash
SHOW CREATE TABLE pakan;
```

Update migration dengan nama foreign key yang tepat:
```php
$table->dropForeign('pakan_batch_produksi_id_foreign');
```

### Problem: Data lama hilang setelah migration

**Cause:** Data `bigint` di-convert ke `varchar`, data lama mungkin tidak valid

**Check data:**
```php
\App\Models\Pakan::all();
```

**If data corrupted:**
Rollback dan fix data sebelum re-run:
```bash
php artisan migrate:rollback --step=1
```

Clean data, then re-migrate:
```bash
php artisan migrate
```

### Problem: Still getting type error after migration

**Check Laravel model cache:**
```bash
php artisan cache:clear
php artisan config:clear
```

**Check database connection:**
```bash
php artisan tinker --execute="DB::connection()->getPdo();"
```

Make sure connected to correct database.

---

## 📊 Impact Analysis

### Tables Affected:
- ✅ `pakan` - Column type changed

### Related Tables (May need similar fix):
Check if other tables also have `batch_produksi_id` with wrong type:

```php
// Check kematian table
Schema::getColumnType('kematian', 'batch_produksi_id');

// Check monitoring_lingkungan table  
Schema::getColumnType('monitoring_lingkungan', 'batch_produksi_id');

// Check kesehatan table
Schema::getColumnType('kesehatan', 'batch_produksi_id');

// Check laporan_harian table
Schema::getColumnType('laporan_harian', 'batch_produksi_id');
```

**If any return `bigint`, create similar migrations for those tables.**

---

## 🔄 Rollback (If Needed)

**Command:**
```bash
php artisan migrate:rollback --step=1
```

This will execute the `down()` method, reverting `batch_produksi_id` back to `bigint`.

**When to rollback:**
- If migration causes data corruption
- If you need to restructure the approach
- If foreign keys break

---

## 📝 Best Practices Learned

### 1. Consistent Data Types
When using string IDs, ensure **all foreign key references** also use string type:

**Pattern A (String Primary Key):**
```php
// Master table
$table->string('batch_produksi_id', 50)->primary();

// Child tables (all must be string)
$table->string('batch_produksi_id', 50)->nullable();
$table->foreign('batch_produksi_id')...
```

**Pattern B (Integer Primary Key with String Reference):**
```php
// Master table
$table->id();  // bigint primary key
$table->string('batch_code', 50)->unique();

// Child tables (use bigint for FK)
$table->foreignId('pembesaran_id')->constrained();
```

### 2. Migration Planning
- Check **all related tables** before creating foreign keys
- Use **consistent naming** for foreign key columns
- Add **indexes** for foreign key columns

### 3. Type Safety
- Use **model casts** to ensure type consistency:
  ```php
  protected $casts = [
      'batch_produksi_id' => 'string',
  ];
  ```

---

## 🎯 Summary

**Problem:** `pakan.batch_produksi_id` was `bigint` but needed to store string values like `'PB-20251006-001'`

**Solution:** Created migration to change column type to `varchar(50)`

**Result:** ✅ Form submissions now work without SQL errors

**Status:** 🚀 **FIXED** - Ready for testing all recording forms!

---

## 🧪 Next Steps

1. ✅ Test form pakan submission
2. Test other forms (kematian, monitoring, kesehatan)
3. Check if other tables need similar fixes
4. Test data relationships (joins, eager loading)
5. Verify DSS alerts still work

**If you encounter similar errors on other forms, we'll create similar migrations for those tables.**
