<?php
namespace App\Http\Controllers;
use App\Models\Alerte;
use Illuminate\Http\{Request,JsonResponse};

class AlerteController extends Controller {
    public function index(Request $request): JsonResponse {
        return response()->json(Alerte::active()->with(['enfant','evaluation'])->when($request->niveau,fn($q)=>$q->where('niveau',$request->niveau))->orderByRaw("FIELD(niveau,'urgent','modere','informatif')")->orderBy('created_at','desc')->paginate(20));
    }
    public function prendreEnCharge(Alerte $alerte): JsonResponse {
        $alerte->prendreEnCharge();
        return response()->json(['message'=>'Alerte prise en charge.','alerte'=>$alerte->fresh()]);
    }
    public function cloturer(Alerte $alerte): JsonResponse {
        $alerte->cloturer();
        return response()->json(['message'=>'Alerte clôturée.','alerte'=>$alerte->fresh()]);
    }
    public function statistiques(): JsonResponse {
        return response()->json(['total_actives'=>Alerte::active()->count(),'urgentes'=>Alerte::urgente()->count(),'moderees'=>Alerte::active()->where('niveau','modere')->count(),'par_type'=>Alerte::active()->selectRaw('type_alerte,COUNT(*) as total')->groupBy('type_alerte')->get()]);
    }
}
