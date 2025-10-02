# Owner Override - Unified Control Section

## 📋 Overview
Section **Owner Override** sekarang menggabungkan **dua kontrol** dalam satu tempat:
1. **Override Status** - Mengubah status penetasan manual
2. **Hasil Penetasan** - Input data hasil menetas (tanggal, jumlah, DOC, persentase)

## 🎯 Konsep Unified Control

### Before (Terpisah)
```
❌ Form memiliki 2 section terpisah:
   - Section: Hasil Penetasan (hidden sampai waktu tercapai)
   - Section: Status Override (di bagian berbeda)
```

### After (Tergabung)
```
✅ Form memiliki 1 section terpadu:
   - Toggle: Owner Override Control
   - Saat aktif → muncul 1 section berisi:
     * Override Status (dropdown)
     * Hasil Penetasan (4 input fields)
```

## 🎨 UI Structure

### State Default (Toggle OFF)
```
┌─ Form Edit Penetasan ─────────────────────┐
│ • Informasi Kandang                       │
│ • Data Telur & Penetasan                  │
│ • Kondisi Lingkungan                      │
│ • Catatan                                 │
│                                           │
│ ═══════════════════════════════════════  │ ← Divider
│                                           │
│ 🛡️ Owner Override Control                 │
│ [○────] Aktifkan untuk override           │
│ ⚠️ Status: [Badge Aktif]                  │
│                                           │
│ [Update Data] [Batal]                     │
└───────────────────────────────────────────┘
```

### State Active (Toggle ON)
```
┌─ Form Edit Penetasan ─────────────────────┐
│ ... (sections sebelumnya) ...             │
│                                           │
│ ═══════════════════════════════════════  │
│                                           │
│ 🛡️ Owner Override Control                 │
│ [────●] ✓ Override aktif - Hasil &        │
│         status tersedia                   │
│ ⚠️ Status: [Badge Aktif]                  │
│                                           │
│ ┌─ Kontrol Owner (Owner Only) ──────────┐│
││ ⚠️ Perhatian: Fitur ini memungkinkan   ││
││    Owner mengubah hasil & status       ││
││                                        ││
││ 🏷️ Override Status                     ││
││ [Dropdown] ▼ -- Status otomatis --     ││
││                                        ││
││ 🐦 Hasil Penetasan                     ││
││ Tanggal Menetas: [Date Picker]        ││
││ Jumlah Menetas:  [Number Input]       ││
││ Jumlah DOC:      [Number Input]       ││
││ Persentase:      [Auto Calculate]     ││
│ └───────────────────────────────────────┘│
│                                           │
│ [Update Data] [Batal]                     │
└───────────────────────────────────────────┘
```

## 🔧 Technical Implementation

### HTML Structure
```blade
@if(auth()->user()->peran === 'owner')
<!-- Divider -->
<hr style="margin: 30px 0; border-top: 2px dashed #e2e8f0;">

<!-- Toggle Section (Always Visible) -->
<div class="form-section" style="background: #fef3c7; ...">
    <label class="custom-switch">
        <input type="checkbox" id="ownerOverrideToggle">
        <span class="slider"></span>
    </label>
    <span id="ownerOverrideLabel">Aktifkan untuk override...</span>
    <span class="badge">Status: Aktif</span>
</div>

<!-- Unified Override Section (Hidden by default) -->
<div id="ownerOverrideSection" style="display: none; ...">
    <h3>Kontrol Owner [Owner Only Badge]</h3>
    
    <!-- Part 1: Override Status -->
    <select name="status">
        <option value="">-- Status otomatis --</option>
        <option value="proses">Proses</option>
        <option value="aktif">Aktif</option>
        <option value="selesai">Selesai</option>
        <option value="gagal">Gagal</option>
    </select>
    
    <!-- Part 2: Hasil Penetasan -->
    <h4>Hasil Penetasan</h4>
    <input name="tanggal_menetas" type="date">
    <input name="jumlah_menetas" type="number">
    <input name="jumlah_doc" type="number">
    <div id="persentase_display">Auto Calculate</div>
</div>
@endif
```

### JavaScript Logic
```javascript
const ownerOverrideToggle = document.getElementById('ownerOverrideToggle');
const ownerOverrideSection = document.getElementById('ownerOverrideSection');
const ownerOverrideLabel = document.getElementById('ownerOverrideLabel');

ownerOverrideToggle.addEventListener('change', function() {
    if (this.checked) {
        // SHOW unified section
        ownerOverrideSection.style.display = 'block';
        ownerOverrideLabel.innerHTML = '✓ Override aktif - Hasil penetasan & status tersedia';
        ownerOverrideLabel.style.color = '#dc3545';
        
        // Auto scroll to section
        setTimeout(() => {
            ownerOverrideSection.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    } else {
        // HIDE section
        ownerOverrideSection.style.display = 'none';
        ownerOverrideLabel.textContent = 'Aktifkan untuk override hasil penetasan & status';
        ownerOverrideLabel.style.color = '#64748b';
        
        // RESET all inputs
        document.getElementById('status').value = '';
        document.getElementById('tanggal_menetas').value = '';
        document.getElementById('jumlah_menetas').value = '';
        document.getElementById('jumlah_doc').value = '';
    }
});
```

## 🎨 Visual Design

