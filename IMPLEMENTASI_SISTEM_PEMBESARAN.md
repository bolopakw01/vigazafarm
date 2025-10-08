# Implementasi Sistem Pembesaran dengan DSS (Decision Support System)

## Status Saat Ini ✅

### Yang Sudah Ada:
1. ✅ **Database Models** - Semua model sudah ada dan berfungsi:
   - `Pembesaran.php`
   - `Pakan.php`
   - `Kematian.php`
   - `MonitoringLingkungan.php`
   - `Kesehatan.php`
   - `LaporanHarian.php`

2. ✅ **Routes** - Sudah terdefinisi di `routes/web.php`:
   - POST `/admin/pembesaran/{id}/pakan` - Simpan pakan
   - POST `/admin/pembesaran/{id}/kematian` - Simpan kematian
   - POST `/admin/pembesaran/{id}/monitoring` - Simpan monitoring lingkungan
   - POST `/admin/pembesaran/{id}/kesehatan` - Simpan kesehatan/vaksinasi
   - POST `/admin/pembesaran/{id}/berat` - Update berat rata-rata
   - POST `/admin/pembesaran/{id}/laporan-harian` - Generate laporan harian
   - GET `/admin/pembesaran/{id}/pakan/list` - Ambil list pakan
   - GET `/admin/pembesaran/{id}/kematian/list` - Ambil list kematian
   - dll.

3. ✅ **Controller** - `PembesaranRecordingController.php` sudah lengkap dengan:
   - Validasi input
   - DSS logic (Decision Support System)
   - Response JSON untuk AJAX

4. ✅ **View (Blade)** - `show-pembesaran.blade.php` dan `_tab-show-pembesaran.blade.php`:
   - Form sudah lengkap dengan field yang benar
   - Chart containers sudah ada
   - Tab navigation sudah berfungsi

5. ✅ **CSS** - Styling sudah sesuai dan responsive

## Yang Perlu Dilengkapi 🔧

### 1. JavaScript untuk AJAX Submission

File `public/bolopa/js/admin-show-part-pembesaran.js` perlu diupdate untuk:

#### A. Form Submission via AJAX

```javascript
// Contoh untuk Form Pakan
document.querySelector('form[aria-label="Form pencatatan pakan harian"]')
    .addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const pembesaranId = window.location.pathname.match(/\/pembesaran\/(\d+)/)[1];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        try {
            const response = await fetch(`/admin/pembesaran/${pembesaranId}/pakan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tanggal: formData.get('tanggal'),
                    stok_pakan_id: formData.get('jenis_pakan'),
                    jumlah_kg: formData.get('jumlah_kg'),
                    jumlah_karung: formData.get('jumlah_karung')
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✅ ' + result.message);
                this.reset();
                // Reload chart data
                loadPakanChart();
            } else {
                alert('❌ ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan');
        }
    });
