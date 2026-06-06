<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); $table->string('prenom');
            $table->date('date_naissance');
            $table->enum('sexe', ['M','F']);
            $table->string('village')->default('Diagambal');
            $table->string('photo')->nullable();
            $table->enum('statut', ['actif','pause','sorti'])->default('actif');
            $table->date('date_inscription');
            $table->foreignId('tuteur_id')->constrained('utilisateurs')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['village','statut']);
        });
    }
    public function down(): void { Schema::dropIfExists('enfants'); }
};
