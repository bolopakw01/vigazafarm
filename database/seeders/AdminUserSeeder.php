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

        $existingOwner = DB::table('vf_pengguna')->where('nama_pengguna', $ownerUsername)->first();

        if ($existingOwner) {
            DB::table('vf_pengguna')->where('id', $existingOwner->id)->update([
                'nama_pengguna' => $ownerUsername,
                'kata_sandi' => Hash::make($ownerPassword),
                'nama' => 'Bolopa Kakungnge Walinono',
                'surel' => 'bolopa@gmail.com',
                'nomor_telepon' => '081234567890',
                'peran' => 'owner',
            ]);
        } else {
            DB::table('vf_pengguna')->insert([
                'nama' => 'Bolopa Kakungnge Walinono',
                'nama_pengguna' => $ownerUsername,
                'surel' => 'bolopa@gmail.com',
                'nomor_telepon' => '081234567890',
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

        $existingOperator = DB::table('vf_pengguna')->where('nama_pengguna', $operatorUsername)->first();

        if ($existingOperator) {
            DB::table('vf_pengguna')->where('id', $existingOperator->id)->update([
                'nama_pengguna' => $operatorUsername,
                'kata_sandi' => Hash::make($operatorPassword),
                'nama' => 'Operator',
                'nomor_telepon' => '081111111111',
                'peran' => 'operator',
            ]);
        } else {
            DB::table('vf_pengguna')->insert([
                'nama' => 'Operator Cokro Sutisno Hadikusumo Mangunkarso TitisanDewo Mangku wanitolimo tanpobusono',
                'nama_pengguna' => $operatorUsername,
                'surel' => $operatorUsername . '@local',
                'nomor_telepon' => '081111111111',
                'kata_sandi' => Hash::make($operatorPassword),
                'peran' => 'operator',
                'surel_terverifikasi_pada' => now(),
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
        }
    }
}
