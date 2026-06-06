<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

class Utilisateur extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    protected $table = 'utilisateurs';
    protected $fillable = ['nom','prenom','email','telephone','mot_de_passe','role','actif','langue'];
    protected $hidden   = ['mot_de_passe','remember_token'];
    protected $casts    = ['actif'=>'boolean'];
    protected function motDePasse(): Attribute {
        return Attribute::make(set: fn($v) => Hash::make($v));
    }
    public function getAuthPassword() { return $this->mot_de_passe; }
    public function enfants()       { return $this->hasMany(Enfant::class,'tuteur_id'); }
    public function evaluations()   { return $this->hasMany(Evaluation::class,'encadreur_id'); }
    public function observations()  { return $this->hasMany(Observation::class,'parent_id'); }
    public function notifications() { return $this->hasMany(Notification::class,'destinataire_id'); }
    public function rapports()      { return $this->hasMany(Rapport::class,'responsable_id'); }
    public function scopeActif($q)           { return $q->where('actif',true); }
    public function scopeParRole($q,$role)   { return $q->where('role',$role); }
    public function scopeResponsable($q)     { return $q->where('role','responsable'); }
    public function getNomCompletAttribute() { return "{$this->prenom} {$this->nom}"; }
}
