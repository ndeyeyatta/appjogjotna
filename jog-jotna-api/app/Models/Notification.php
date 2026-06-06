<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Notification extends Model {
    protected $fillable = ['destinataire_id','alerte_id','observation_id','type','message','lu','date_envoi'];
    protected $casts = ['lu'=>'boolean','date_envoi'=>'datetime'];
    public function destinataire() { return $this->belongsTo(Utilisateur::class,'destinataire_id'); }
    public function alerte()       { return $this->belongsTo(Alerte::class); }
    public function scopeNonLue($q){ return $q->where('lu',false); }
    public function marquerLue(): void { $this->update(['lu'=>true]); }
}
