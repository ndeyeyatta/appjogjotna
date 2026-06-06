<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_correct(): void
    {
        Utilisateur::factory()->create([
            'email' => 'test@jog.sn',
            'mot_de_passe' => 'pass1234',
            'actif' => true,
            'role' => 'encadreur',
        ]);

        $this->postJson('/api/login', [
            'email' => 'test@jog.sn',
            'mot_de_passe' => 'pass1234',
        ])->assertStatus(200)
            ->assertJsonStructure(['token', 'utilisateur' => ['id', 'role']]);
    }

    /** @test */
    public function login_echoue_mauvais_mdp(): void
    {
        Utilisateur::factory()->create([
            'email' => 'test@jog.sn',
            'mot_de_passe' => 'correct',
            'actif' => true,
        ]);

        $this->postJson('/api/login', [
            'email' => 'test@jog.sn',
            'mot_de_passe' => 'mauvais',
        ])->assertStatus(422);
    }

    /** @test */
    public function route_protegee_sans_token(): void
    {
        $this->getJson('/api/enfants')->assertStatus(401);
    }
}
