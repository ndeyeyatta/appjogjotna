<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MesureNutritionnelle extends Model {
    protected $table = 'mesures_nutritionnelles';
    protected $fillable = ['enfant_id','encadreur_id','date_mesure','poids','taille','perimetre_brachial','z_score_poids_age','statut_nutritionnel'];
    protected $casts = ['date_mesure'=>'date','poids'=>'float','taille'=>'float','z_score_poids_age'=>'float'];
    public function enfant()    { return $this->belongsTo(Enfant::class); }
    public function encadreur() { return $this->belongsTo(Utilisateur::class,'encadreur_id'); }
    public function alertes()   { return $this->hasMany(Alerte::class,'mesure_id'); }
    public function getStatutLibelleAttribute() { return match($this->statut_nutritionnel){'normal'=>'Normal','malnutrition_moderee'=>'Malnutrition modérée','malnutrition_severe'=>'Malnutrition sévère',default=>'?'}; }
}
