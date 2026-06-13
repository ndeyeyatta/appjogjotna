<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, EnfantController, EvaluationController, ObservationController,
    AlerteController, DashboardController, SeanceController, NotificationController,
    RapportController, MesureNutritionnelleController, UtilisateurController,
    IndicateurController, ParametreController, BackupController
};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/changer-mot-de-passe', [AuthController::class, 'changerMotDePasse']);

    Route::get('parametres', [ParametreController::class, 'show']);

    Route::get('enfants', [EnfantController::class, 'index']);
    Route::get('enfants/{enfant}', [EnfantController::class, 'show']);
    Route::get('enfants/{enfant}/progression', [EnfantController::class, 'progression']);

    Route::middleware('role:encadreur,responsable,admin')->group(function () {
        Route::get('enfants-liste/parents', [EnfantController::class, 'parents']);
        Route::post('enfants', [EnfantController::class, 'store']);
        Route::put('enfants/{enfant}', [EnfantController::class, 'update']);
        Route::patch('enfants/{enfant}', [EnfantController::class, 'update']);
        Route::delete('enfants/{enfant}', [EnfantController::class, 'destroy']);

        Route::get('evaluations/create/{enfant}', [EvaluationController::class, 'create']);
        Route::post('evaluations/{enfant}', [EvaluationController::class, 'store']);
        Route::get('enfants/{enfant}/evaluations', [EvaluationController::class, 'parEnfant']);
        Route::get('enfants/{enfant}/courbe-progression', [EvaluationController::class, 'courbeProgression']);

        Route::post('mesures/{enfant}', [MesureNutritionnelleController::class, 'store']);
        Route::get('enfants/{enfant}/mesures', [MesureNutritionnelleController::class, 'historique']);

        Route::get('observations/en-attente', [ObservationController::class, 'enAttente']);
        Route::post('observations/{observation}/valider', [ObservationController::class, 'valider']);
        Route::post('observations/{observation}/rejeter', [ObservationController::class, 'rejeter']);

        Route::post('alertes/{alerte}/prendre-en-charge', [AlerteController::class, 'prendreEnCharge']);
        Route::post('alertes/{alerte}/cloturer', [AlerteController::class, 'cloturer']);

        Route::apiResource('seances', SeanceController::class);
        Route::get('seances/prochaines', [SeanceController::class, 'prochaines']);

        Route::get('dashboard/encadreur', [DashboardController::class, 'encadreur']);
    });

    Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show']);

    Route::middleware('role:parent')->group(function () {
        Route::post('observations', [ObservationController::class, 'store']);
        Route::get('observations/mes-observations', [ObservationController::class, 'mesObservations']);
        Route::get('dashboard/parent', [DashboardController::class, 'parent']);
    });

    Route::middleware('role:responsable,admin')->group(function () {
        Route::get('dashboard/responsable', [DashboardController::class, 'responsable']);
        Route::get('rapports', [RapportController::class, 'index']);
        Route::post('rapports/pdf', [RapportController::class, 'genererPDF']);
        Route::post('rapports/excel', [RapportController::class, 'genererExcel']);
    });

    Route::get('alertes', [AlerteController::class, 'index']);
    Route::get('alertes/statistiques', [AlerteController::class, 'statistiques']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/non-lues', [NotificationController::class, 'nonLues']);
    Route::post('notifications/{notification}/lue', [NotificationController::class, 'marquerLue']);
    Route::post('notifications/tout-lire', [NotificationController::class, 'toutMarquerLues']);

    Route::get('rapports/{rapport}/download', [RapportController::class, 'download'])->name('rapports.download');

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('utilisateurs', UtilisateurController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('indicateurs', [IndicateurController::class, 'index']);
        Route::put('indicateurs/{indicateur}', [IndicateurController::class, 'update']);
        Route::patch('indicateurs/{indicateur}', [IndicateurController::class, 'update']);
        Route::put('parametres', [ParametreController::class, 'update']);
        Route::get('admin/backup/stats', [BackupController::class, 'stats']);
        Route::get('admin/backup/export', [BackupController::class, 'export']);
    });
});
