<?php
namespace App\Services;
use App\Models\{Enfant,Alerte,Evaluation,Seance,Rapport,Utilisateur,Observation,MesureNutritionnelle};
use Illuminate\Support\Facades\Cache;

class DashboardService {
    public function donneesResponsable(): array {
        return Cache::remember('dashboard_responsable',300,function() {
            return ['total_enfants'=>Enfant::actif()->count(),'alertes_actives'=>Alerte::active()->count(),'alertes_urgentes'=>Alerte::urgente()->count(),'seances_semaine'=>Seance::cetteSemaine()->count(),'observations_attente'=>Observation::enAttente()->count(),'par_village'=>$this->statsVillage(),'tendance_nutri'=>$this->tendanceNutri(),'enfants_alerte'=>$this->enfantsEnAlerte(),'derniers_rapports'=>Rapport::orderBy('created_at','desc')->limit(3)->get()];
        });
    }
    public function donneesEncadreur(int $id): array {
        return ['mes_enfants'=>Enfant::actif()->count(),'alertes_actives'=>Alerte::active()->count(),'observations_attente'=>Observation::enAttente()->count(),'prochaine_seance'=>Seance::planifiee()->where('encadreur_id',$id)->orderBy('date_seance')->first(),'evaluations_recentes'=>Evaluation::where('encadreur_id',$id)->with('enfant')->orderBy('created_at','desc')->limit(5)->get()];
    }
    public function donneesParent(int $id): array {
        return ['enfants'=>Enfant::where('tuteur_id',$id)->with(['alertes'=>fn($q)=>$q->active()])->get(),'prochaine_seance'=>Seance::planifiee()->orderBy('date_seance')->first(),'mes_observations'=>Observation::where('parent_id',$id)->orderBy('created_at','desc')->limit(5)->get()];
    }
    private function statsVillage(): array {
        return Enfant::actif()->selectRaw('village,COUNT(*) as total')->groupBy('village')->get()->map(fn($r)=>['village'=>$r->village,'total'=>$r->total,'alertes'=>Alerte::active()->whereHas('enfant',fn($q)=>$q->where('village',$r->village))->count()])->toArray();
    }
    private function tendanceNutri(): array {
        $res=[];
        for($i=5;$i>=0;$i--) {
            $d=now()->subMonths($i);
            $res[]=['mois'=>$d->format('M Y'),'normaux'=>MesureNutritionnelle::whereMonth('date_mesure',$d->month)->whereYear('date_mesure',$d->year)->where('statut_nutritionnel','normal')->count(),'moderes'=>MesureNutritionnelle::whereMonth('date_mesure',$d->month)->whereYear('date_mesure',$d->year)->where('statut_nutritionnel','malnutrition_moderee')->count(),'severes'=>MesureNutritionnelle::whereMonth('date_mesure',$d->month)->whereYear('date_mesure',$d->year)->where('statut_nutritionnel','malnutrition_severe')->count()];
        }
        return $res;
    }
    private function enfantsEnAlerte(): array {
        return Enfant::enAlerte()->with(['alertes'=>fn($q)=>$q->active()->urgente()])->limit(10)->get()->map(fn($e)=>['id'=>$e->id,'nom'=>$e->nom_complet,'age'=>$e->age_ans,'alerte'=>$e->alertes->first()?->type_alerte,'niveau'=>$e->alertes->first()?->niveau])->toArray();
    }
    public function viderCache(): void { Cache::forget('dashboard_responsable'); }
}
