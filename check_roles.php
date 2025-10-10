<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== User Roles ===\n";
$users = \App\Models\User::select('id', 'nama', 'nama_pengguna', 'peran')->get();
foreach ($users as $user) {
    echo "ID: {$user->id} | Nama: {$user->nama} | Username: {$user->nama_pengguna} | Role: {$user->peran}\n";
}

echo "\n=== Pembesaran Status ===\n";
$pembesaran = \App\Models\Pembesaran::select('id', 'batch_produksi_id', 'status_batch', 'tanggal_selesai')->first();
if ($pembesaran) {
    echo "Batch: {$pembesaran->batch_produksi_id}\n";
    echo "Status: " . ($pembesaran->status_batch ?? 'NULL') . "\n";
    echo "Tanggal Selesai: " . ($pembesaran->tanggal_selesai ?? 'NULL') . "\n";
}
