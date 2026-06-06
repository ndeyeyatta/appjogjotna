<?php
namespace App\Http\Controllers;
use App\Models\Enfant;
use Illuminate\Http\{Request,JsonResponse};

class EnfantController extends Controller {
    public function index(Request $request): JsonResponse {
        $q = Enfant::with('tuteur')
            ->when($request->village, fn($q)=>$q->parVillage($request->village))
            ->when($request->statut,  fn($q)=>$q->where('statut',$request->statut))
            ->when($request->search,  fn($q)=>$q->where(fn($q)=>$q->where('nom','like',"%{$request->search}%")->orWhere('prenom','like',"%{$request->search}%")));
        return response()->json($q->orderBy('nom')->paginate(25));
    }
    public function store(Request $request): JsonResponse {
        $data = $request->validate(['nom'=>'required|string|max:100','prenom'=>'required|string|max:100','date_naissance'=>'required|date|before:today','sexe'=>'required|in:M,F','village'=>'required|string|max:100','tuteur_id'=>'required|exists:utilisateurs,id','date_inscription'=>'required|date','notes'=>'nullable|string']);
        return response()->json(Enfant::create($data)->load('tuteur'),201);
    }
    public function show(Enfant $enfant): JsonResponse {
        return response()->json($enfant->load(['tuteur','evaluations.encadreur','evaluations.scoresIndicateurs.indicateur','observations.parent','mesuresNutritionnelles','alertes'=>fn($q)=>$q->active()]));
    }
    public function update(Request $request, Enfant $enfant): JsonResponse {
        $data = $request->validate(['nom'=>'sometimes|string|max:100','prenom'=>'sometimes|string|max:100','date_naissance'=>'sometimes|date|before:today','sexe'=>'sometimes|in:M,F','village'=>'sometimes|string|max:100','statut'=>'sometimes|in:actif,pause,sorti','notes'=>'nullable|string']);
        $enfant->update($data);
        return response()->json($enfant->fresh('tuteur'));
    }
    public function destroy(Enfant $enfant): JsonResponse {
        $enfant->delete();
        return response()->json(['message'=>'Profil supprimé (droit à l\'oubli).']);
    }
    public function progression(Enfant $enfant): JsonResponse {
        return response()->json(['enfant'=>['id'=>$enfant->id,'nom'=>$enfant->nom_complet,'age'=>$enfant->age_ans],'progression'=>$enfant->evaluations()->orderBy('date_eval')->get()->groupBy('dimension'),'mesures'=>$enfant->mesuresNutritionnelles()->limit(6)->get()]);
    }
}
