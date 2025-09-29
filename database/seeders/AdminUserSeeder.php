<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Owner
        $ownerUsername = 'lopa123';
        $ownerPassword = 'lopa123';

        $existingOwner = DB::table('pengguna')->where('nama_pengguna', $ownerUsername)->first();

        if ($existingOwner) {
            DB::table('pengguna')->where('id', $existingOwner->id)->update([
                'nama_pengguna' => $ownerUsername,
                'kata_sandi' => Hash::make($ownerPassword),
                'nama' => 'Bolopa Kakungnge Walinono',
                'surel' => 'bolopa@gmail.com',
                'peran' => 'owner',
            ]);
        } else {
            DB::table('pengguna')->insert([
                'nama' => 'Bolopa Kakungnge Walinono',
                'nama_pengguna' => $ownerUsername,
                'surel' => 'bolopa@gmail.com',
                'kata_sandi' => Hash::make($ownerPassword),
                'peran' => 'owner',
                'surel_terverifikasi_pada' => now(),
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
        }

        // Operator
        $operatorUsername = 'op1';
        $operatorPassword = 'op1';

        $existingOperator = DB::table('pengguna')->where('nama_pengguna', $operatorUsername)->first();

        if ($existingOperator) {
            DB::table('pengguna')->where('id', $existingOperator->id)->update([
                'nama_pengguna' => $operatorUsername,
                'kata_sandi' => Hash::make($operatorPassword),
                'nama' => 'Operator',
                'peran' => 'operator',
            ]);
        } else {
            DB::table('pengguna')->insert([
                'nama' => 'Operator',
                'nama_pengguna' => $operatorUsername,
                'surel' => $operatorUsername . '@local',
                'kata_sandi' => Hash::make($operatorPassword),
                'peran' => 'operator',
                'surel_terverifikasi_pada' => now(),
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
        }
    }
}
