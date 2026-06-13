<?php
namespace App\Http\Controllers;
use App\Models\{Enfant, Utilisateur};
use Illuminate\Http\{Request, JsonResponse};

class EnfantController extends Controller {
    private function peutVoirEnfant(Request $request, Enfant $enfant): bool {
        if (in_array($request->user()->role, ['encadreur', 'responsable', 'admin'], true)) return true;
        return $request->user()->role === 'parent' && $enfant->tuteur_id === $request->user()->id;
    }

    public function index(Request $request): JsonResponse {
        $q = Enfant::with('tuteur')
            ->when($request->user()->role === 'parent', fn($q) => $q->where('tuteur_id', $request->user()->id))
            ->when($request->village, fn($q) => $q->parVillage($request->village))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->search, fn($q) => $q->where(fn($q) => $q
                ->where('nom', 'like', "%{$request->search}%")
                ->orWhere('prenom', 'like', "%{$request->search}%")));
        return response()->json($q->orderBy('nom')->paginate(25));
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:M,F',
            'village' => 'required|string|max:100',
            'tuteur_id' => 'required|exists:utilisateurs,id',
            'date_inscription' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        if (!Utilisateur::where('id', $data['tuteur_id'])->where('role', 'parent')->exists()) {
            return response()->json(['message' => 'Le tuteur doit être un parent inscrit.'], 422);
        }
        return response()->json(Enfant::create($data)->load('tuteur'), 201);
    }

    public function show(Request $request, Enfant $enfant): JsonResponse {
        if (!$this->peutVoirEnfant($request, $enfant)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        return response()->json($enfant->load([
            'tuteur',
            'evaluations.encadreur',
            'evaluations.scoresIndicateurs.indicateur',
            'observations.parent',
            'mesuresNutritionnelles',
            'alertes' => fn($q) => $q->active(),
        ]));
    }

    public function update(Request $request, Enfant $enfant): JsonResponse {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'date_naissance' => 'sometimes|date|before:today',
            'sexe' => 'sometimes|in:M,F',
            'village' => 'sometimes|string|max:100',
            'statut' => 'sometimes|in:actif,pause,sorti',
            'tuteur_id' => 'sometimes|exists:utilisateurs,id',
            'notes' => 'nullable|string',
        ]);
        if (isset($data['tuteur_id']) && !Utilisateur::where('id', $data['tuteur_id'])->where('role', 'parent')->exists()) {
            return response()->json(['message' => 'Le tuteur doit être un parent inscrit.'], 422);
        }
        $enfant->update($data);
        return response()->json($enfant->fresh('tuteur'));
    }

    public function destroy(Enfant $enfant): JsonResponse {
        $enfant->delete();
        return response()->json(['message' => 'Profil supprimé (droit à l\'oubli).']);
    }

    public function progression(Request $request, Enfant $enfant): JsonResponse {
        if (!$this->peutVoirEnfant($request, $enfant)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        return response()->json([
            'enfant' => ['id' => $enfant->id, 'nom' => $enfant->nom_complet, 'age' => $enfant->age_ans],
            'progression' => $enfant->evaluations()->orderBy('date_eval')->get()->groupBy('dimension'),
            'mesures' => $enfant->mesuresNutritionnelles()->limit(6)->get(),
        ]);
    }

    public function parents(): JsonResponse {
        return response()->json(
            Utilisateur::parRole('parent')->actif()->orderBy('nom')->get(['id', 'nom', 'prenom', 'email'])
        );
    }
}
