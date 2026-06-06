<?php
namespace App\Http\Controllers;
use App\Models\{Evaluation,Enfant,Indicateur};
use App\Services\EvalService;
use Illuminate\Http\{Request,JsonResponse};

class EvaluationController extends Controller {
    public function __construct(private EvalService $evalService) {}
    public function create(Enfant $enfant): JsonResponse {
        return response()->json(['enfant'=>['id'=>$enfant->id,'nom_complet'=>$enfant->nom_complet,'age_mois'=>$enfant->age_mois,'age_ans'=>$enfant->age_ans,'sexe'=>$enfant->sexe,'tranche_age'=>$enfant->tranche_age],'indicateurs'=>Indicateur::parTranche($enfant->age_mois)->orderBy('dimension')->get()->groupBy('dimension')]);
    }
    public function store(Request $request, Enfant $enfant): JsonResponse {
        $data = $request->validate(['dimension'=>'required|in:cognitif,moteur,socio_emotionnel,nutritionnel','scores'=>'required|array|min:1','scores.*'=>'required|integer|between:0,2','observations'=>'nullable|array','commentaire'=>'nullable|string|max:1000','periode'=>'nullable|string|max:20']);
        $data['encadreur_id']=$request->user()->id; $data['date_eval']=now();
        $r = $this->evalService->traiterEvaluation($data,$enfant);
        return response()->json(['message'=>'Évaluation enregistrée.','evaluation'=>$r['evaluation'],'scores'=>$r['scores'],'alerte'=>$r['alerte']],201);
    }
    public function show(Evaluation $evaluation): JsonResponse {
        return response()->json($evaluation->load(['enfant','encadreur','scoresIndicateurs.indicateur','alertes']));
    }
    public function parEnfant(Enfant $enfant): JsonResponse {
        return response()->json($enfant->evaluations()->with(['encadreur','scoresIndicateurs.indicateur'])->orderBy('date_eval','desc')->paginate(10));
    }
    public function courbeProgression(Enfant $enfant): JsonResponse {
        $data=[];
        foreach(['cognitif','moteur','socio_emotionnel','nutritionnel'] as $d)
            $data[$d]=$enfant->evaluations()->parDimension($d)->orderBy('date_eval')->get(['date_eval','score_global','retard_detecte'])->map(fn($e)=>['date'=>$e->date_eval->format('d/m/Y'),'score'=>$e->score_global,'retard'=>$e->retard_detecte]);
        return response()->json(['enfant'=>$enfant->nom_complet,'age'=>$enfant->age_ans,'courbes'=>$data]);
    }
}
