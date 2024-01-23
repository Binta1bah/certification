<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'telephone' => $this->faker->regexify('^7[0-9]{8}$'), // Génération d'un numéro de téléphone fictif
            'photo' => $this->faker->image('public/images', 100, 100, null, false), // Génération d'une URL de photo fictive
            'role_id' => 2,
            'remember_token' => Str::random(10),
        ];
    }
}
