<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== UPDATING PASSWORD ===\n\n";

try {
    // Update password untuk user lopa123
    $updated = DB::table('pengguna')
        ->where('nama_pengguna', 'lopa123')
        ->update([
            'kata_sandi' => Hash::make('lopa123'),
            'diperbarui_pada' => now()
        ]);
    
    if ($updated) {
        echo "✅ Password untuk user 'lopa123' berhasil diubah menjadi 'lopa123'\n\n";
        
        // Tampilkan info user
        $user = DB::table('pengguna')
            ->where('nama_pengguna', 'lopa123')
            ->first(['nama', 'nama_pengguna', 'peran']);
        
        echo "User Info:\n";
        echo "  Nama: {$user->nama}\n";
        echo "  Username: {$user->nama_pengguna}\n";
        echo "  Password: lopa123\n";
        echo "  Role: {$user->peran}\n";
    } else {
        echo "⚠️ User 'lopa123' tidak ditemukan\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
