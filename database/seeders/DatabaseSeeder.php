<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call AdminUserSeeder to create admin users
        $this->call(AdminUserSeeder::class);
        
        // Seed data master untuk DSS
        $this->call(KandangSeeder::class);
        $this->call(StokPakanSeeder::class);
        $this->call(ParameterStandarSeeder::class);
        
        // Seed penetasan dummy data
        $this->call(PenetasanSeeder::class);
    }
}
