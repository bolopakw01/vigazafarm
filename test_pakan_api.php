<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get pembesaran ID from URL or use first one
$pembesaranId = $_GET['id'] ?? null;

if (!$pembesaranId) {
    $pembesaran = \App\Models\Pembesaran::first();
    if (!$pembesaran) {
        die("No pembesaran found in database");
    }
    $pembesaranId = $pembesaran->id;
} else {
    $pembesaran = \App\Models\Pembesaran::find($pembesaranId);
    if (!$pembesaran) {
        die("Pembesaran ID $pembesaranId not found");
    }
}

echo "<h2>Testing Pakan API for Pembesaran ID: $pembesaranId</h2>";
echo "<p>Batch Produksi ID: {$pembesaran->batch_produksi_id}</p>";

// Get pakan data
$pakanList = \App\Models\Pakan::where('batch_produksi_id', $pembesaran->batch_produksi_id)
    ->with('stokPakan')
    ->orderByDesc('tanggal')
    ->limit(30)
    ->get();

echo "<h3>Found " . $pakanList->count() . " pakan records</h3>";

if ($pakanList->count() > 0) {
    echo "<h4>Sample Record (first):</h4>";
    echo "<pre>";
    print_r($pakanList->first()->toArray());
    echo "</pre>";
    
    echo "<h4>JSON Response (as API would return):</h4>";
    echo "<pre>";
    echo json_encode([
        'success' => true,
        'data' => $pakanList,
    ], JSON_PRETTY_PRINT);
    echo "</pre>";
}
