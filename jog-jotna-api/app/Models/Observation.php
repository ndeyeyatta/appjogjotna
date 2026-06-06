<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Observation extends Model {
    protected $fillable = ['enfant_id','parent_id','encadreur_id','date_obs','categorie','description','description_wolof','statut','urgence','date_validation','commentaire_encadreur'];
    protected $casts = ['date_obs'=>'datetime','date_validation'=>'datetime'];
    public function enfant()    { return $this->belongsTo(Enfant::class); }
    public function parent()    { return $this->belongsTo(Utilisateur::class,'parent_id'); }
    public function encadreur() { return $this->belongsTo(Utilisateur::class,'encadreur_id'); }
    public function scopeEnAttente($q) { return $q->where('statut','en_attente'); }
    public function getCategorieLibelleAttribute() { return match($this->categorie){'comportement'=>'Comportement','alimentation'=>'Alimentation','motricite'=>'Motricité','sommeil'=>'Sommeil','relations'=>'Relations','autre'=>'Autre',default=>$this->categorie}; }
    public function getCategorieIconeAttribute()   { return match($this->categorie){'comportement'=>'🗣','alimentation'=>'🍽','motricite'=>'🏃','sommeil'=>'😴','relations'=>'👥','autre'=>'❓',default=>'📝'}; }
    public function valider(int $id,string $c=''): void { $this->update(['statut'=>'validee','encadreur_id'=>$id,'date_validation'=>now(),'commentaire_encadreur'=>$c]); }
    public function rejeter(int $id,string $c=''): void { $this->update(['statut'=>'rejetee','encadreur_id'=>$id,'date_validation'=>now(),'commentaire_encadreur'=>$c]); }
}
