<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rapport extends Model {
    protected $fillable = ['responsable_id','periode_debut','periode_fin','format','fichier_path','date_generation','titre'];
    protected $casts = ['periode_debut'=>'date','periode_fin'=>'date','date_generation'=>'datetime'];
    public function responsable() { return $this->belongsTo(Utilisateur::class,'responsable_id'); }
}
