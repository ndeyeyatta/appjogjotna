<?php
namespace Tests\Unit;
use Tests\TestCase;
use App\Services\EvalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IndicateurOmsSeeder;

class EvalServiceTest extends TestCase {
    use RefreshDatabase;
    private EvalService $svc;
    protected function setUp(): void { parent::setUp(); $this->svc=new EvalService(); $this->seed(IndicateurOmsSeeder::class); }

    /** @test */ public function calcule_z_score_enfant_normal(): void {
        $z = $this->svc->calculerZScore(14.3,36,'M');
        $this->assertGreaterThan(-1.0,$z); $this->assertLessThan(1.0,$z);
    }
    /** @test */ public function calcule_z_score_malnutrition_moderee(): void {
        $z = $this->svc->calculerZScore(9.5,24,'M');
        $this->assertLessThan(-2.0,$z); $this->assertGreaterThan(-3.0,$z);
    }
    /** @test */ public function calcule_z_score_malnutrition_severe(): void {
        $z = $this->svc->calculerZScore(8.5,36,'F');
        $this->assertLessThan(-3.0,$z);
    }
    /** @test */ public function retourne_zero_si_reference_manquante(): void {
        $z = $this->svc->calculerZScore(25.0,200,'M');
        $this->assertEquals(0.0,$z);
    }
    /** @test */ public function calcule_score_dimension_tous_acquis(): void {
        $scores=[1=>2,2=>2,3=>2,4=>2];
        $r = $this->svc->calculerScoreDimension($scores,'cognitif',36);
        $this->assertEquals(100.0,$r['score']); $this->assertFalse($r['retard']);
    }
    /** @test */ public function detecte_retard_urgent_si_score_zero(): void {
        $scores=[1=>0,2=>0,3=>0,4=>0];
        $r = $this->svc->calculerScoreDimension($scores,'cognitif',36);
        $this->assertEquals(0.0,$r['score']); $this->assertTrue($r['retard']); $this->assertTrue($r['urgent']);
    }
    /** @test */ public function retourne_zero_si_scores_vides(): void {
        $r = $this->svc->calculerScoreDimension([],'moteur',48);
        $this->assertEquals(0,$r['score']); $this->assertFalse($r['retard']);
    }
}
