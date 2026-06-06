<?php
namespace App\Observers;
use App\Models\{Evaluation,Alerte,Notification,Utilisateur};
class EvaluationObserver {
    public function created(Evaluation $eval): void {
        if(!$eval->retard_detecte) return;
        $alerte = Alerte::create(['enfant_id'=>$eval->enfant_id,'evaluation_id'=>$eval->id,'type_alerte'=>$eval->dimension,'niveau'=>$eval->urgent?'urgent':'modere','message'=>"Retard chez {$eval->enfant->nom_complet}. {$eval->dimension_libelle}. Score:{$eval->score_global}%.",'statut'=>'active']);
        $this->notif($eval->encadreur_id,$alerte);
        $r = Utilisateur::responsable()->actif()->first();
        if($r) $this->notif($r->id,$alerte);
    }
    private function notif(int $id,Alerte $a): void {
        Notification::create(['destinataire_id'=>$id,'alerte_id'=>$a->id,'type'=>'alerte','message'=>"⚠ [{$a->niveau}] {$a->message}",'lu'=>false,'date_envoi'=>now()]);
    }
}
