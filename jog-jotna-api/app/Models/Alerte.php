<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Alerte extends Model {
    protected $fillable = ['enfant_id','evaluation_id','mesure_id','type_alerte','niveau','message','statut'];
    public function enfant()     { return $this->belongsTo(Enfant::class); }
    public function evaluation() { return $this->belongsTo(Evaluation::class); }
    public function mesure()     { return $this->belongsTo(MesureNutritionnelle::class,'mesure_id'); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function scopeActive($q)  { return $q->where('statut','active'); }
    public function scopeUrgente($q) { return $q->where('niveau','urgent'); }
    public function cloturer(): void { $this->update(['statut'=>'cloturee']); }
    public function prendreEnCharge(): void { $this->update(['statut'=>'en_traitement']); }
}
