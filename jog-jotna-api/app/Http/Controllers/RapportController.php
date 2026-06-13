<?php
namespace App\Http\Controllers;
use App\Models\{Rapport, Enfant, Alerte, Evaluation, MesureNutritionnelle};
use App\Services\DashboardService;
use App\Exports\RapportExport;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RapportController extends Controller {
    public function __construct(private DashboardService $svc) {}

    public function index(): JsonResponse {
        return response()->json(Rapport::with('responsable')->orderBy('date_generation', 'desc')->paginate(10));
    }

    public function genererPDF(Request $request): JsonResponse {
        $request->validate([
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
            'titre' => 'nullable|string|max:200',
        ]);
        $donnees = $this->collecterDonnees($request->periode_debut, $request->periode_fin);
        $html = view('rapports.rapport_pdf', $donnees)->render();
        $pdf = app(\Barryvdh\DomPDF\PDF::class)->loadHTML($html);
        $chemin = 'rapports/rapport_' . now()->format('Y-m-d_His') . '.pdf';
        Storage::put($chemin, $pdf->output());
        $rapport = Rapport::create([
            'responsable_id' => $request->user()->id,
            'periode_debut' => $request->periode_debut,
            'periode_fin' => $request->periode_fin,
            'format' => 'PDF',
            'fichier_path' => $chemin,
            'date_generation' => now(),
            'titre' => $request->titre ?? 'Rapport JÒG JOTNA ' . now()->format('d/m/Y'),
        ]);
        return response()->json([
            'message' => 'Rapport PDF généré.',
            'rapport' => $rapport,
            'download_url' => route('rapports.download', $rapport->id),
        ], 201);
    }

    public function genererExcel(Request $request): JsonResponse {
        $request->validate([
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
            'titre' => 'nullable|string|max:200',
        ]);
        $donnees = $this->collecterDonnees($request->periode_debut, $request->periode_fin);
        $titre = $request->titre ?? 'Rapport JÒG JOTNA ' . now()->format('d/m/Y');
        $chemin = 'rapports/rapport_' . now()->format('Y-m-d_His') . '.xlsx';
        Excel::store(new RapportExport($donnees, $titre), $chemin);
        $rapport = Rapport::create([
            'responsable_id' => $request->user()->id,
            'periode_debut' => $request->periode_debut,
            'periode_fin' => $request->periode_fin,
            'format' => 'Excel',
            'fichier_path' => $chemin,
            'date_generation' => now(),
            'titre' => $titre,
        ]);
        return response()->json([
            'message' => 'Rapport Excel généré.',
            'rapport' => $rapport,
            'download_url' => route('rapports.download', $rapport->id),
        ], 201);
    }

    public function download(Rapport $rapport) {
        if (!Storage::exists($rapport->fichier_path)) {
            return response()->json(['message' => 'Fichier introuvable.'], 404);
        }
        return Storage::download($rapport->fichier_path);
    }

    private function collecterDonnees(string $debut, string $fin): array {
        return [
            'periode_debut' => $debut,
            'periode_fin' => $fin,
            'total_enfants' => Enfant::actif()->count(),
            'nouvelles_inscriptions' => Enfant::whereBetween('date_inscription', [$debut, $fin])->count(),
            'evaluations' => Evaluation::whereBetween('date_eval', [$debut, $fin])->with('enfant')->get(),
            'alertes' => Alerte::whereBetween('created_at', [$debut, $fin])->with('enfant')->get(),
            'mesures' => MesureNutritionnelle::whereBetween('date_mesure', [$debut, $fin])->with('enfant')->get(),
            'stats_nutritionnel' => MesureNutritionnelle::whereBetween('date_mesure', [$debut, $fin])
                ->selectRaw('statut_nutritionnel, COUNT(*) as total')->groupBy('statut_nutritionnel')->get(),
            'par_village' => $this->svc->donneesResponsable()['par_village'],
        ];
    }
}
