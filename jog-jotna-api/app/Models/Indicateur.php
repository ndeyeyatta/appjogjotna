<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Indicateur extends Model {
    protected $fillable = ['libelle','description','dimension','tranche_age_min','tranche_age_max','score_seuil_alerte','score_seuil_urgent'];
    public function scoresIndicateurs() { return $this->hasMany(ScoreIndicateur::class); }
    public function scopeParDimension($q,$d) { return $q->where('dimension',$d); }
    public function scopeParTranche($q,$mois) { return $q->where('tranche_age_min','<=',$mois)->where('tranche_age_max','>=',$mois); }
}
