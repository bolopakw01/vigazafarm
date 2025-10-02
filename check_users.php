<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING DATABASE CONNECTION ===\n\n";

try {
    $users = DB::table('pengguna')->get(['id', 'nama', 'nama_pengguna', 'peran']);
    
    echo "Total users: " . $users->count() . "\n\n";
    
    echo "User List:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Nama: {$user->nama}\n";
        echo "Username: {$user->nama_pengguna}\n";
        echo "Role: {$user->peran}\n";
        echo str_repeat("-", 60) . "\n";
    }
    
    echo "\n✅ Database connection successful!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
