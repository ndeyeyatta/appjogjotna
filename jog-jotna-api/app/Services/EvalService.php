<?php
namespace App\Services;
use App\Models\{Enfant,Evaluation,Indicateur,ScoreIndicateur,Alerte,Notification,Utilisateur,MesureNutritionnelle};
use Illuminate\Support\Facades\{DB,Log};

class EvalService {
    public function traiterEvaluation(array $d, Enfant $enfant): array {
        return DB::transaction(function() use($d,$enfant) {
            $r = $this->calculerScoreDimension($d['scores'],$d['dimension'],$enfant->age_mois);
            $evaluation = Evaluation::create([
                'enfant_id'=>$enfant->id,'encadreur_id'=>$d['encadreur_id'],
                'date_eval'=>$d['date_eval']??now(),'dimension'=>$d['dimension'],
                'score_global'=>$r['score'],'retard_detecte'=>$r['retard'],
                'urgent'=>$r['urgent'],'periode'=>$d['periode']??null,'commentaire'=>$d['commentaire']??null,
            ]);
            foreach($d['scores'] as $ind_id=>$val)
                ScoreIndicateur::create(['evaluation_id'=>$evaluation->id,'indicateur_id'=>$ind_id,'valeur'=>$val,'observation_qualitative'=>$d['observations'][$ind_id]??null]);
            $alerte = null;
            if($r['retard']) $alerte = $this->genererAlerte($evaluation,$enfant,$r);
            return ['evaluation'=>$evaluation,'scores'=>$r,'alerte'=>$alerte];
        });
    }

    public function calculerScoreDimension(array $scores, string $dimension, int $ageMois): array {
        if(empty($scores)) return ['score'=>0,'retard'=>false,'urgent'=>false,'pourcentage'=>0];
        $total = array_sum($scores); $max = count($scores)*2;
        $pct = $max>0?round(($total/$max)*100,1):0;
        $ind = Indicateur::parDimension($dimension)->parTranche($ageMois)->first();
        $sa = $ind?$ind->score_seuil_alerte:60; $su = $ind?$ind->score_seuil_urgent:40;
        return ['score'=>$pct,'retard'=>$pct<$sa,'urgent'=>$pct<$su,'pourcentage'=>$pct,'seuil_alerte'=>$sa];
    }

    public function calculerZScore(float $poids, int $ageMois, string $sexe): float {
        $ref = $this->getReferencesOMS($ageMois,$sexe);
        if(!$ref) { Log::warning("Ref OMS manquante age={$ageMois} sexe={$sexe}"); return 0.0; }
        return round(($poids-$ref['mediane'])/$ref['ecart_type'],2);
    }

    public function traiterMesureNutritionnelle(array $d, Enfant $enfant): array {
        $z = $this->calculerZScore($d['poids'],$enfant->age_mois,$enfant->sexe);
        $statut = $z<=-3?'malnutrition_severe':($z<=-2?'malnutrition_moderee':'normal');
        $mesure = MesureNutritionnelle::create([
            'enfant_id'=>$enfant->id,'encadreur_id'=>$d['encadreur_id'],
            'date_mesure'=>$d['date_mesure']??now()->toDateString(),
            'poids'=>$d['poids'],'taille'=>$d['taille'],
            'perimetre_brachial'=>$d['perimetre_brachial']??null,
            'z_score_poids_age'=>$z,'statut_nutritionnel'=>$statut,
        ]);
        $alerte = null;
        if($statut!=='normal') {
            $alerte = Alerte::create(['enfant_id'=>$enfant->id,'mesure_id'=>$mesure->id,'type_alerte'=>'nutritionnel','niveau'=>$statut==='malnutrition_severe'?'urgent':'modere','message'=>"Malnutrition chez {$enfant->nom_complet}. Z-score:{$z} DS.",'statut'=>'active']);
            $this->notifierActeurs($enfant,$alerte);
        }
        return ['mesure'=>$mesure,'z_score'=>$z,'statut'=>$statut,'alerte'=>$alerte];
    }

    private function genererAlerte(Evaluation $eval, Enfant $enfant, array $r): Alerte {
        $alerte = Alerte::create(['enfant_id'=>$enfant->id,'evaluation_id'=>$eval->id,'type_alerte'=>$eval->dimension,'niveau'=>$r['urgent']?'urgent':'modere','message'=>"Retard détecté chez {$enfant->nom_complet}. {$eval->dimension_libelle}. Score:{$r['score']}%.",'statut'=>'active']);
        $this->notifierActeurs($enfant,$alerte);
        return $alerte;
    }

    private function notifierActeurs(Enfant $enfant, Alerte $alerte): void {
        foreach([Utilisateur::parRole('encadreur')->actif()->first(),Utilisateur::responsable()->actif()->first()] as $u) {
            if($u) Notification::create(['destinataire_id'=>$u->id,'alerte_id'=>$alerte->id,'type'=>'alerte','message'=>"⚠ [{$alerte->niveau}] {$alerte->message}",'lu'=>false,'date_envoi'=>now()]);
        }
    }

    private function getReferencesOMS(int $mois, string $sexe): ?array {
        $t=['M'=>[24=>['mediane'=>12.2,'ecart_type'=>1.1],30=>['mediane'=>13.3,'ecart_type'=>1.2],36=>['mediane'=>14.3,'ecart_type'=>1.3],42=>['mediane'=>15.3,'ecart_type'=>1.4],48=>['mediane'=>16.3,'ecart_type'=>1.5],54=>['mediane'=>17.3,'ecart_type'=>1.6],60=>['mediane'=>18.3,'ecart_type'=>1.7]],
           'F'=>[24=>['mediane'=>11.5,'ecart_type'=>1.1],30=>['mediane'=>12.7,'ecart_type'=>1.2],36=>['mediane'=>13.9,'ecart_type'=>1.3],42=>['mediane'=>14.9,'ecart_type'=>1.4],48=>['mediane'=>15.9,'ecart_type'=>1.5],54=>['mediane'=>16.9,'ecart_type'=>1.6],60=>['mediane'=>18.0,'ecart_type'=>1.7]]];
        $ks=array_keys($t[$sexe]??[]); if(!$ks) return null;
        if($mois < min($ks) || $mois > max($ks)) return null;
        $proche=null; $min=PHP_INT_MAX;
        foreach($ks as $k) { $d=abs($k-$mois); if($d<$min){$min=$d;$proche=$k;} }
        return $proche?($t[$sexe][$proche]??null):null;
    }
}
