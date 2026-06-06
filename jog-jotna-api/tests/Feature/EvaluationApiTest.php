<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Utilisateur, Enfant, Indicateur};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\IndicateurOmsSeeder;

class EvaluationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(IndicateurOmsSeeder::class);
    }

    /** @test */
    public function peut_charger_grille_evaluation(): void
    {
        $encadreur = Utilisateur::factory()->create(['role' => 'encadreur', 'actif' => true]);
        $parent = Utilisateur::factory()->create(['role' => 'parent']);
        $enfant = Enfant::factory()->create([
            'tuteur_id' => $parent->id,
            'date_naissance' => now()->subMonths(36)->toDateString(),
        ]);

        Sanctum::actingAs($encadreur);

        $this->getJson("/api/evaluations/create/{$enfant->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['enfant', 'indicateurs']);
    }

    /** @test */
    public function retard_genere_alerte(): void
    {
        $encadreur = Utilisateur::factory()->create(['role' => 'encadreur', 'actif' => true]);
        $parent = Utilisateur::factory()->create(['role' => 'parent']);
        $enfant = Enfant::factory()->create([
            'tuteur_id' => $parent->id,
            'date_naissance' => now()->subMonths(36)->toDateString(),
        ]);

        Sanctum::actingAs($encadreur);

        $ids = Indicateur::parTranche($enfant->age_mois)->limit(4)->pluck('id');
        $scores = $ids->mapWithKeys(fn ($id) => [$id => 0])->toArray();

        $response = $this->postJson("/api/evaluations/{$enfant->id}", [
            'dimension' => 'cognitif',
            'scores' => $scores,
        ])->assertStatus(201)->json();

        $this->assertTrue($response['scores']['retard']);
        $this->assertNotNull($response['alerte']);
    }
}
