<?php
namespace App\Http\Controllers;
use App\Models\{Utilisateur, Enfant, Indicateur, Evaluation, ScoreIndicateur, MesureNutritionnelle, Observation, Alerte, Notification, Seance, Rapport};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\DB;

class BackupController extends Controller {
    public function export(Request $request): JsonResponse {
        $payload = [
            'exporte_le' => now()->toIso8601String(),
            'exporte_par' => $request->user()->email,
            'version' => '1.0',
            'donnees' => [
                'utilisateurs' => Utilisateur::all()->makeHidden(['mot_de_passe', 'remember_token']),
                'enfants' => Enfant::all(),
                'indicateurs' => Indicateur::all(),
                'evaluations' => Evaluation::all(),
                'scores_indicateurs' => ScoreIndicateur::all(),
                'mesures_nutritionnelles' => MesureNutritionnelle::all(),
                'observations' => Observation::all(),
                'alertes' => Alerte::all(),
                'notifications' => Notification::all(),
                'seances' => Seance::all(),
                'enfant_seance' => DB::table('enfant_seance')->get(),
                'rapports' => Rapport::all(),
            ],
        ];
        $filename = 'jog-jotna-backup-' . now()->format('Y-m-d_His') . '.json';
        return response()->json($payload, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function stats(): JsonResponse {
        return response()->json([
            'utilisateurs' => Utilisateur::count(),
            'enfants' => Enfant::count(),
            'evaluations' => Evaluation::count(),
            'observations' => Observation::count(),
            'alertes' => Alerte::count(),
            'seances' => Seance::count(),
            'rapports' => Rapport::count(),
            'taille_estimee_ko' => round(
                (Utilisateur::count() + Enfant::count() + Evaluation::count() + Observation::count()) * 0.5,
                1
            ),
        ]);
    }
}