### Color Scheme
```
Toggle Section (Warning - Attention needed):
  Background: #fef3c7 (Yellow-50)
  Border: #fbbf24 (Yellow-400)
  
Override Section (Danger - Critical control):
  Background: #fee2e2 (Red-50)
  Border-left: 4px solid #dc3545 (Red-600)
  Border-radius: 10px
```

### Typography
```
Toggle Label:
  Default: color: #64748b, weight: 400
  Active:  color: #dc3545, weight: 600

Section Title:
  Color: #dc3545 (Red)
  Icon: fa-user-shield
  Badge: bg-danger "Owner Only"
```

## 🔄 User Flow

### Scenario 1: Normal Edit (No Override)
```
1. Owner buka form edit
2. Lihat data penetasan
3. Edit data operasional (suhu, kelembaban, catatan)
4. Toggle override TIDAK diaktifkan
5. Submit → Status tetap otomatis, hasil penetasan kosong
```

### Scenario 2: Override Diperlukan
```
1. Owner buka form edit
2. Scroll ke bawah ke section "Owner Override Control"
3. Klik toggle → Section unified muncul
4. Lihat warning "Perhatian: Fitur ini memungkinkan..."
5. Pilih status baru dari dropdown (misal: Selesai)
6. Isi hasil penetasan:
   - Tanggal menetas: 2025-10-15
   - Jumlah menetas: 850
   - Jumlah DOC: 840
   - Persentase: Auto calculate → 85% 🟢
7. Submit → Status dan hasil tersimpan
```

### Scenario 3: Cancel Override
```
1. Owner aktifkan toggle
2. Mulai isi beberapa field
3. Berubah pikiran → matikan toggle
4. Semua input direset otomatis
5. Submit → Tidak ada perubahan status/hasil
```

## 📊 Data Flow

### Form Submission
```php
// Toggle OFF (default):
$request->status = null          → Use automatic status
$request->tanggal_menetas = null → No result yet
$request->jumlah_menetas = null
$request->jumlah_doc = null

// Toggle ON with input:
$request->status = 'selesai'            → Manual override
$request->tanggal_menetas = '2025-10-15' → Result data
$request->jumlah_menetas = 850
$request->jumlah_doc = 840
```

### Controller Logic
```php
// Update method in PenetasanController
if ($request->status) {
    $penetasan->status = $request->status;
}

if ($request->tanggal_menetas) {
    $penetasan->tanggal_menetas = $request->tanggal_menetas;
    $penetasan->jumlah_menetas = $request->jumlah_menetas;
    $penetasan->jumlah_doc = $request->jumlah_doc;
    // Auto calculate percentage...
}
```

## 🔐 Security

### Role Check
```blade
@if(auth()->user()->peran === 'owner')
    <!-- Section only appears for owner -->
@endif
```

### Visual Indicators
- 🛡️ Shield icon = Protected feature
- 🔴 Red badge "Owner Only" = Access restricted
- ⚠️ Warning alert = Critical action

## ✅ Benefits

### 1. Unified Interface
- Semua owner control di satu tempat
- Tidak perlu mencari-cari di berbagai section
- Logical grouping: "Override" mencakup semua manual control

### 2. Better UX
- Toggle memberikan control eksplisit
- Section hidden by default = tidak mengganggu
- Auto-scroll memastikan user melihat form yang muncul

### 3. Data Integrity
- Auto-reset saat toggle OFF mencegah submit tidak sengaja
- Warning alert mengingatkan ini fitur sensitif
- Badge "Owner Only" memperjelas siapa yang bisa akses

### 4. Cleaner Code
- Tidak ada duplikasi section hasil penetasan
- Satu JavaScript function handle semua override logic
- Mudah maintain dan extend

## 🐛 Error Prevention

### Reset Mechanism
```javascript
// Saat toggle OFF, semua input direset
if (!this.checked) {
    // Prevent accidental submission
    statusSelect.value = '';
    tanggalMenetas.value = '';
    jumlahMenetas.value = '';
    jumlahDoc.value = '';
}
```

### Visual Feedback
- Label berubah warna: abu (OFF) → merah (ON)
- Section muncul dengan warning alert
- Smooth scroll memastikan visibility

## 📝 Testing Checklist

- [ ] Toggle OFF: Section tersembunyi
- [ ] Toggle ON: Section muncul dengan smooth scroll
- [ ] Label text berubah sesuai state
- [ ] Label color berubah (abu → merah)
- [ ] Dropdown status memiliki 5 opsi
- [ ] Input hasil penetasan (4 fields) tersedia
- [ ] Persentase auto-calculate
- [ ] Toggle OFF: Semua input direset
- [ ] Submit tanpa toggle: Status otomatis
- [ ] Submit dengan toggle: Status & hasil tersimpan
- [ ] Operator tidak lihat section ini

## 🎉 Final Result

**One unified section** untuk semua owner override needs:
- ✅ Override status penetasan
- ✅ Input hasil penetasan prematur
- ✅ Clean, organized interface
- ✅ Explicit user control dengan toggle
- ✅ Auto-reset untuk data integrity
- ✅ Visual warnings untuk critical actions

---

**Location**: Form Edit Penetasan (`edit-penetasan.blade.php`)  
**Position**: Bagian paling bawah, sebelum tombol Submit  
**Access**: Owner only  
**Trigger**: Manual toggle activation
