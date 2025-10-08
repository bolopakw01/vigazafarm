# Perbaikan: Column 'produksi_id' Cannot Be Null di Tabel Kematian

## 📋 Masalah
Error saat submit form kematian:
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'produksi_id' 
cannot be null (Connection: mysql, SQL: insert into `kematian` ...)
```

## 🔍 Root Cause
Sama seperti tabel `pakan`, tabel `kematian` juga punya kolom `produksi_id` yang **NOT NULL**. 

**Masalah:**
- Fase **pembesaran** belum punya `produksi_id` (masih null)
- Controller insert dengan `'produksi_id' => null`
- Database constraint: `produksi_id BIGINT NOT NULL` → **REJECT!**

## ✅ Solusi
Make column `produksi_id` **NULLABLE** di tabel `kematian`.

### Migration Created
File: `database/migrations/2025_10_07_032943_make_produksi_id_nullable_in_kematian_table.php`

```php
public function up(): void
{
    Schema::table('kematian', function (Blueprint $table) {
        // Make produksi_id nullable because pembesaran phase doesn't have produksi_id yet
        $table->unsignedBigInteger('produksi_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('kematian', function (Blueprint $table) {
        // Revert back to NOT NULL (but this will fail if there are null values)
        $table->unsignedBigInteger('produksi_id')->nullable(false)->change();
    });
}
```

### Run Migration
```bash
php artisan migrate
```

**Output:**
```
INFO  Running migrations.

2025_10_07_032943_make_produksi_id_nullable_in_kematian_table ........... 210.42ms DONE
```

## 📊 Tabel yang Affected

### ✅ Sudah di-fix:
1. **pakan** - `produksi_id` → nullable ✅ (migration sebelumnya)
2. **kematian** - `produksi_id` → nullable ✅ (migration ini)

### ✅ Tidak perlu di-fix:
3. **monitoring_lingkungan** - ❌ Tidak punya kolom `produksi_id` (hanya `batch_produksi_id`)
4. **kesehatan** - ❌ Tidak punya kolom `produksi_id` (hanya `batch_produksi_id`)
5. **laporan_harian** - ❌ Tidak punya kolom `produksi_id` (hanya `batch_produksi_id`)

## 🧪 Testing

### 1. Submit Form Kematian
```
Tanggal: 2025-10-07
Jumlah Ekor: 1
Penyebab: Penyakit
Catatan: waduh boy
```

**Before Fix:**
```
❌ SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'produksi_id' cannot be null
```

**After Fix:**
```
✅ Data kematian berhasil disimpan
✅ Mortalitas update
✅ DSS alert muncul jika > 5%
```

### 2. Verify Database
```bash
php artisan tinker
```

```php
use App\Models\Kematian;

// Check latest record
$k = Kematian::latest()->first();
echo "Produksi ID: " . ($k->produksi_id ?? 'NULL') . PHP_EOL;
echo "Batch Produksi ID: " . $k->batch_produksi_id . PHP_EOL;
echo "Jumlah: " . $k->jumlah . PHP_EOL;
echo "Penyebab: " . $k->penyebab . PHP_EOL;

// Output:
// Produksi ID: NULL
// Batch Produksi ID: PB-20251006-001
// Jumlah: 1
// Penyebab: penyakit
```

### 3. Check Column Definition
```sql
DESCRIBE kematian;
```

**Expected:**
```
+-------------------+---------------------+------+-----+---------+----------------+
| Field             | Type                | Null | Key | Default | Extra          |
+-------------------+---------------------+------+-----+---------+----------------+
| produksi_id       | bigint(20) unsigned | YES  | MUL | NULL    |                |
| batch_produksi_id | varchar(50)         | YES  | MUL | NULL    |                |
+-------------------+---------------------+------+-----+---------+----------------+
```

Note: `Null = YES` untuk `produksi_id` ✅

## 📝 Related Migrations

### History:
1. `2025_10_06_172904_fix_all_batch_produksi_id_columns.php`
   - Fixed `batch_produksi_id` type: bigint → varchar(50)
   - Tables: pakan, kematian, monitoring, kesehatan, laporan

2. `2025_10_06_173134_make_produksi_id_nullable_in_pakan_table.php`
   - Made `produksi_id` nullable in `pakan` table

3. **`2025_10_07_032943_make_produksi_id_nullable_in_kematian_table.php`** ← THIS ONE
   - Made `produksi_id` nullable in `kematian` table

## 🔄 Data Flow

### Fase Pembesaran (saat ini):
```
Pembesaran
  ├── batch_produksi_id: "PB-20251006-001"
  ├── produksi_id: NULL ← Belum ada produksi
  │
  ├── Pakan
  │     ├── batch_produksi_id: "PB-20251006-001"
  │     └── produksi_id: NULL ✅
  │
  └── Kematian
        ├── batch_produksi_id: "PB-20251006-001"
        └── produksi_id: NULL ✅
```

### Fase Produksi (future):
```
Produksi (id: 1)
  ├── batch_produksi_id: "PB-20251006-001"
  │
  ├── Pakan
  │     ├── batch_produksi_id: "PB-20251006-001"
  │     └── produksi_id: 1 ✅
  │
  └── Kematian
        ├── batch_produksi_id: "PB-20251006-001"
        └── produksi_id: 1 ✅
```

## 🎯 Why This Design?

### `batch_produksi_id` (varchar):
- **Primary tracking ID**
- Follows batch across ALL phases (penetasan → pembesaran → produksi)
- Human-readable: "PB-20251006-001"
- **Always required** (but nullable for flexibility)

### `produksi_id` (bigint nullable):
- **Phase-specific ID**
- Only exists when batch enters **produksi phase**
- NULL during pembesaran phase
- Foreign key to `produksi` table when not null

## ✅ Status
🟢 **RESOLVED**

Form kematian sekarang bisa submit tanpa error!

## 📌 Next Steps
Test semua form recording:
- ✅ Pakan → Working
- ✅ Kematian → **Working now!**
- ⏳ Monitoring → Should work (no produksi_id column)
- ⏳ Kesehatan → Should work (no produksi_id column)
- ⏳ Berat → Need to test
- ⏳ Laporan → Need to test (no produksi_id column)
