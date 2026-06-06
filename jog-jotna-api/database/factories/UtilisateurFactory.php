<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'mot_de_passe' => 'password123',
            'role' => fake()->randomElement(['parent', 'encadreur', 'responsable', 'admin']),
            'actif' => true,
            'langue' => fake()->randomElement(['fr', 'wo']),
        ];
    }
}
