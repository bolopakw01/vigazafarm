<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'nama_pengguna' => fake()->unique()->userName(),
            'surel' => fake()->unique()->safeEmail(),
            'nomor_telepon' => fake()->phoneNumber(),
            'surel_terverifikasi_pada' => now(),
            'kata_sandi' => static::$password ??= Hash::make('password'),
            'peran' => 'operator',
            'token_ingat' => Str::random(10),
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'surel_terverifikasi_pada' => null,
        ]);
    }
}
