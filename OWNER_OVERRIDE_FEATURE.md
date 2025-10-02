# Fitur Owner Override Status - Dokumentasi

## 📋 Deskripsi
Fitur Owner Override memungkinkan pengguna dengan role **Owner** untuk mengubah status penetasan secara manual. Section kontrol hanya muncul saat owner mengaktifkan toggle.

## 🎯 Fungsi Utama

### 1. Toggle Owner Control
- **Lokasi**: Form Edit Penetasan
- **Akses**: Hanya Owner
- **Fungsi**: Mengaktifkan/menonaktifkan section override status

### 2. Section Status Override
- **Visibility**: Hidden by default
- **Muncul**: Saat toggle diaktifkan
- **Konten**: Dropdown untuk memilih status manual

## 🔧 Cara Kerja

### A. State Awal (Toggle OFF)
```
✅ Menampilkan status saat ini dengan badge warna
✅ Toggle dalam posisi OFF (abu-abu)
✅ Section "Kontrol Owner" tersembunyi
✅ Form hanya menampilkan field standar
```

### B. Saat Toggle Diaktifkan (ON)
```
1. Toggle berubah menjadi hijau
2. Label berubah: "Override aktif - Section kontrol muncul"
3. Section "Kontrol Owner" muncul dengan animasi
4. Scroll otomatis ke section yang baru muncul
5. Dropdown status tersedia untuk dipilih
```

### C. Saat Toggle Dimatikan (OFF)
```
1. Toggle kembali abu-abu
2. Label kembali: "Aktifkan untuk override status"
3. Section "Kontrol Owner" tersembunyi kembali
4. Dropdown status direset ke default
```

## 🎨 Tampilan UI

### Toggle Control Section
```html
┌─────────────────────────────────────────────┐
│ 🛡️ Owner Control                            │
│                                             │
│ [Toggle Switch] Aktifkan untuk override    │
│ ⚠️ Status saat ini: [Badge Aktif]          │
└─────────────────────────────────────────────┘
```

### Status Override Section (Muncul saat toggle ON)
```html
┌─────────────────────────────────────────────┐
│ 🛡️ Kontrol Owner [Owner Only Badge]        │
│                                             │
│ ⚠️ Perhatian: Fitur ini memungkinkan       │
│    Owner untuk mengubah status secara       │
│    manual.                                  │
│                                             │
│ Override Status                             │
│ [Dropdown: -- Gunakan status otomatis --]  │
│           - Proses                          │
│           - Aktif                           │
│           - Selesai                         │
│           - Gagal                           │
└─────────────────────────────────────────────┘
```

## 💻 Implementasi Teknis

### HTML Structure
```blade
<!-- Toggle Control -->
<div class="form-section">
    <label class="custom-switch">
        <input type="checkbox" id="statusOverrideToggle">
        <span class="slider"></span>
    </label>
    <span id="statusOverrideLabel">Aktifkan untuk override status</span>
</div>

<!-- Hidden Section -->
<div id="statusOverrideSection" style="display: none;">
    <!-- Konten override form -->
</div>
```

### JavaScript Logic
```javascript
statusOverrideToggle.addEventListener('change', function() {
    if (this.checked) {
        // Show section
        statusOverrideSection.style.display = 'block';
        // Update label
        statusOverrideLabel.textContent = 'Override aktif - Section kontrol muncul';
        // Scroll to section
        statusOverrideSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        // Hide section
        statusOverrideSection.style.display = 'none';
        // Reset dropdown
        statusSelect.value = '';
    }
});
```

## 🎯 Status Badge Colors
```
⚫ Proses  → Badge abu-abu (secondary)
🔵 Aktif   → Badge biru (info)
🟢 Selesai → Badge hijau (success)
🔴 Gagal   → Badge merah (danger)
```

## 🔐 Security & Access Control

### Role Checking
```blade
@if(auth()->user()->peran === 'owner')
    <!-- Owner-only features -->
@endif
```

### Form Submission
- Jika toggle OFF: Field status tidak terkirim (gunakan status otomatis)
- Jika toggle ON: Field status terkirim sesuai pilihan dropdown

## 📱 User Experience Flow

### Skenario 1: Owner Tidak Perlu Override
```
1. Owner membuka form edit
2. Melihat status saat ini di bagian atas
3. Tidak mengaktifkan toggle
4. Melakukan edit data lain
5. Submit → Status tetap mengikuti sistem otomatis
```

### Skenario 2: Owner Perlu Override
```
1. Owner membuka form edit
2. Melihat status saat ini tidak sesuai
3. Mengaktifkan toggle override
4. Section kontrol muncul dengan smooth scroll
5. Memilih status baru dari dropdown
6. Submit → Status berubah sesuai pilihan manual
```

## 🐛 Error Prevention

### Reset on Toggle Off
- Dropdown otomatis reset ke default
- Mencegah submit status yang tidak disengaja

### Visual Feedback
- Toggle switch dengan animasi smooth
- Label berubah warna (abu-abu ↔ merah)
- Section muncul dengan border merah sebagai warning

### Scroll Behavior
- Auto-scroll ke section saat muncul
- Memastikan user melihat form yang baru tampil

## 📊 Fitur Terkait

### 1. Batch Preview (Create Form)
- Auto-generate batch code PTN-YYYYMMDD-XXX
- Preview dengan refresh button

### 2. Role-Based Access
- Operator: Hanya menu operasional
- Owner: Full access + override capability

### 3. Status System
- Proses → Aktif (automatic after 1 day)
- Aktif → Selesai (when hatching complete)
- Manual override available for Owner

## 🔄 Update History
- **2025-10-02**: Initial implementation with toggle-based visibility
- **Feature**: Hidden by default, shows only when needed
- **UX**: Cleaner interface, less overwhelming for users

---

**Catatan**: Fitur ini dirancang untuk memberikan fleksibilitas kepada Owner tanpa membuat interface terlalu rumit. Toggle memastikan section kontrol hanya muncul saat dibutuhkan.
