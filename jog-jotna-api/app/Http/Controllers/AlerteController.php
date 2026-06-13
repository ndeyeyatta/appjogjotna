<?php
namespace App\Http\Controllers;
use App\Models\Alerte;
use Illuminate\Http\{Request, JsonResponse};

class AlerteController extends Controller {
    public function index(Request $request): JsonResponse {
        $q = Alerte::active()->with(['enfant', 'evaluation'])
            ->when($request->user()->role === 'parent', fn($q) => $q->whereHas(
                'enfant',
                fn($q) => $q->where('tuteur_id', $request->user()->id)
            ))
            ->when($request->niveau, fn($q) => $q->where('niveau', $request->niveau))
            ->orderByRaw("FIELD(niveau,'urgent','modere','informatif')")
            ->orderBy('created_at', 'desc');
        return response()->json($q->paginate(20));
    }

    public function prendreEnCharge(Request $request, Alerte $alerte): JsonResponse {
        if (!in_array($request->user()->role, ['encadreur', 'responsable', 'admin'], true)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        $alerte->prendreEnCharge();
        return response()->json(['message' => 'Alerte prise en charge.', 'alerte' => $alerte->fresh()]);
    }

    public function cloturer(Request $request, Alerte $alerte): JsonResponse {
        if (!in_array($request->user()->role, ['encadreur', 'responsable', 'admin'], true)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        $alerte->cloturer();
        return response()->json(['message' => 'Alerte clôturée.', 'alerte' => $alerte->fresh()]);
    }

    public function statistiques(): JsonResponse {
        return response()->json([
            'total_actives' => Alerte::active()->count(),
            'urgentes' => Alerte::urgente()->count(),
            'moderees' => Alerte::active()->where('niveau', 'modere')->count(),
            'par_type' => Alerte::active()->selectRaw('type_alerte, COUNT(*) as total')->groupBy('type_alerte')->get(),
        ]);
    }
}
