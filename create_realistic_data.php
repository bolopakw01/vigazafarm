<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Buat Data Realistis untuk Demo FCR ===\n\n";

// Ambil batch pembesaran
$pembesaran = \App\Models\Pembesaran::where('batch_produksi_id', 'PB-20251006-001')->first();

if (!$pembesaran) {
    echo "Batch tidak ditemukan!\n";
    exit;
}

echo "📋 Data Awal:\n";
echo "Batch: {$pembesaran->batch_produksi_id}\n";
echo "Populasi Awal: {$pembesaran->jumlah_anak_ayam} ekor\n";

$totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
$populasiSaatIni = $pembesaran->jumlah_anak_ayam - $totalMati;
echo "Populasi Saat Ini: {$populasiSaatIni} ekor\n\n";

// Scenario 1: Update populasi agar lebih realistis
echo "🔧 Updating data...\n\n";

// 1. Update jumlah anak ayam menjadi 1000 ekor
$pembesaran->jumlah_anak_ayam = 1000;
$pembesaran->save();
echo "✅ Populasi awal diupdate: 1000 ekor\n";

// 2. Update kematian menjadi 50 ekor
\DB::table('kematian')->where('batch_produksi_id', $pembesaran->batch_produksi_id)->delete();
\DB::table('kematian')->insert([
    'batch_produksi_id' => $pembesaran->batch_produksi_id,
    'tanggal' => \Carbon\Carbon::now()->subDays(2),
    'jumlah' => 50,
    'penyebab' => 'tidak_diketahui',
    'keterangan' => 'Demo data untuk FCR',
    'dibuat_pada' => now(),
    'diperbarui_pada' => now(),
]);
echo "✅ Kematian diupdate: 50 ekor\n";

// 3. Update berat rata-rata menjadi 120 gram (35 hari)
$pembesaran->berat_rata_rata = 120;
$pembesaran->save();
echo "✅ Berat rata-rata diupdate: 120 gram\n";

// 4. Update total pakan menjadi realistis (240 kg untuk 1000 ekor, 35 hari)
\DB::table('pakan')->where('batch_produksi_id', $pembesaran->batch_produksi_id)->delete();

// Simulasi pakan selama 35 hari
$stokPakan = \App\Models\StokPakan::first();
if ($stokPakan) {
    for ($i = 1; $i <= 35; $i++) {
        \DB::table('pakan')->insert([
            'batch_produksi_id' => $pembesaran->batch_produksi_id,
            'stok_pakan_id' => $stokPakan->id,
            'tanggal' => \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->addDays($i),
            'jumlah_kg' => round((1000 * 0.020 * $i / 35), 2), // Progresif dari 0.5kg s/d 20kg per hari
            'harga_per_kg' => $stokPakan->harga_per_kg,
            'total_biaya' => round((1000 * 0.020 * $i / 35) * $stokPakan->harga_per_kg, 2),
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);
    }
    echo "✅ Data pakan diupdate: 35 hari konsumsi\n\n";
}

// Hitung ulang
$totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
$populasiSaatIni = $pembesaran->jumlah_anak_ayam - $totalMati;
$totalPakan = \App\Models\Pakan::totalKonsumsiByBatch($pembesaran->batch_produksi_id);

$beratAwalKg = 0.009;
$beratAkhirKg = 120 / 1000;
$pertambahanBeratPerEkor = $beratAkhirKg - $beratAwalKg;
$totalPertambahanBerat = $pertambahanBeratPerEkor * $populasiSaatIni;

echo "📊 DATA BARU:\n";
echo "Populasi Awal: 1000 ekor\n";
echo "Kematian: 50 ekor\n";
echo "Populasi Saat Ini: {$populasiSaatIni} ekor\n";
echo "Total Pakan: " . number_format($totalPakan, 2) . " kg\n";
echo "Berat Awal: 9 gram\n";
echo "Berat Akhir: 120 gram\n";
echo "Pertambahan per ekor: " . number_format($pertambahanBeratPerEkor * 1000, 2) . " gram\n";
echo "Total Pertambahan: " . number_format($totalPertambahanBerat, 2) . " kg\n\n";

if ($totalPakan > 0 && $totalPertambahanBerat > 0) {
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
    echo "❌ FCR = 0\n";
}

echo "\n✨ Data demo berhasil dibuat! Silakan refresh halaman pembesaran.\n";
