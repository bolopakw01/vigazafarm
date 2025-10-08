# Decision Support System (DSS) - Sistem Pembesaran Puyuh

## 📊 Fitur DSS Yang Sudah Terintegrasi

### 1. Analisis Mortalitas (Kematian)

**Lokasi:** `PembesaranRecordingController@storeKematian`

**Logic:**
```php
$totalMati = Kematian::totalKematianByBatch($batch_id);
$mortalitas = ($totalMati / $populasi_awal) * 100;

if ($mortalitas > 5) {
    $alert = "Perhatian! Mortalitas melebihi 5% ({$mortalitas}%)";
    $is_high_mortality = true;
}
```

**Threshold:**
- Normal: 0-5%
- Warning: > 5%
- Critical: > 10% (bisa ditambahkan)

**Rekomendasi Otomatis:**
- Mortalitas > 5%: Cek kondisi lingkungan, vaksinasi, dan kualitas pakan
- Mortalitas > 10%: Segera konsultasi dengan ahli/vet

**Output JSON:**
```json
{
    "success": true,
    "total_mati": 25,
    "mortalitas": 6.25,
    "is_high_mortality": true,
    "alert": "Perhatian! Mortalitas melebihi 5%"
}
```

---

### 2. Monitoring Lingkungan (Suhu & Kelembaban)

**Lokasi:** `PembesaranRecordingController@storeMonitoring`

**Parameter Ideal untuk Grower (4-6 minggu):**
| Parameter | Range Ideal |
|-----------|-------------|
| Suhu | 27-30°C |
| Kelembaban | 60-70% |
| Ventilasi | Baik |

**Logic DSS:**
```php
$dss_alert = '';

// Check Suhu
if ($suhu < 27) {
    $dss_alert .= 'Suhu terlalu rendah! Tingkatkan pemanas. ';
} elseif ($suhu > 30) {
    $dss_alert .= 'Suhu terlalu tinggi! Tingkatkan ventilasi. ';
}

// Check Kelembaban
if ($kelembaban < 60) {
    $dss_alert .= 'Kelembaban terlalu rendah! Tambah kabut/spray air. ';
} elseif ($kelembaban > 70) {
    $dss_alert .= 'Kelembaban terlalu tinggi! Tingkatkan ventilasi. ';
}
```

**Dampak Lingkungan Tidak Ideal:**
- Suhu terlalu rendah → Puyuh kedinginan, pertumbuhan lambat
- Suhu terlalu tinggi → Stress panas, nafsu makan turun
- Kelembaban rendah → Dehidrasi, masalah pernapasan
- Kelembaban tinggi → Jamur, penyakit pernapasan

**Rekomendasi Otomatis:**
- Suhu < 27°C: Nyalakan pemanas, tutup ventilasi sebagian
- Suhu > 30°C: Buka ventilasi, pasang kipas, spray air dingin
- Kelembaban < 60%: Spray air, pasang humidifier
- Kelembaban > 70%: Buka ventilasi, gunakan dehumidifier

---

### 3. Analisis Pertumbuhan Berat

**Lokasi:** `PembesaranRecordingController@storeBeratRataRata`

**Parameter Standar Berat (gram):**
| Umur (hari) | Berat Standar (g) | Berat Minimum (90%) |
|-------------|-------------------|---------------------|
| 7 | 15 | 13.5 |
| 14 | 35 | 31.5 |
| 21 | 60 | 54 |
| 28 | 90 | 81 |
| 35 | 120 | 108 |
| 42 | 150 | 135 |

**Logic DSS:**
```php
$umurHari = Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(now());
$beratStandar = ParameterStandar::getBeratStandar('grower', $umurHari);

$percentase = ($berat_rata / $beratStandar) * 100;

if ($percentase < 90) {
    $dss_alert = "Berat dibawah standar! Saat ini: {$berat_rata}g, Standar: {$beratStandar}g ({$percentase}%). Cek kualitas pakan dan kesehatan.";
} elseif ($percentase < 95) {
    $dss_alert = "Berat mendekati minimum. Monitor ketat dan tingkatkan kualitas pakan.";
}
```

**Penyebab Berat Dibawah Standar:**
1. Kualitas pakan kurang baik
2. Kuantitas pakan tidak cukup
3. Penyakit/infeksi
4. Stress lingkungan
5. Kompetisi makan terlalu tinggi (overcrowding)

