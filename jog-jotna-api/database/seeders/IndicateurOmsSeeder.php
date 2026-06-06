<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Indicateur;

class IndicateurOmsSeeder extends Seeder {
    public function run(): void {
        $jalons = [
            // COGNITIF
            ['libelle'=>'Trie les objets par forme','dimension'=>'cognitif','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Trie les objets par couleur','dimension'=>'cognitif','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Imite les actions des adultes','dimension'=>'cognitif','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Reconnaît les objets familiers','dimension'=>'cognitif','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Complète un puzzle 3-4 pièces','dimension'=>'cognitif','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Comprend les concepts dedans/dehors','dimension'=>'cognitif','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Compte jusqu\'à 3','dimension'=>'cognitif','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Comprend les concepts temporels','dimension'=>'cognitif','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Compte jusqu\'à 4 objets','dimension'=>'cognitif','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Comprend même/différent','dimension'=>'cognitif','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Résout un puzzle 5+ pièces','dimension'=>'cognitif','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Comprend les relations cause-effet','dimension'=>'cognitif','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Compte jusqu\'à 10 objets','dimension'=>'cognitif','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Reconnaît les lettres de l\'alphabet','dimension'=>'cognitif','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Dessine un triangle','dimension'=>'cognitif','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Comprend les concepts de temps','dimension'=>'cognitif','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            // MOTEUR
            ['libelle'=>'Monte et descend les escaliers','dimension'=>'moteur','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Court sans tomber','dimension'=>'moteur','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Donne un coup de pied dans un ballon','dimension'=>'moteur','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Empile 4+ cubes','dimension'=>'moteur','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Pédale sur un tricycle','dimension'=>'moteur','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Monte/descend escaliers en alternant','dimension'=>'moteur','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Utilise des ciseaux','dimension'=>'moteur','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Tient un crayon correctement','dimension'=>'moteur','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Saute sur un pied','dimension'=>'moteur','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Attrape une balle rebondie','dimension'=>'moteur','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Dessine un carré','dimension'=>'moteur','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Boutonne ses vêtements','dimension'=>'moteur','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Saut à cloche-pied','dimension'=>'moteur','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Se balance sur un pied 10 secondes','dimension'=>'moteur','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Copie des lettres simples','dimension'=>'moteur','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Utilise des couverts correctement','dimension'=>'moteur','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            // SOCIO-EMOTIONNEL
            ['libelle'=>'Joue à côté des autres enfants','dimension'=>'socio_emotionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>55,'score_seuil_urgent'=>35],
            ['libelle'=>'Montre de l\'affection aux proches','dimension'=>'socio_emotionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>55,'score_seuil_urgent'=>35],
            ['libelle'=>'Imite les comportements des adultes','dimension'=>'socio_emotionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>55,'score_seuil_urgent'=>35],
            ['libelle'=>'Exprime ses émotions basiques','dimension'=>'socio_emotionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>55,'score_seuil_urgent'=>35],
            ['libelle'=>'Joue avec d\'autres enfants','dimension'=>'socio_emotionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Montre une gamme d\'émotions','dimension'=>'socio_emotionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Comprend les règles simples de jeu','dimension'=>'socio_emotionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'S\'habille seul (partiellement)','dimension'=>'socio_emotionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Coopère avec les pairs','dimension'=>'socio_emotionnel','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Distingue réel et imaginaire','dimension'=>'socio_emotionnel','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Négocie et résout des conflits','dimension'=>'socio_emotionnel','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Manifeste de l\'empathie','dimension'=>'socio_emotionnel','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Respecte les règles de groupe','dimension'=>'socio_emotionnel','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Gère ses émotions en groupe','dimension'=>'socio_emotionnel','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Fait preuve d\'initiative','dimension'=>'socio_emotionnel','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Distingue les genres','dimension'=>'socio_emotionnel','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            // NUTRITIONNEL
            ['libelle'=>'Mange seul avec une cuillère','dimension'=>'nutritionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Boit seul dans un verre','dimension'=>'nutritionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Accepte une variété d\'aliments','dimension'=>'nutritionnel','tranche_age_min'=>21,'tranche_age_max'=>27,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Mange avec une fourchette','dimension'=>'nutritionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Exprime sa faim et sa satiété','dimension'=>'nutritionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Consomme 3 repas équilibrés par jour','dimension'=>'nutritionnel','tranche_age_min'=>30,'tranche_age_max'=>42,'score_seuil_alerte'=>60,'score_seuil_urgent'=>40],
            ['libelle'=>'Utilise correctement les couverts','dimension'=>'nutritionnel','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Reconnaît les aliments sains','dimension'=>'nutritionnel','tranche_age_min'=>42,'tranche_age_max'=>54,'score_seuil_alerte'=>65,'score_seuil_urgent'=>45],
            ['libelle'=>'Mange de façon autonome et propre','dimension'=>'nutritionnel','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
            ['libelle'=>'Comprend l\'importance d\'une bonne nutrition','dimension'=>'nutritionnel','tranche_age_min'=>54,'tranche_age_max'=>66,'score_seuil_alerte'=>70,'score_seuil_urgent'=>50],
        ];
        foreach($jalons as $j) Indicateur::firstOrCreate(['libelle'=>$j['libelle'],'dimension'=>$j['dimension']],$j);
        $this->command->info('✓ '.count($jalons).' jalons OMS insérés.');
    }
}
