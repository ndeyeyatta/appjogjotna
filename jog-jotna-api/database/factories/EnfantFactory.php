<?php

namespace Database\Factories;

use App\Models\Enfant;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enfant>
 */
class EnfantFactory extends Factory
{
    protected $model = Enfant::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'date_naissance' => fake()->dateTimeBetween('-5 years', '-2 years')->format('Y-m-d'),
            'sexe' => fake()->randomElement(['M', 'F']),
            'village' => 'Diagambal',
            'statut' => 'actif',
            'date_inscription' => now()->toDateString(),
            'tuteur_id' => Utilisateur::factory()->create(['role' => 'parent'])->id,
            'notes' => null,
        ];
    }
}