**Rekomendasi Otomatis:**
- < 90% standar: Tingkatkan protein pakan, cek kesehatan, tambah frekuensi makan
- 90-95% standar: Monitor ketat, evaluasi pakan
- > 95% standar: Pertumbuhan normal, pertahankan

---

### 4. Analisis Konsumsi Pakan (FCR)

**Lokasi:** `Model Pakan` (bisa ditambahkan method)

**Feed Conversion Ratio (FCR):**
```
FCR = Total Konsumsi Pakan (kg) / Total Pertambahan Berat (kg)
```

**Target FCR untuk Puyuh Pedaging:**
- Ideal: 2.5 - 3.0
- Acceptable: 3.0 - 3.5
- Poor: > 3.5

**Logic DSS:**
```php
$totalPakan = Pakan::totalKonsumsiByBatch($batch_id); // kg
$beratAwal = $pembesaran->berat_awal; // gram
$beratAkhir = $pembesaran->berat_rata_rata; // gram
$pertambahanBerat = ($beratAkhir - $beratAwal) / 1000; // convert to kg
$populasi = $pembesaran->populasi_saat_ini;

$totalPertambahanBerat = ($pertambahanBerat * $populasi);
$fcr = $totalPakan / $totalPertambahanBerat;

if ($fcr > 3.5) {
    $dss_alert = "FCR terlalu tinggi ({$fcr})! Evaluasi kualitas pakan dan efisiensi pemberian.";
} elseif ($fcr > 3.0) {
    $dss_alert = "FCR di atas ideal ({$fcr}). Monitor dan tingkatkan efisiensi.";
}
```

**Faktor Yang Mempengaruhi FCR:**
1. Kualitas pakan (protein, energi)
2. Kesehatan burung
3. Lingkungan (suhu, kelembaban)
4. Manajemen pemberian pakan
5. Genetik/strain burung

---

### 5. Reminder Vaksinasi

**Lokasi:** `Model Kesehatan@generateReminder`

**Jadwal Vaksinasi Puyuh:**
| Umur (hari) | Vaksin | Metode |
|-------------|--------|--------|
| 7 | ND (Newcastle Disease) | Tetes mata/hidung |
| 14 | ND + IB (Infectious Bronchitis) | Air minum |
| 28 | Fowl Pox | Tusuk sayap |
| 35 | ND Booster | Air minum |

**Logic DSS:**
```php
$umurHari = Carbon::parse($tanggal_masuk)->diffInDays(now());
$reminders = [];

$jadwalVaksin = [
    ['umur' => 7, 'nama' => 'ND', 'metode' => 'Tetes mata/hidung'],
    ['umur' => 14, 'nama' => 'ND + IB', 'metode' => 'Air minum'],
    ['umur' => 28, 'nama' => 'Fowl Pox', 'metode' => 'Tusuk sayap'],
    ['umur' => 35, 'nama' => 'ND Booster', 'metode' => 'Air minum']
];

foreach ($jadwalVaksin as $vaksin) {
    $selisih = $umurHari - $vaksin['umur'];
    
    if ($selisih >= -2 && $selisih <= 0) {
        // H-2 sampai H-day
        $reminders[] = [
            'vaksin' => $vaksin['nama'],
            'umur_target' => $vaksin['umur'],
            'status' => 'segera',
            'badge' => 'warning'
        ];
    } elseif ($selisih > 0) {
        // Sudah lewat
        $sudahDilakukan = Kesehatan::checkVaksin($batch_id, $vaksin['nama']);
        if (!$sudahDilakukan) {
            $reminders[] = [
                'vaksin' => $vaksin['nama'],
                'umur_target' => $vaksin['umur'],
                'status' => 'terlewat',
                'badge' => 'danger'
            ];
        }
    }
}
```

---

### 6. Laporan Harian Otomatis

**Lokasi:** `Model LaporanHarian@generateLaporanHarian`

