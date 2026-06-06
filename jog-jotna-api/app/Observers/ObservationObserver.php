<?php
namespace App\Observers;
use App\Models\{Observation,Notification,Utilisateur};
class ObservationObserver {
    public function created(Observation $obs): void {
        $enc = Utilisateur::parRole('encadreur')->actif()->first();
        if(!$enc) return;
        Notification::create(['destinataire_id'=>$enc->id,'observation_id'=>$obs->id,'type'=>'observation','message'=>"{$obs->categorie_icone} {$obs->parent->nom_complet} a signalé une observation pour {$obs->enfant->nom_complet} — {$obs->categorie_libelle}",'lu'=>false,'date_envoi'=>now()]);
    }
}
