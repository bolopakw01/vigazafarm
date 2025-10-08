# Update: Detail Laporan Design (Simple & Clean)

**Tanggal:** 7 Oktober 2025  
**Design:** Sesuai note.html (Simple & Professional)

---

## 🎨 DESIGN CHANGES

### Sebelum (Fancy):
- ❌ Gradient header (ungu)
- ❌ 4 cards with colored borders
- ❌ Icons di setiap card
- ❌ Complex styling

### Sesudah (Simple):
- ✅ Plain header (abu-abu #f8f9fa)
- ✅ Simple text layout
- ✅ Minimal icons
- ✅ Clean & professional

---

## 📋 STRUKTUR LAYOUT

```
┌─────────────────────────────────────────────┐
│ 📋 Detail Laporan Harian              [X]  │ ← Plain grey header
├─────────────────────────────────────────────┤
│ 📅 Tanggal Laporan       👤 Dibuat Oleh    │
│ Selasa, 7 Oktober 2025        lopa123      │
├─────────────────────────────────────────────┤
│  Populasi  │  Pakan (kg)  │  Kematian  │ Mortalitas  │
│   125.00   │      4       │   80.00%   │      1      │
├─────────────────────────────────────────────┤
│ Catatan Lengkap                             │
│ ┌───────────────────────────────────────┐  │
│ │ LAPORAN HARIAN - Selasa, 7 Okt 2025  │  │
│ │                                       │  │
│ │ 🌾 PEMBERIAN PAKAN:                  │  │
│ │ - Belum ada data...                   │  │
│ │                                       │  │
│ │ 💀 MORTALITAS:                       │  │
│ │ - Tidak ada kematian hari ini ✅     │  │
│ └───────────────────────────────────────┘  │
│                                             │
│ Dibuat 07 Okt 2025 pukul 11.43             │
│           Diperbarui 07 Okt 2025 pukul 11.43│
├─────────────────────────────────────────────┤
│              [Tutup] [Edit] [Hapus]        │
└─────────────────────────────────────────────┘
```

---

## 🔄 PERUBAHAN FIELD MAPPING

### Header:
- Tanggal: Format "Selasa, 7 Oktober 2025" (lengkap)
- Pengguna: `nama_pengguna` atau `name`

### Statistik (PERHATIAN - Ada swap):
| Label | Old Value | **New Value** |
|-------|-----------|---------------|
| Populasi | `jumlah_burung` (integer) | `jumlah_burung` **.toFixed(2)** |
| Pakan (kg) | `konsumsi_pakan_kg` (.toFixed(2)) | `konsumsi_pakan_kg` **(integer)** |
| Kematian | `jumlah_kematian` (integer) | `mortalitas_kumulatif` **.toFixed(2) + '%'** |
| Mortalitas | `mortalitas_kumulatif` (.toFixed(2) + '%') | `jumlah_kematian` **(integer)** |

**⚠️ CATATAN:** Ada swap antara Kematian & Mortalitas sesuai HTML sample

---

## ✨ FITUR BARU

### 1. Timestamp Detail
- **Dibuat:** `dibuat_pada` → "Dibuat 07 Oktober 2025 pukul 11.43"
- **Diperbarui:** `diperbarui_pada` → "Diperbarui 07 Oktober 2025 pukul 11.43"

### 2. Tombol Edit
- Icon: `fa-pen`
- Action: Placeholder (alert) - ready untuk implement
- TODO: Redirect ke edit page atau show edit modal

### 3. Tombol Hapus
- Icon: `fa-trash`
- Action: Placeholder (alert) - ready untuk implement
- Confirmation: `confirm()` dialog
- TODO: DELETE request ke API

---

## 🎯 CSS STYLING

### Colors:
- Header: `#f8f9fa` (light grey)
- Text label: `#6c757d` (muted grey)
- Border: `#dee2e6` & `#e1e1e1`
- Catatan box: `#f9f9f9` background

### Typography:
- Header: font-weight 600
- Labels: font-size 0.8rem, color grey
- Values: font-weight 500

### Layout:
- Border radius: 0.75rem (modal), 0.5rem (catatan box)
- Simple borders: 1px solid
- No shadows, no gradients

---

## 📝 TODO: Implement Edit & Delete

### Edit Function:
```javascript
// Option 1: Redirect to edit page
window.location.href = `/admin/pembesaran/${pembesaranId}/laporan-harian/${laporanId}/edit`;

// Option 2: Show edit modal
showEditLaporanModal(laporanId);
```

### Delete Function:
```javascript
fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/${laporanId}`, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    credentials: 'same-origin'
})
.then(response => response.json())
.then(result => {
    if (result.success) {
        alert('Laporan berhasil dihapus');
        loadLaporanData(); // Reload list
    }
});
```

### API Routes Needed:
```php
// routes/web.php
Route::put('/laporan-harian/{laporan}', [PembesaranRecordingController::class, 'updateLaporanHarian']);
Route::delete('/laporan-harian/{laporan}', [PembesaranRecordingController::class, 'destroyLaporanHarian']);
```

---

## ✅ TESTING

- [x] Modal muncul dengan design simple
- [x] Header plain grey (bukan gradient)
- [x] Statistik 4 kolom dengan layout simple
- [x] Catatan box dengan border & background
- [x] Timestamp created & updated
- [x] Tombol Edit (placeholder)
- [x] Tombol Hapus (placeholder dengan confirmation)
- [ ] Implement actual Edit functionality
- [ ] Implement actual Delete functionality

---

## 📁 FILES

- **Modified:** `_note-show-pembesaran.blade.php`
- **Reference:** `note.html` (design template)

---

**Status:** ✅ Design Updated, TODO: Implement Edit/Delete API
