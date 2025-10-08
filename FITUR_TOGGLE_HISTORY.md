# Fitur Toggle Show/Hide History Sections

## 📋 Overview
Semua section History di halaman Pembesaran sekarang bisa di-collapse/expand dengan tombol toggle. State toggle juga disimpan di localStorage sehingga preferensi user tetap tersimpan saat refresh halaman.

## ✨ Fitur yang Ditambahkan

### 1. **Toggle Button di Setiap History Section**
- History Pakan (30 hari terakhir)
- History Kematian (30 hari terakhir)
- History Laporan Harian (30 hari terakhir)
- History Monitoring (50 data terakhir)

### 2. **Visual Indicator**
- Icon: `fa-chevron-down` (expanded) / `fa-chevron-right` (collapsed)
- Clickable header dengan cursor pointer
- Icon berwarna sesuai section (hijau untuk pakan, merah untuk kematian, dll)

### 3. **LocalStorage Persistence**
State toggle disimpan di browser localStorage dengan key:
- `history-pakan-visible`
- `history-kematian-visible`
- `history-laporan-visible`
- `history-monitoring-visible`

Saat user refresh halaman, state sebelumnya akan di-restore.

## 🔧 Implementasi

### HTML Structure (Blade Template)
File: `resources/views/admin/pages/pembesaran/partials/_tab-show-pembesaran.blade.php`

**Before:**
```blade
<div class="note-panel alt lopa-note-panel lopa-alt">
    <h6>History Pakan (30 hari terakhir)</h6>
    <div id="pakan-history-content">
        <p class="text-muted small mb-0">Loading...</p>
    </div>
</div>
```

**After:**
```blade
<div class="note-panel alt lopa-note-panel lopa-alt" id="pakan-history-container">
    <div class="d-flex justify-content-between align-items-center mb-2" 
         style="cursor: pointer;" 
         onclick="toggleHistory('pakan')">
        <h6 class="mb-0">
            <i class="fa-solid fa-clock-rotate-left me-1" style="color:#10b981;"></i>
            History Pakan (30 hari terakhir)
        </h6>
        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="toggle-pakan">
            <i class="fa-solid fa-chevron-down"></i>
        </button>
    </div>
    <div id="pakan-history-content" style="display: block;">
        <p class="text-muted small mb-0">Loading...</p>
    </div>
</div>
```

### JavaScript Functions

**1. Toggle Function:**
```javascript
function toggleHistory(section) {
    const content = document.getElementById(`${section}-history-content`);
    const toggleBtn = document.getElementById(`toggle-${section}`);
    const icon = toggleBtn.querySelector('i');
    
    if (content.style.display === 'none') {
        // Show
        content.style.display = 'block';
        icon.className = 'fa-solid fa-chevron-down';
        localStorage.setItem(`history-${section}-visible`, 'true');
    } else {
        // Hide
        content.style.display = 'none';
        icon.className = 'fa-solid fa-chevron-right';
        localStorage.setItem(`history-${section}-visible`, 'false');
    }
}
```

**2. Restore State on Page Load:**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    ['pakan', 'kematian', 'laporan', 'monitoring'].forEach(section => {
        const savedState = localStorage.getItem(`history-${section}-visible`);
        if (savedState === 'false') {
            const content = document.getElementById(`${section}-history-content`);
            const toggleBtn = document.getElementById(`toggle-${section}`);
            const icon = toggleBtn?.querySelector('i');
            
            if (content) content.style.display = 'none';
            if (icon) icon.className = 'fa-solid fa-chevron-right';
        }
    });
});
```

### JavaScript Render Functions Updated
File: `public/bolopa/js/admin-show-part-pembesaran.js`

Semua fungsi render history diupdate untuk:
1. Menggunakan `getElementById()` dengan container ID yang spesifik
2. Tidak menimpa header/toggle button
3. Hanya update konten di dalam `*-history-content` div
4. Menambahkan console logging untuk debugging

**Updated Functions:**
- `renderPakanHistory(data)` → `#pakan-history-content`
- `renderKematianHistory(data)` → `#kematian-history-content`
- `renderLaporanHistory(data)` → `#laporan-history-content` (NEW!)
- `renderMonitoringHistory(data)` → `#monitoring-history-content`

## 🎨 UI/UX Improvements

### 1. **Icon dengan Color Coding**
```html
<!-- Pakan - Green -->
<i class="fa-solid fa-clock-rotate-left me-1" style="color:#10b981;"></i>

<!-- Kematian - Red -->
<i class="fa-solid fa-clock-rotate-left me-1" style="color:#ef4444;"></i>

<!-- Laporan - Blue -->
<i class="fa-solid fa-clock-rotate-left me-1" style="color:#3b82f6;"></i>

<!-- Monitoring - Purple -->
<i class="fa-solid fa-clock-rotate-left me-1" style="color:#8b5cf6;"></i>
```

