<template>
  <AppLayout :nav-active="2" nav-role="encadreur">
    <div class="bg-blue-700 page-header">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">Nouvelle évaluation</h1>
      <p v-if="enfant" class="text-blue-200 text-sm">{{ enfant.nom_complet }} : {{ enfant.age_ans }}</p>
    </div>
    <div class="page-body form-container">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else>
        <div v-if="!enfantId" class="mb-4">
          <label class="block text-sm font-bold text-gray-700 mb-2">Enfant *</label>
          <select v-model="enfantSelectionne" @change="chargerGrille" class="w-full border-2 border-blue-400 rounded-xl px-4 py-3 text-sm">
            <option value="">: Choisir un enfant :</option>
            <option v-for="e in enfantsListe" :key="e.id" :value="e.id">{{ e.prenom }} {{ e.nom }} : {{ e.village }}</option>
          </select>
        </div>
        <template v-if="enfant">
          <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Dimension *</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
              <button v-for="d in dimensions" :key="d.val" @click="dimension = d.val"
                class="py-3 rounded-xl border-2 text-sm font-semibold transition"
                :class="dimension === d.val ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500'">
                {{ d.label }}
              </button>
            </div>
          </div>
          <div v-if="dimension && indicateurs.length">
            <h2 class="font-bold text-gray-800 mb-3">Indicateurs</h2>
            <div v-for="ind in indicateurs" :key="ind.id" class="bg-white rounded-xl shadow-sm p-3 mb-2">
              <p class="text-sm text-gray-700 mb-2">{{ ind.libelle }}</p>
              <div class="flex gap-2">
                <button v-for="v in valeurs" :key="v.val" @click="scores[ind.id] = v.val"
                  class="flex-1 py-2 rounded-lg text-xs font-semibold border-2 transition"
                  :class="scores[ind.id] === v.val ? v.actif : 'border-gray-200 text-gray-400'">
                  {{ v.label }}
                </button>
              </div>
            </div>
          </div>
          <textarea v-model="commentaire" rows="3" placeholder="Commentaire (optionnel)"
            class="w-full mt-4 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <div v-if="erreur" class="mt-3 bg-red-50 border border-red-300 text-red-700 text-sm rounded-xl p-3">{{ erreur }}</div>
          <button @click="envoyer" :disabled="soumission || !peutEnvoyer"
            class="w-full mt-4 bg-blue-600 text-white font-bold py-4 rounded-xl disabled:opacity-50">
            {{ soumission ? 'Enregistrement...' : 'Enregistrer l\'évaluation' }}
          </button>
        </template>
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({ enfantId: { type: Number, default: null } });
const router = useRouter();

const enfant = ref(null);
const enfantsListe = ref([]);
const enfantSelectionne = ref('');
const indicateursData = ref({});
const dimension = ref('');
const scores = ref({});
const commentaire = ref('');
const chargement = ref(true);
const soumission = ref(false);
const erreur = ref('');

const dimensions = [
  { val: 'cognitif', label: 'Cognitif' },
  { val: 'moteur', label: 'Moteur' },
  { val: 'socio_emotionnel', label: 'Socio-émotionnel' },
  { val: 'nutritionnel', label: 'Nutritionnel' },
];
const valeurs = [
  { val: 0, label: 'Non acquis', actif: 'border-red-500 bg-red-50 text-red-700' },
  { val: 1, label: 'En cours', actif: 'border-amber-500 bg-amber-50 text-amber-700' },
  { val: 2, label: 'Acquis', actif: 'border-green-500 bg-green-50 text-green-700' },
];

const enfantActifId = computed(() => props.enfantId || (enfantSelectionne.value ? Number(enfantSelectionne.value) : null));
const indicateurs = computed(() => indicateursData.value[dimension.value] || []);
const peutEnvoyer = computed(() => {
  if (!enfantActifId.value || !dimension.value) return false;
  return indicateurs.value.length > 0 && indicateurs.value.every(ind => scores.value[ind.id] !== undefined);
});

watch(dimension, () => { scores.value = {}; });

async function chargerGrille() {
  const id = enfantActifId.value;
  if (!id) { enfant.value = null; return; }
  chargement.value = true;
  erreur.value = '';
  try {
    const { data } = await axios.get(`/api/evaluations/create/${id}`);
    enfant.value = data.enfant;
    indicateursData.value = data.indicateurs;
    dimension.value = '';
    scores.value = {};
  } catch {
    erreur.value = 'Impossible de charger la grille d\'évaluation.';
  } finally { chargement.value = false; }
}

onMounted(async () => {
  if (props.enfantId) {
    await chargerGrille();
  } else {
    try {
      const { data } = await axios.get('/api/enfants');
      enfantsListe.value = data.data || data;
    } catch {}
    chargement.value = false;
  }
});

async function envoyer() {
  erreur.value = '';
  soumission.value = true;
  const scoresObj = {};
  indicateurs.value.forEach(ind => { scoresObj[ind.id] = scores.value[ind.id]; });
  try {
    const { data } = await axios.post(`/api/evaluations/${enfantActifId.value}`, {
      dimension: dimension.value,
      scores: scoresObj,
      commentaire: commentaire.value || null,
    });
    router.push(`/evaluations/${data.evaluation.id}/resultat`);
  } catch (e) {
    erreur.value = e.response?.data?.message || 'Erreur lors de l\'enregistrement.';
  } finally { soumission.value = false; }
}
</script>
