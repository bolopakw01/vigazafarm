<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                    VIGAZAFARM - LOGIN CREDENTIALS                     ║\n";
echo "║                Decision Support System untuk Burung Puyuh             ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    $users = DB::table('pengguna')->get(['nama', 'nama_pengguna', 'surel', 'peran']);
    
    foreach ($users as $user) {
        $role = strtoupper($user->peran);
        $divider = str_repeat("─", 71);
        
        echo "┌{$divider}┐\n";
        echo "│ 👤 {$role} ACCOUNT" . str_repeat(" ", 71 - strlen(" 👤 {$role} ACCOUNT") - 1) . "│\n";
        echo "├{$divider}┤\n";
        echo "│ Nama     : " . str_pad($user->nama, 59) . "│\n";
        echo "│ Username : " . str_pad($user->nama_pengguna, 59) . "│\n";
        echo "│ Password : " . str_pad($user->nama_pengguna, 59) . "│\n";
        echo "│ Email    : " . str_pad($user->surel, 59) . "│\n";
        echo "│ Role     : " . str_pad(ucfirst($user->peran), 59) . "│\n";
        echo "└{$divider}┘\n";
        echo "\n";
    }
    
    echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║                          ACCESS INFORMATION                           ║\n";
    echo "╠═══════════════════════════════════════════════════════════════════════╣\n";
    echo "║ 🌐 Local URL    : http://localhost/vigazafarm/public                 ║\n";
    echo "║ 🔐 Login Page   : http://localhost/vigazafarm/public/login           ║\n";
    echo "║ 📊 Dashboard    : http://localhost/vigazafarm/public/dashboard       ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
    echo "📝 NOTE: Password sama dengan username untuk kemudahan development\n";
    echo "⚠️  Pastikan MySQL Server sudah berjalan di XAMPP Control Panel\n";
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
