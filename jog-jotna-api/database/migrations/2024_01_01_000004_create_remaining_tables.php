<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('mesures_nutritionnelles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->onDelete('cascade');
            $table->foreignId('encadreur_id')->constrained('utilisateurs')->onDelete('restrict');
            $table->date('date_mesure');
            $table->float('poids'); $table->float('taille');
            $table->float('perimetre_brachial')->nullable();
            $table->float('z_score_poids_age')->nullable();
            $table->enum('statut_nutritionnel',['normal','malnutrition_moderee','malnutrition_severe'])->default('normal');
            $table->timestamps();
            $table->index(['enfant_id','date_mesure']);
        });
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('utilisateurs')->onDelete('restrict');
            $table->foreignId('encadreur_id')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->dateTime('date_obs');
            $table->enum('categorie',['comportement','alimentation','motricite','sommeil','relations','autre']);
            $table->text('description'); $table->text('description_wolof')->nullable();
            $table->enum('statut',['en_attente','validee','rejetee'])->default('en_attente');
            $table->enum('urgence',['informatif','a_surveiller','urgent'])->default('informatif');
            $table->dateTime('date_validation')->nullable();
            $table->text('commentaire_encadreur')->nullable();
            $table->timestamps();
        });
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->onDelete('cascade');
            $table->foreignId('evaluation_id')->nullable()->constrained('evaluations')->onDelete('set null');
            $table->foreignId('mesure_id')->nullable()->constrained('mesures_nutritionnelles')->onDelete('set null');
            $table->enum('type_alerte',['cognitif','moteur','socio_emotionnel','nutritionnel','absence']);
            $table->enum('niveau',['urgent','modere','informatif'])->default('informatif');
            $table->text('message');
            $table->enum('statut',['active','en_traitement','cloturee'])->default('active');
            $table->timestamps();
            $table->index(['statut','niveau']);
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destinataire_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('alerte_id')->nullable()->constrained('alertes')->onDelete('set null');
            $table->foreignId('observation_id')->nullable()->constrained('observations')->onDelete('set null');
            $table->enum('type',['alerte','observation','seance','rapport','systeme']);
            $table->text('message'); $table->boolean('lu')->default(false);
            $table->dateTime('date_envoi');
            $table->timestamps();
            $table->index(['destinataire_id','lu']);
        });
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encadreur_id')->constrained('utilisateurs')->onDelete('restrict');
            $table->date('date_seance'); $table->time('heure');
            $table->string('lieu')->default('Centre communautaire Diagambal');
            $table->text('activites_prevues')->nullable();
            $table->enum('statut',['planifiee','realisee','annulee'])->default('planifiee');
            $table->text('compte_rendu')->nullable();
            $table->timestamps();
        });
        Schema::create('enfant_seance', function (Blueprint $table) {
            $table->foreignId('enfant_id')->constrained('enfants')->onDelete('cascade');
            $table->foreignId('seance_id')->constrained('seances')->onDelete('cascade');
            $table->boolean('present')->default(false);
            $table->primary(['enfant_id','seance_id']);
        });
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responsable_id')->constrained('utilisateurs')->onDelete('restrict');
            $table->date('periode_debut'); $table->date('periode_fin');
            $table->enum('format',['PDF','Excel'])->default('PDF');
            $table->string('fichier_path')->nullable();
            $table->dateTime('date_generation');
            $table->string('titre')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        foreach(['rapports','enfant_seance','seances','notifications','alertes','observations','mesures_nutritionnelles'] as $t)
            Schema::dropIfExists($t);
    }
};
