<?php
namespace App\Http\Controllers;
use App\Models\{Seance,Notification};
use Illuminate\Http\{Request,JsonResponse};

class SeanceController extends Controller {
    public function index(): JsonResponse { return response()->json(Seance::with(['encadreur','enfants'])->orderBy('date_seance','desc')->paginate(15)); }
    public function store(Request $request): JsonResponse {
        $data = $request->validate(['date_seance'=>'required|date|after_or_equal:today','heure'=>'required|date_format:H:i','lieu'=>'nullable|string|max:200','activites_prevues'=>'nullable|string','enfant_ids'=>'required|array|min:1','enfant_ids.*'=>'exists:enfants,id']);
        $data['encadreur_id']=$request->user()->id;
        $seance = Seance::create($data);
        $seance->enfants()->attach($request->enfant_ids);
        foreach($seance->enfants as $e) {
            if($e->tuteur) Notification::create(['destinataire_id'=>$e->tuteur_id,'type'=>'seance','message'=>"📅 Séance le {$seance->date_seance->format('d/m/Y')} à {$seance->heure} — {$seance->lieu}",'lu'=>false,'date_envoi'=>now()]);
        }
        return response()->json($seance->load(['enfants','encadreur']),201);
    }
    public function show(Seance $seance): JsonResponse { return response()->json($seance->load(['encadreur','enfants.alertes'])); }
    public function update(Request $request, Seance $seance): JsonResponse {
        $seance->update($request->validate(['statut'=>'sometimes|in:planifiee,realisee,annulee','compte_rendu'=>'nullable|string','heure'=>'sometimes|date_format:H:i','lieu'=>'nullable|string|max:200']));
        return response()->json($seance->fresh(['encadreur','enfants']));
    }
    public function prochaines(): JsonResponse { return response()->json(Seance::planifiee()->with(['encadreur','enfants'])->where('date_seance','>=',today())->orderBy('date_seance')->limit(5)->get()); }
}
