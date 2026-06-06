<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Enfant extends Model {
    use HasFactory;

    protected $fillable = ['nom','prenom','date_naissance','sexe','village','photo','statut','date_inscription','tuteur_id','notes'];
    protected $casts = ['date_naissance'=>'date','date_inscription'=>'date'];
    public function tuteur()              { return $this->belongsTo(Utilisateur::class,'tuteur_id'); }
    public function evaluations()         { return $this->hasMany(Evaluation::class)->orderBy('date_eval','desc'); }
    public function observations()        { return $this->hasMany(Observation::class)->orderBy('date_obs','desc'); }
    public function mesuresNutritionnelles() { return $this->hasMany(MesureNutritionnelle::class)->orderBy('date_mesure','desc'); }
    public function alertes()             { return $this->hasMany(Alerte::class); }
    public function seances()             { return $this->belongsToMany(Seance::class,'enfant_seance')->withPivot('present')->withTimestamps(); }
    public function getAgeMoisAttribute() { return Carbon::parse($this->date_naissance)->diffInMonths(now()); }
    public function getAgeAnsAttribute()  { $m=$this->age_mois; $a=intdiv($m,12); $r=$m%12; return $a>0?"{$a} ans {$r} mois":"{$r} mois"; }
    public function getNomCompletAttribute() { return "{$this->prenom} {$this->nom}"; }
    public function getTrancheAgeAttribute() { $m=$this->age_mois; foreach([24,36,48,60] as $t) if($m<=$t) return $t; return 60; }
    public function scopeActif($q)         { return $q->where('statut','actif'); }
    public function scopeEnAlerte($q)      { return $q->whereHas('alertes',fn($q)=>$q->where('statut','active')); }
    public function scopeParVillage($q,$v) { return $q->where('village',$v); }
    public function estEnAlerte()          { return $this->alertes()->where('statut','active')->exists(); }
}
