<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug FCR Calculation ===\n\n";

// Ambil batch pembesaran pertama
$pembesaran = \App\Models\Pembesaran::first();

if (!$pembesaran) {
    echo "Tidak ada data pembesaran!\n";
    exit;
}

echo "Batch: {$pembesaran->batch_produksi_id}\n";
echo "Populasi Awal: {$pembesaran->jumlah_anak_ayam} ekor\n";

// Hitung populasi saat ini
$totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
$populasiSaatIni = $pembesaran->jumlah_anak_ayam - $totalMati;
echo "Total Mati: {$totalMati} ekor\n";
echo "Populasi Saat Ini: {$populasiSaatIni} ekor\n\n";

// Berat
$beratRataRata = $pembesaran->berat_rata_rata ?? 0;
echo "Berat Rata-rata: {$beratRataRata} gram\n";

// Total pakan
$totalPakan = \App\Models\Pakan::totalKonsumsiByBatch($pembesaran->batch_produksi_id);
echo "Total Pakan: {$totalPakan} kg\n\n";

// Hitung FCR
$beratAwalKg = 0.009; // 9 gram
$beratAkhirKg = $beratRataRata / 1000;
$pertambahanBeratPerEkor = $beratAkhirKg - $beratAwalKg;
$totalPertambahanBerat = $pertambahanBeratPerEkor * $populasiSaatIni;

echo "=== Perhitungan FCR ===\n";
echo "Berat Awal: " . ($beratAwalKg * 1000) . " gram (0.009 kg)\n";
echo "Berat Akhir: {$beratRataRata} gram (" . number_format($beratAkhirKg, 3) . " kg)\n";
echo "Pertambahan per ekor: " . number_format($pertambahanBeratPerEkor, 3) . " kg\n";
echo "Total Pertambahan Berat: " . number_format($totalPertambahanBerat, 2) . " kg\n\n";

if ($totalPakan > 0 && $totalPertambahanBerat > 0 && $beratAkhirKg > $beratAwalKg) {
    $fcr = $totalPakan / $totalPertambahanBerat;
    echo "FCR = {$totalPakan} / " . number_format($totalPertambahanBerat, 2) . " = " . number_format($fcr, 2) . "\n";
    
    if ($fcr <= 2.5) {
        echo "Status: Sangat Baik ✅\n";
    } elseif ($fcr <= 3.5) {
        echo "Status: Baik 👍\n";
    } elseif ($fcr <= 4.5) {
        echo "Status: Cukup ⚠️\n";
    } else {
        echo "Status: Perlu Perbaikan ❌\n";
    }
} else {
    echo "FCR = 0 (Data tidak lengkap)\n";
    echo "Alasan:\n";
    if ($totalPakan <= 0) echo "  - Total pakan = 0 atau negatif\n";
    if ($totalPertambahanBerat <= 0) echo "  - Total pertambahan berat = 0 atau negatif\n";
    if ($beratAkhirKg <= $beratAwalKg) echo "  - Berat akhir <= berat awal\n";
}
