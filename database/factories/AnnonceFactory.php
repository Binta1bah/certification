<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Localite;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Annonce>
 */
class AnnonceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'etat' => fake()->randomElement(['Comme Neuf', 'Bon Etat', 'Etat Moyen', 'A Bricoler']),
            'type' => fake()->randomElement(['Don', 'Echange']),
            'categorie_id' => function () {
                return Categorie::factory()->create()->id;
            },
            'localite_id' => function () {
                return Localite::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'date_limite' => fake()->date()
        ];
    }
}
