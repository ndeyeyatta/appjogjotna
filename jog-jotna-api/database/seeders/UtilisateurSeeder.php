<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Utilisateur,Enfant};
class UtilisateurSeeder extends Seeder {
    public function run(): void {
        // Texte en clair : le mutateur motDePasse du modèle Utilisateur applique Hash::make()
        $users = [
            ['nom'=>'DIOP','prenom'=>'Mamadou','email'=>'admin@jogjotna.sn','telephone'=>'+221770000001','mot_de_passe'=>'admin1234','role'=>'admin','actif'=>true,'langue'=>'fr'],
            ['nom'=>'NDIAYE','prenom'=>'Ousmane','email'=>'responsable@jogjotna.sn','telephone'=>'+221770000002','mot_de_passe'=>'resp1234','role'=>'responsable','actif'=>true,'langue'=>'fr'],
            ['nom'=>'DIALLO','prenom'=>'Aminata','email'=>'aminata.diallo@jogjotna.sn','telephone'=>'+221761000001','mot_de_passe'=>'encadreur1234','role'=>'encadreur','actif'=>true,'langue'=>'fr'],
            ['nom'=>'SECK','prenom'=>'Mariama','email'=>'mariama.seck@jogjotna.sn','telephone'=>'+221761000002','mot_de_passe'=>'encadreur1234','role'=>'encadreur','actif'=>true,'langue'=>'wo'],
            ['nom'=>'KONÉ','prenom'=>'Fatou','email'=>'fatou.kone@gmail.com','telephone'=>'+221782000001','mot_de_passe'=>'parent1234','role'=>'parent','actif'=>true,'langue'=>'wo'],
            ['nom'=>'BALDÉ','prenom'=>'Ibrahim','email'=>'ibrahim.balde@gmail.com','telephone'=>'+221782000002','mot_de_passe'=>'parent1234','role'=>'parent','actif'=>true,'langue'=>'fr'],
            ['nom'=>'FALL','prenom'=>'Aïssatou','email'=>'aissatou.fall@gmail.com','telephone'=>'+221782000003','mot_de_passe'=>'parent1234','role'=>'parent','actif'=>true,'langue'=>'wo'],
        ];
        foreach ($users as $u) {
            Utilisateur::updateOrCreate(['email' => $u['email']], $u);
        }
        $this->command->info('✓ '.count($users).' utilisateurs créés.');
    }
}

class EnfantSeeder extends Seeder {
    public function run(): void {
        $fatou   = Utilisateur::where('email','fatou.kone@gmail.com')->first();
        $ibrahim = Utilisateur::where('email','ibrahim.balde@gmail.com')->first();
        $aissatou= Utilisateur::where('email','aissatou.fall@gmail.com')->first();
        if(!$fatou||!$ibrahim||!$aissatou) { $this->command->warn('Parents non trouvés.'); return; }
        $enfants = [
            ['nom'=>'KONÉ','prenom'=>'Moussa','date_naissance'=>'2022-01-15','sexe'=>'M','village'=>'Diagambal','statut'=>'actif','date_inscription'=>'2024-01-10','tuteur_id'=>$fatou->id],
            ['nom'=>'KONÉ','prenom'=>'Aminata','date_naissance'=>'2023-04-20','sexe'=>'F','village'=>'Diagambal','statut'=>'actif','date_inscription'=>'2024-01-10','tuteur_id'=>$fatou->id],
            ['nom'=>'BALDÉ','prenom'=>'Seydou','date_naissance'=>'2021-07-08','sexe'=>'M','village'=>'Diagambal','statut'=>'actif','date_inscription'=>'2024-02-15','tuteur_id'=>$ibrahim->id],
            ['nom'=>'FALL','prenom'=>'Rokhaya','date_naissance'=>'2022-11-30','sexe'=>'F','village'=>'Ndoffane','statut'=>'actif','date_inscription'=>'2024-03-01','tuteur_id'=>$aissatou->id],
            ['nom'=>'NDIAYE','prenom'=>'Oumar','date_naissance'=>'2021-03-12','sexe'=>'M','village'=>'Diagambal','statut'=>'actif','date_inscription'=>'2024-01-20','tuteur_id'=>$ibrahim->id],
        ];
        foreach($enfants as $e) Enfant::firstOrCreate(['nom'=>$e['nom'],'prenom'=>$e['prenom'],'tuteur_id'=>$e['tuteur_id']],$e);
        $this->command->info('✓ '.count($enfants).' enfants créés.');
    }
}
