<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model {
    protected $fillable = ['enfant_id','encadreur_id','date_eval','dimension','score_global','retard_detecte','urgent','periode','commentaire'];
    protected $casts = ['date_eval'=>'datetime','retard_detecte'=>'boolean','urgent'=>'boolean','score_global'=>'float'];
    public function enfant()          { return $this->belongsTo(Enfant::class); }
    public function encadreur()       { return $this->belongsTo(Utilisateur::class,'encadreur_id'); }
    public function scoresIndicateurs() { return $this->hasMany(ScoreIndicateur::class); }
    public function alertes()         { return $this->hasMany(Alerte::class); }
    public function scopeAvecRetard($q) { return $q->where('retard_detecte',true); }
    public function scopeUrgent($q)     { return $q->where('urgent',true); }
    public function scopeParDimension($q,$d) { return $q->where('dimension',$d); }
    public function getNiveauAttribute() { if($this->urgent) return 'urgent'; if($this->retard_detecte) return 'modere'; return 'normal'; }
    public function getDimensionLibelleAttribute() {
        return match($this->dimension) { 'cognitif'=>'Développement cognitif','moteur'=>'Développement moteur','socio_emotionnel'=>'Développement socio-émotionnel','nutritionnel'=>'Développement nutritionnel',default=>$this->dimension };
    }
}
