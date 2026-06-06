<?php
namespace App\Http\Controllers;
use App\Models\Enfant;
use App\Services\EvalService;
use Illuminate\Http\{Request,JsonResponse};

class MesureNutritionnelleController extends Controller {
    public function __construct(private EvalService $svc) {}
    public function store(Request $request, Enfant $enfant): JsonResponse {
        $data = $request->validate(['poids'=>'required|numeric|between:5,50','taille'=>'required|numeric|between:50,150','perimetre_brachial'=>'nullable|numeric|between:80,200','date_mesure'=>'nullable|date']);
        $data['encadreur_id']=$request->user()->id; $data['date_mesure']=$data['date_mesure']??now()->toDateString();
        $r = $this->svc->traiterMesureNutritionnelle($data,$enfant);
        return response()->json(['message'=>'Mesure enregistrée.','mesure'=>$r['mesure'],'z_score'=>$r['z_score'],'statut'=>$r['statut'],'alerte'=>$r['alerte']],201);
    }
    public function historique(Enfant $enfant): JsonResponse {
        return response()->json(['enfant'=>$enfant->nom_complet,'mesures'=>$enfant->mesuresNutritionnelles()->orderBy('date_mesure')->get(['date_mesure','poids','taille','z_score_poids_age','statut_nutritionnel'])]);
    }
}
