<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Update Berat Rata-rata untuk Demo FCR ===\n\n";

// Ambil batch pembesaran
$pembesaran = \App\Models\Pembesaran::where('batch_produksi_id', 'PB-20251006-001')->first();

if (!$pembesaran) {
    echo "Batch tidak ditemukan!\n";
    exit;
}

echo "Batch: {$pembesaran->batch_produksi_id}\n";
echo "Umur saat ini: " . \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(\Carbon\Carbon::now()) . " hari\n\n";

// Hitung berat rata-rata yang realistis berdasarkan umur
$umurHari = \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(\Carbon\Carbon::now());

// Estimasi berat berdasarkan umur (gram)
// DOC: 9g, 7 hari: 25g, 14 hari: 60g, 21 hari: 95g, 28 hari: 120g, 35 hari: 150g
if ($umurHari <= 7) {
    $beratEstimasi = 9 + ($umurHari * 2.3); // ~9-25g
} elseif ($umurHari <= 14) {
    $beratEstimasi = 25 + (($umurHari - 7) * 5); // ~25-60g
} elseif ($umurHari <= 21) {
    $beratEstimasi = 60 + (($umurHari - 14) * 5); // ~60-95g
} elseif ($umurHari <= 28) {
    $beratEstimasi = 95 + (($umurHari - 21) * 3.6); // ~95-120g
} else {
    $beratEstimasi = 120 + (($umurHari - 28) * 4.3); // ~120-150g
}

// Bulatkan ke atas
$beratBaru = ceil($beratEstimasi);

echo "Berat lama: {$pembesaran->berat_rata_rata} gram\n";
echo "Berat baru (estimasi): {$beratBaru} gram\n\n";

// Update
$pembesaran->berat_rata_rata = $beratBaru;
$pembesaran->save();

echo "✅ Berat rata-rata berhasil diupdate!\n\n";

// Hitung ulang FCR
$populasiAwal = $pembesaran->jumlah_anak_ayam;
$totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
$populasiSaatIni = $populasiAwal - $totalMati;
$totalPakan = \App\Models\Pakan::totalKonsumsiByBatch($pembesaran->batch_produksi_id);

$beratAwalKg = 0.009;
$beratAkhirKg = $beratBaru / 1000;
$pertambahanBeratPerEkor = $beratAkhirKg - $beratAwalKg;
$totalPertambahanBerat = $pertambahanBeratPerEkor * $populasiSaatIni;

echo "=== Hasil FCR ===\n";
echo "Populasi Saat Ini: {$populasiSaatIni} ekor\n";
echo "Total Pakan: {$totalPakan} kg\n";
echo "Berat Awal: 9 gram\n";
echo "Berat Akhir: {$beratBaru} gram\n";
echo "Pertambahan per ekor: " . number_format($pertambahanBeratPerEkor * 1000, 2) . " gram\n";
echo "Total Pertambahan: " . number_format($totalPertambahanBerat, 3) . " kg\n\n";

if ($totalPakan > 0 && $totalPertambahanBerat > 0 && $beratAkhirKg > $beratAwalKg) {
    $fcr = $totalPakan / $totalPertambahanBerat;
    echo "🎯 FCR = " . number_format($fcr, 2) . "\n";
    
    if ($fcr <= 2.5) {
        echo "📊 Status: Sangat Baik ✅ (hijau)\n";
    } elseif ($fcr <= 3.5) {
        echo "📊 Status: Baik 👍 (biru)\n";
    } elseif ($fcr <= 4.5) {
        echo "📊 Status: Cukup ⚠️ (kuning)\n";
    } else {
        echo "📊 Status: Perlu Perbaikan ❌ (merah)\n";
    }
} else {
    echo "❌ FCR masih 0 - Data tidak lengkap\n";
    if ($totalPakan <= 0) echo "   - Total pakan = 0\n";
    if ($totalPertambahanBerat <= 0) echo "   - Total pertambahan berat = 0 atau negatif\n";
}

echo "\n✨ Silakan refresh halaman pembesaran untuk melihat perubahan!\n";
