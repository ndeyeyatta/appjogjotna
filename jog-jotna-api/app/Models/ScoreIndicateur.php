<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScoreIndicateur extends Model {
    protected $table = 'scores_indicateurs';
    protected $fillable = ['evaluation_id','indicateur_id','valeur','observation_qualitative'];
    public function evaluation() { return $this->belongsTo(Evaluation::class); }
    public function indicateur() { return $this->belongsTo(Indicateur::class); }
    public function getLibelleValeurAttribute() { return match((int)$this->valeur){0=>'Non acquis',1=>'En cours',2=>'Acquis',default=>'?'}; }
}
