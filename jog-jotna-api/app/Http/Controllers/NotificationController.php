<?php
namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\{Request,JsonResponse};

class NotificationController extends Controller {
    public function index(Request $r): JsonResponse { return response()->json(Notification::where('destinataire_id',$r->user()->id)->orderBy('date_envoi','desc')->paginate(20)); }
    public function nonLues(Request $r): JsonResponse {
        $ns = Notification::where('destinataire_id',$r->user()->id)->nonLue()->orderBy('date_envoi','desc')->get();
        return response()->json(['count'=>$ns->count(),'notifications'=>$ns]);
    }
    public function marquerLue(Notification $n, Request $r): JsonResponse {
        if($n->destinataire_id!==$r->user()->id) return response()->json(['message'=>'Non autorisé.'],403);
        $n->marquerLue();
        return response()->json(['message'=>'Notification marquée lue.']);
    }
    public function toutMarquerLues(Request $r): JsonResponse {
        Notification::where('destinataire_id',$r->user()->id)->nonLue()->update(['lu'=>true]);
        return response()->json(['message'=>'Toutes marquées comme lues.']);
    }
}
