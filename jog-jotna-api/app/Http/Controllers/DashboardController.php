<?php
namespace App\Http\Controllers;
use App\Services\DashboardService;
use Illuminate\Http\{Request,JsonResponse};

class DashboardController extends Controller {
    public function __construct(private DashboardService $svc) {}
    public function responsable(): JsonResponse { return response()->json($this->svc->donneesResponsable()); }
    public function encadreur(Request $r): JsonResponse { return response()->json($this->svc->donneesEncadreur($r->user()->id)); }
    public function parent(Request $r): JsonResponse { return response()->json($this->svc->donneesParent($r->user()->id)); }
}