```

#### B. Load Data untuk Chart

```javascript
async function loadPakanChart() {
    const pembesaranId = window.location.pathname.match(/\/pembesaran\/(\d+)/)[1];
    const response = await fetch(`/admin/pembesaran/${pembesaranId}/pakan/list`);
    const result = await response.json();
    
    if (result.success && result.data) {
        const categories = result.data.map(d => 
            new Date(d.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' })
        );
        const series = [{
            name: 'Pakan (kg)',
            data: result.data.map(d => parseFloat(d.jumlah_kg))
        }];
        
        // Render/update chart
        if (feedChart) {
            feedChart.updateSeries(series);
            feedChart.updateOptions({ xaxis: { categories } });
        } else {
            feedChart = new ApexCharts(document.querySelector('#chartFeed'), {
                chart: { type: 'area', height: 240, toolbar: { show: false } },
                series: series,
                xaxis: { categories: categories },
                colors: ['#0077b6'],
                stroke: { curve: 'smooth', width: 2 }
            });
            feedChart.render();
        }
    }
}
```

### 2. Populate Dropdown Stok Pakan

Di `PembesaranController@show`, data `$stokPakanList` sudah dikirim ke view. 
Update form di blade:

```blade
<select class="form-select" name="jenis_pakan" required>
    <option value="">-- Pilih Jenis --</option>
    @foreach($stokPakanList as $stok)
        <option value="{{ $stok->id }}">
            {{ $stok->nama_pakan }} (Stok: {{ $stok->stok_kg }} kg)
        </option>
    @endforeach
</select>
```

### 3. DSS (Decision Support System) Features

Controller sudah memiliki DSS logic, tinggal ditampilkan di frontend:

#### A. Alert Mortalitas Tinggi
```javascript
if (result.is_high_mortality && result.alert) {
    showWarningToast(result.alert);
    // Bisa ditambahkan rekomendasi aksi
}
```

#### B. Alert Lingkungan Tidak Ideal
Tambahkan di `PembesaranRecordingController@storeMonitoring`:
```php
// Check suhu ideal (27-30°C untuk grower)
$dssAlert = null;
if ($validated['suhu'] < 27) {
    $dssAlert = 'Suhu terlalu rendah! Tingkatkan pemanas.';
} elseif ($validated['suhu'] > 30) {
    $dssAlert = 'Suhu terlalu tinggi! Tingkatkan ventilasi.';
}

if ($validated['kelembaban'] < 60 || $validated['kelembaban'] > 70) {
    $dssAlert .= ' Kelembaban diluar range ideal (60-70%).';
}

return response()->json([
    'success' => true,
    'message' => 'Data monitoring berhasil disimpan',
    'data' => $monitoring,
    'dss_alert' => $dssAlert
]);
```

#### C. Alert Berat Dibawah Standar
Di `PembesaranRecordingController@storeBeratRataRata`:
```php
$umurHari = Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(Carbon::now());
$beratStandar = ParameterStandar::getBeratStandar('grower', $umurHari);

$dssAlert = null;
if ($beratStandar && $validated['berat_rata'] < ($beratStandar * 0.9)) {
    $dssAlert = "Berat dibawah standar! Berat saat ini: {$validated['berat_rata']}g, Standar: {$beratStandar}g. Cek kualitas pakan dan kesehatan.";
}

return response()->json([
    'success' => true,
    'message' => 'Data berat berhasil disimpan',
    'berat_rata' => $validated['berat_rata'],
    'berat_standar' => $beratStandar,
    'dss_alert' => $dssAlert
]);
```

### 4. Real-time Metrics Update

Setelah submit form, update metrics di halaman tanpa reload:

```javascript
function updateMetrics(data) {
    // Update Populasi Saat Ini
    if (data.populasi_saat_ini) {
        document.querySelector('.bolopa-kai-teal .bolopa-kai-value').textContent = 
            data.populasi_saat_ini.toLocaleString('id-ID');
    }
    
    // Update Mortalitas
    if (data.mortalitas !== undefined) {
        document.querySelector('.bolopa-kai-red .bolopa-kai-value').innerHTML = 
            `${data.mortalitas.toFixed(2)}<small style="font-size:0.45em;">%</small>`;
        document.querySelector('.bolopa-kai-red .bolopa-kai-label').textContent = 
            `Mortalitas (${data.total_mati} ekor)`;
    }
    
    // Update Berat
    if (data.berat_rata) {
        document.querySelector('.bolopa-kai-green .bolopa-kai-value').textContent = 
            `${Math.round(data.berat_rata)}g`;
    }
}
```

### 5. History Display

Tampilkan history di note-panel:

```javascript
function renderPakanHistory(data) {
    const container = document.querySelector('.note-panel.alt.lopa-note-panel.lopa-alt');
    if (!container || !data.length) return;
    
    const html = `
        <h6>History Pakan (${data.length} terakhir)</h6>
        <table class="table table-sm">
            <thead>
                <tr><th>Tanggal</th><th>Jenis</th><th>Jumlah</th><th>Biaya</th></tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td>${new Date(d.tanggal).toLocaleDateString('id-ID')}</td>
                        <td>${d.stok_pakan?.nama_pakan || '-'}</td>
                        <td>${d.jumlah_kg} kg</td>
                        <td>Rp ${parseInt(d.total_biaya).toLocaleString('id-ID')}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    container.innerHTML = html;
}
```

## Testing Checklist ✔️

### Test Form Submission:
- [ ] Form Pakan - Submit dan cek database
- [ ] Form Kematian - Submit dan cek alert DSS jika mortalitas > 5%
- [ ] Form Monitoring - Submit dan cek alert DSS jika suhu/kelembaban diluar range
- [ ] Form Kesehatan - Submit dan cek list history
- [ ] Form Berat - Submit dan cek alert DSS jika berat dibawah standar
- [ ] Generate Laporan Harian - Submit dan cek record di database

### Test Chart Rendering:
- [ ] Chart Pakan menampilkan data real dari database
- [ ] Chart Mortalitas menampilkan tren kumulatif
- [ ] Chart Monitoring menampilkan suhu dan kelembaban
- [ ] Chart Berat menampilkan pertumbuhan per minggu

### Test DSS Alerts:
- [ ] Alert muncul saat mortalitas > 5%
- [ ] Alert muncul saat suhu < 27°C atau > 30°C
- [ ] Alert muncul saat kelembaban < 60% atau > 70%
- [ ] Alert muncul saat berat < 90% dari standar

## File Structure Summary

```
vigazafarm/
├── app/
│   ├── Models/
│   │   ├── Pembesaran.php ✅
│   │   ├── Pakan.php ✅
│   │   ├── Kematian.php ✅
│   │   ├── MonitoringLingkungan.php ✅
│   │   ├── Kesehatan.php ✅
│   │   └── LaporanHarian.php ✅
│   └── Http/Controllers/
│       ├── PembesaranController.php ✅
│       └── PembesaranRecordingController.php ✅
├── routes/
│   └── web.php ✅
├── resources/views/admin/pages/pembesaran/
│   ├── show-pembesaran.blade.php ✅
│   └── partials/_tab-show-pembesaran.blade.php ✅
└── public/bolopa/
    ├── css/admin-show-part-pembesaran.css ✅
    └── js/admin-show-part-pembesaran.js ⚠️ (Perlu update AJAX)
```

## Next Steps untuk Developer

1. **Update JavaScript File** - Implementasikan AJAX submission sesuai contoh diatas
2. **Test Each Form** - Submit setiap form dan pastikan data masuk database
3. **Verify DSS Alerts** - Pastikan alert muncul sesuai kondisi
4. **Load Real Data to Charts** - Fetch data dari API dan render ke chart
5. **Add Loading States** - Tambahkan spinner/loading saat submit form
6. **Error Handling** - Tangani error dengan baik (validasi, network error, dll)

## Dukungan DSS yang Sudah Terintegrasi

1. **Analisis Mortalitas** - Alert otomatis jika > 5%
2. **Monitoring Lingkungan** - Alert jika suhu/kelembaban diluar range ideal
3. **Pertumbuhan Berat** - Bandingkan dengan standar, alert jika dibawah 90%
4. **Konsumsi Pakan** - Track dan hitung efisiensi FCR (Feed Conversion Ratio)
5. **Reminder Vaksinasi** - Otomatis generate reminder berdasarkan umur

Semua backend sudah siap, tinggal koneksikan frontend dengan AJAX! 🚀
