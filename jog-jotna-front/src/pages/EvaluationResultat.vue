<template>
  <AppLayout>
    <div class="bg-blue-700 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">Résultat de l'évaluation</h1>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else-if="evaluation">

        <!-- En-tête -->
        <div class="bg-blue-50 rounded-xl p-4 mb-4 text-center">
          <p class="font-bold text-blue-800 text-lg">{{ evaluation.enfant?.prenom }} {{ evaluation.enfant?.nom }}</p>
          <p class="text-blue-600 text-sm">{{ dimensionLabel(evaluation.dimension) }} — {{ formatDate(evaluation.date_eval) }}</p>
          <div class="mt-3">
            <span class="text-4xl font-bold" :class="couleurScore(evaluation.score_global)">
              {{ evaluation.score_global }}%
            </span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-3 mt-2">
            <div class="h-3 rounded-full" :class="barreScore(evaluation.score_global)"
              :style="`width:${evaluation.score_global}%`"></div>
          </div>
          <span v-if="evaluation.urgent" class="mt-2 inline-block bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-semibold">⚠ Retard urgent détecté</span>
          <span v-else-if="evaluation.retard_detecte" class="mt-2 inline-block bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full font-semibold">⚠ Retard modéré détecté</span>
          <span v-else class="mt-2 inline-block bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-semibold">✓ Développement normal</span>
        </div>

        <!-- Scores par indicateur -->
        <h2 class="font-bold text-gray-800 mb-3">Détail par indicateur</h2>
        <div v-for="s in evaluation.scores_indicateurs" :key="s.id"
          class="bg-white rounded-xl shadow-sm p-3 mb-2 flex items-center justify-between">
          <p class="text-sm text-gray-700 flex-1 mr-3">{{ s.indicateur?.libelle }}</p>
          <span :class="valeurBadge(s.valeur)" class="text-xs px-2 py-1 rounded-full font-semibold flex-shrink-0">
            {{ valeurLabel(s.valeur) }}
          </span>
        </div>

        <!-- Commentaire -->
        <div v-if="evaluation.commentaire" class="bg-white rounded-xl shadow-sm p-4 mt-4">
          <p class="text-sm font-bold text-gray-700 mb-1">💬 Commentaire</p>
          <p class="text-sm text-gray-600">{{ evaluation.commentaire }}</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 mt-6">
          <button @click="$router.push(`/enfants/${evaluation.enfant_id}`)"
            class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold text-sm">
            Voir dossier enfant
          </button>
          <button @click="$router.push('/evaluations/creer?enfant='+evaluation.enfant_id)"
            class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold text-sm">
            Nouvelle évaluation
          </button>
        </div>
      </template>
    </div>
    <BottomNav :active="2" role="encadreur" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';

const props = defineProps({ id: { type: Number, required: true } });
const evaluation = ref(null);
const chargement = ref(true);

onMounted(async () => {
  const { data } = await axios.get(`/api/evaluations/${props.id}`);
  evaluation.value = data;
  chargement.value = false;
});

const dimensionLabel = d => ({ cognitif:'Cognitif', moteur:'Moteur', socio_emotionnel:'Socio-émotionnel', nutritionnel:'Nutritionnel' }[d] || d);
const valeurLabel    = v => ({ 0:'Non acquis', 1:'En cours', 2:'Acquis' }[v] || '?');
const valeurBadge    = v => ({ 0:'bg-red-100 text-red-700', 1:'bg-amber-100 text-amber-700', 2:'bg-green-100 text-green-700' }[v] || '');
const couleurScore   = s => s >= 75 ? 'text-green-600' : s >= 60 ? 'text-amber-600' : 'text-red-600';
const barreScore     = s => s >= 75 ? 'bg-green-500' : s >= 60 ? 'bg-amber-500' : 'bg-red-500';
const formatDate     = d => d ? new Date(d).toLocaleDateString('fr-FR') : '—';
</script>
