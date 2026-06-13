<?php
namespace App\Http\Controllers;
use App\Models\Indicateur;
use Illuminate\Http\{Request, JsonResponse};

class IndicateurController extends Controller {
    public function index(Request $request): JsonResponse {
        $q = Indicateur::query()
            ->when($request->dimension, fn($q) => $q->where('dimension', $request->dimension))
            ->orderBy('dimension')
            ->orderBy('tranche_age_min');
        return response()->json($q->get());
    }

    public function update(Request $request, Indicateur $indicateur): JsonResponse {
        $data = $request->validate([
            'libelle' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'score_seuil_alerte' => 'sometimes|numeric|between:0,100',
            'score_seuil_urgent' => 'sometimes|numeric|between:0,100',
        ]);
        $indicateur->update($data);
        return response()->json($indicateur->fresh());
    }
}