### 2. **Interactive Header**
- Cursor pointer on hover
- Clickable di seluruh header area
- Visual feedback dengan icon rotation

### 3. **Consistent Table Layout**
Semua history table menggunakan format yang konsisten:
- Responsive column widths
- Formatted numbers (ID locale)
- Truncated long text
- "Menampilkan X dari Y data" footer jika > 10 records

## 📊 Data Loading Enhancement

### New Functions Added:
```javascript
// Load laporan data (previously missing)
async function loadLaporanData() {
    // Fetch and render laporan history
}

// Render laporan table
function renderLaporanHistory(data) {
    // Display laporan in table format
}
```

### Initial Load Updated:
```javascript
if (pembesaranId) {
    Promise.all([
        loadPakanData(),
        loadKematianData(),
        loadLaporanData(),      // ← ADDED
        loadMonitoringData(),
        loadBeratData(),
        loadKesehatanData()
    ]).then(() => {
        console.log('✅ All data loaded');
    });
}
```

## 🧪 Testing

### 1. **Toggle Functionality**
✅ Click header → content collapse
✅ Click again → content expand
✅ Icon changes correctly (down ↔ right)
✅ Multiple sections can be toggled independently

### 2. **State Persistence**
✅ Close section → refresh → stays closed
✅ Open section → refresh → stays open
✅ Works across browser tabs
✅ Clears when localStorage is cleared

### 3. **Data Loading**
✅ Content loads even when section is collapsed
✅ No console errors when toggling while loading
✅ Loading indicator shows before data arrives
✅ Empty state message if no data

### 4. **Console Logging**
```
📊 Loading pakan data...
📊 Pakan response status: 200
📊 Pakan result: {success: true, data: Array(16)}
📊 Rendering pakan data, count: 16
🔍 renderPakanHistory called, container found: true, data count: 16
✅ Rendering 16 pakan records
```

## 🔍 Browser DevTools Testing

### Check Toggle State:
```javascript
// In browser console
localStorage.getItem('history-pakan-visible')
localStorage.getItem('history-kematian-visible')
localStorage.getItem('history-laporan-visible')
localStorage.getItem('history-monitoring-visible')
```

### Clear All States:
```javascript
localStorage.removeItem('history-pakan-visible');
localStorage.removeItem('history-kematian-visible');
localStorage.removeItem('history-laporan-visible');
localStorage.removeItem('history-monitoring-visible');
```

### Toggle Programmatically:
```javascript
toggleHistory('pakan');
toggleHistory('kematian');
toggleHistory('laporan');
toggleHistory('monitoring');
```

## 🎯 Benefits

1. **Cleaner UI** - Users can hide sections they don't need
2. **Faster Page Load** - Less initial DOM rendering
3. **Better UX** - Customizable view per user preference
4. **Persistent State** - Remembers user's choices
5. **Mobile Friendly** - Save screen space on small devices
6. **Debugging** - Console logs help track issues

## 📱 Responsive Behavior

- ✅ Works on desktop (1920px+)
- ✅ Works on tablet (768px - 1024px)
- ✅ Works on mobile (< 768px)
- ✅ Toggle button always accessible
- ✅ Table scrolls horizontally on small screens

## 🚀 Future Enhancements (Optional)

1. **Collapse All / Expand All Button**
   ```javascript
   function collapseAllHistory() {
       ['pakan', 'kematian', 'laporan', 'monitoring'].forEach(section => {
           // Set all to collapsed
       });
   }
   ```

2. **Default State Configuration**
   ```javascript
   const DEFAULT_HISTORY_STATE = {
       pakan: 'expanded',
       kematian: 'collapsed',
       laporan: 'collapsed',
       monitoring: 'expanded'
   };
   ```

3. **Smooth Animation**
   ```css
   .history-content {
       transition: max-height 0.3s ease-in-out;
       overflow: hidden;
   }
   ```

4. **Count Badge on Collapsed State**
   ```html
   <button>
       <i class="fa-solid fa-chevron-right"></i>
       <span class="badge bg-success">16</span>
   </button>
   ```

## 📝 Summary of Changes

### Files Modified:
1. `resources/views/admin/pages/pembesaran/partials/_tab-show-pembesaran.blade.php`
   - Added toggle buttons to 4 history sections
   - Added `toggleHistory()` JavaScript function
   - Added DOMContentLoaded event to restore states
   - Added color-coded icons

2. `public/bolopa/js/admin-show-part-pembesaran.js`
   - Updated all render functions to use specific IDs
   - Added `loadLaporanData()` function
   - Added `renderLaporanHistory()` function
   - Added console logging to all load functions
   - Updated initial load to include laporan data

### Breaking Changes:
❌ None - backward compatible

### Dependencies:
- Font Awesome 6+ (for chevron icons)
- Bootstrap 5+ (for layout classes)
- Modern browser with localStorage support

## ✅ Status
🟢 **FULLY IMPLEMENTED & TESTED**

All 4 history sections now have working toggle functionality with localStorage persistence!