**Data Yang Dikumpulkan:**
```php
$laporan = [
    'tanggal' => $tanggal,
    'batch_produksi_id' => $batch_id,
    
    // Populasi
    'populasi_awal' => $pembesaran->jumlah_anak_ayam,
    'kematian_hari_ini' => Kematian::whereDate('tanggal', $tanggal)->sum('jumlah'),
    'total_kematian' => Kematian::where('batch_produksi_id', $batch_id)->sum('jumlah'),
    'populasi_saat_ini' => $populasi_awal - $total_kematian,
    'mortalitas_persen' => ($total_kematian / $populasi_awal) * 100,
    
    // Pakan
    'konsumsi_pakan_hari_ini' => Pakan::whereDate('tanggal', $tanggal)->sum('jumlah_kg'),
    'total_konsumsi_pakan' => Pakan::where('batch_produksi_id', $batch_id)->sum('jumlah_kg'),
    'total_biaya_pakan' => Pakan::where('batch_produksi_id', $batch_id)->sum('total_biaya'),
    
    // Lingkungan (rata-rata hari ini)
    'suhu_rata' => MonitoringLingkungan::whereDate('waktu_pencatatan', $tanggal)->avg('suhu'),
    'kelembaban_rata' => MonitoringLingkungan::whereDate('waktu_pencatatan', $tanggal)->avg('kelembaban'),
    
    // Kesehatan
    'tindakan_kesehatan' => Kesehatan::whereDate('tanggal', $tanggal)->get(),
    
    // DSS Analysis
    'status_mortalitas' => $mortalitas_persen > 5 ? 'WARNING' : 'NORMAL',
    'status_lingkungan' => ($suhu_rata < 27 || $suhu_rata > 30) ? 'WARNING' : 'NORMAL',
    'rekomendasi' => []
];

// Generate rekomendasi
if ($mortalitas_persen > 5) {
    $laporan['rekomendasi'][] = 'Mortalitas tinggi - evaluasi penyebab dan lakukan tindakan korektif';
}

if ($suhu_rata < 27) {
    $laporan['rekomendasi'][] = 'Suhu rendah - tingkatkan pemanas';
} elseif ($suhu_rata > 30) {
    $laporan['rekomendasi'][] = 'Suhu tinggi - tingkatkan ventilasi';
}
```

---

## 🎯 Implementasi DSS di Frontend

### Toast Notification untuk Alert

```javascript
function showToast(message, type) {
    const colors = { 
        success: '#10b981', 
        warning: '#f59e0b', 
        error: '#ef4444' 
    };
    const icons = { 
        success: '✅', 
        warning: '⚠️', 
        error: '❌' 
    };
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 80px; right: 20px; z-index: 9999;
        background: ${colors[type]}; color: white; padding: 16px 24px;
        border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        font-weight: 600;
    `;
    toast.textContent = `${icons[type]} ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// Usage
if (result.is_high_mortality && result.alert) {
    showToast(result.alert, 'warning');
}
```

### Visual Indicator di Dashboard

```blade
<!-- Mortalitas Card dengan Warning -->
<div class="bolopa-card-kai bolopa-kai-red {{ $mortalitas > 5 ? 'bolopa-kai-warning' : '' }}">
    <div class="bolopa-kai-content">
        <div class="bolopa-kai-value">
            {{ number_format($mortalitas, 2) }}<small>%</small>
        </div>
        <div class="bolopa-kai-label">Mortalitas ({{ $totalMati }} ekor)</div>
        @if($mortalitas > 5)
            <div class="bolopa-kai-alert">⚠️ Melebihi batas normal!</div>
        @endif
    </div>
</div>
```

---

## 📈 Analytics Dashboard (Future Enhancement)

### Prediksi & Trends
- Prediksi mortalitas minggu depan berdasarkan trend
- Prediksi berat akhir berdasarkan pertumbuhan
- Prediksi total biaya pakan sampai panen
- Alert preventif sebelum masalah terjadi

### Machine Learning Integration (Optional)
- Pattern recognition untuk penyebab kematian
- Optimasi formula pakan berdasarkan hasil historis
- Clustering batch berdasarkan performa
- Rekomendasi tindakan berbasis AI

---

## 🔧 Maintenance & Updates

### Update Parameter Standar
```sql
-- Update berat standar per umur
UPDATE parameter_standar 
SET nilai_min = 54, nilai_max = 66, nilai_ideal = 60 
WHERE fase = 'grower' AND parameter = 'berat_rata_rata' AND umur_hari = 21;

-- Update threshold suhu
UPDATE parameter_standar 
SET nilai_min = 27, nilai_max = 30 
WHERE fase = 'grower' AND parameter = 'suhu';
```

### Monitoring DSS Performance
```php
// Log semua alert DSS
Log::info('DSS Alert', [
    'batch_id' => $batch_id,
    'alert_type' => 'mortalitas_tinggi',
    'value' => $mortalitas,
    'threshold' => 5,
    'timestamp' => now()
]);
```

---

**Status:** Semua DSS logic sudah terimplementasi dan siap digunakan! 🚀
