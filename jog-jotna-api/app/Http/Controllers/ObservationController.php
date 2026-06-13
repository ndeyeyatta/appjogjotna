<?php
namespace App\Http\Controllers;
use App\Models\Observation;
use Illuminate\Http\{Request, JsonResponse};

class ObservationController extends Controller {
    public function store(Request $request): JsonResponse {
        if ($request->user()->role !== 'parent') {
            return response()->json(['message' => 'Seuls les parents peuvent soumettre des observations.'], 403);
        }
        $data = $request->validate([
            'enfant_id' => 'required|exists:enfants,id',
            'categorie' => 'required|in:comportement,alimentation,motricite,sommeil,relations,autre',
            'description' => 'required|string|min:10|max:1000',
            'description_wolof' => 'nullable|string|max:1000',
            'urgence' => 'nullable|in:informatif,a_surveiller,urgent',
        ]);
        $enfant = \App\Models\Enfant::findOrFail($data['enfant_id']);
        if ($enfant->tuteur_id !== $request->user()->id) {
            return response()->json(['message' => 'Cet enfant ne vous est pas rattaché.'], 403);
        }
        $data['parent_id'] = $request->user()->id;
        $data['date_obs'] = now();
        $data['statut'] = 'en_attente';
        $data['urgence'] = $data['urgence'] ?? 'informatif';
        $obs = Observation::create($data);
        return response()->json([
            'message' => 'Observation envoyée. L\'encadreur sera notifié.',
            'observation' => $obs->load('enfant'),
        ], 201);
    }

    public function enAttente(Request $request): JsonResponse {
        if (!in_array($request->user()->role, ['encadreur', 'responsable', 'admin'], true)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        return response()->json(
            Observation::enAttente()->with(['enfant', 'parent'])
                ->orderBy('urgence', 'desc')->orderBy('date_obs', 'desc')->get()
        );
    }

    public function valider(Request $request, Observation $obs): JsonResponse {
        if (!in_array($request->user()->role, ['encadreur', 'responsable', 'admin'], true)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        $request->validate(['commentaire' => 'nullable|string|max:500']);
        $obs->valider($request->user()->id, $request->commentaire ?? '');
        return response()->json(['message' => 'Observation validée.', 'observation' => $obs->fresh()]);
    }

    public function rejeter(Request $request, Observation $obs): JsonResponse {
        if (!in_array($request->user()->role, ['encadreur', 'responsable', 'admin'], true)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        $request->validate(['commentaire' => 'required|string|max:500']);
        $obs->rejeter($request->user()->id, $request->commentaire);
        return response()->json(['message' => 'Observation rejetée.', 'observation' => $obs->fresh()]);
    }

    public function mesObservations(Request $request): JsonResponse {
        return response()->json(
            Observation::where('parent_id', $request->user()->id)->with('enfant')
                ->orderBy('date_obs', 'desc')->paginate(10)
        );
    }
}
