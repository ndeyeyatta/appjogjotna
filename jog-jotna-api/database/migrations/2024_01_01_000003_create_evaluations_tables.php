<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('indicateurs', function (Blueprint $table) {
            $table->id();
            $table->string('libelle'); $table->text('description')->nullable();
            $table->enum('dimension', ['cognitif','moteur','socio_emotionnel','nutritionnel']);
            $table->tinyInteger('tranche_age_min'); $table->tinyInteger('tranche_age_max');
            $table->float('score_seuil_alerte')->default(60);
            $table->float('score_seuil_urgent')->default(40);
            $table->timestamps();
            $table->index(['dimension','tranche_age_min','tranche_age_max']);
        });
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->onDelete('cascade');
            $table->foreignId('encadreur_id')->constrained('utilisateurs')->onDelete('restrict');
            $table->dateTime('date_eval');
            $table->enum('dimension', ['cognitif','moteur','socio_emotionnel','nutritionnel']);
            $table->float('score_global')->nullable();
            $table->boolean('retard_detecte')->default(false);
            $table->boolean('urgent')->default(false);
            $table->string('periode')->nullable(); $table->text('commentaire')->nullable();
            $table->timestamps();
            $table->index(['enfant_id','date_eval']);
        });
        Schema::create('scores_indicateurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('indicateur_id')->constrained('indicateurs')->onDelete('restrict');
            $table->tinyInteger('valeur');
            $table->text('observation_qualitative')->nullable();
            $table->timestamps();
            $table->unique(['evaluation_id','indicateur_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('scores_indicateurs');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('indicateurs');
    }
};
