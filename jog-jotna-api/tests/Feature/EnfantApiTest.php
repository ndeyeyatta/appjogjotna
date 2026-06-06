<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Utilisateur, Enfant};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class EnfantApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function encadreur_peut_creer_enfant(): void
    {
        $encadreur = Utilisateur::factory()->create(['role' => 'encadreur', 'actif' => true]);
        $parent = Utilisateur::factory()->create(['role' => 'parent']);

        Sanctum::actingAs($encadreur);

        $this->postJson('/api/enfants', [
            'nom' => 'DIALLO',
            'prenom' => 'Fatou',
            'date_naissance' => '2021-06-15',
            'sexe' => 'F',
            'village' => 'Diagambal',
            'tuteur_id' => $parent->id,
            'date_inscription' => now()->toDateString(),
        ])->assertStatus(201)
            ->assertJsonFragment(['nom' => 'DIALLO']);
    }

    /** @test */
    public function peut_rechercher_par_nom(): void
    {
        $encadreur = Utilisateur::factory()->create(['role' => 'encadreur', 'actif' => true]);
        $parent = Utilisateur::factory()->create(['role' => 'parent']);

        Sanctum::actingAs($encadreur);

        Enfant::factory()->create([
            'nom' => 'KOUYATE',
            'prenom' => 'Oumar',
            'tuteur_id' => $parent->id,
        ]);

        $this->getJson('/api/enfants?search=KOUYATE')
            ->assertStatus(200)
            ->assertJsonFragment(['nom' => 'KOUYATE']);
    }
}
