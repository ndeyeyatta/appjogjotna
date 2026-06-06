<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Seance extends Model {
    protected $fillable = ['encadreur_id','date_seance','heure','lieu','activites_prevues','statut','compte_rendu'];
    protected $casts = ['date_seance'=>'date'];
    public function encadreur() { return $this->belongsTo(Utilisateur::class,'encadreur_id'); }
    public function enfants()   { return $this->belongsToMany(Enfant::class,'enfant_seance')->withPivot('present')->withTimestamps(); }
    public function scopePlanifiee($q)   { return $q->where('statut','planifiee'); }
    public function scopeCetteSemaine($q){ return $q->whereBetween('date_seance',[now()->startOfWeek(),now()->endOfWeek()]); }
}
