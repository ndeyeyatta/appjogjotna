<template>
  <AppLayout>
    <div class="bg-green-700 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">Mesure nutritionnelle</h1>
      <p v-if="enfant" class="text-green-200 text-sm">{{ enfant.prenom }} {{ enfant.nom }}</p>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Date de mesure</label>
          <input v-model="form.date_mesure" type="date" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Poids (kg) *</label>
            <input v-model.number="form.poids" type="number" step="0.1" min="5" max="50"
              class="w-full border rounded-xl px-3 py-2.5 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Taille (cm) *</label>
            <input v-model.number="form.taille" type="number" step="0.1" min="50" max="150"
              class="w-full border rounded-xl px-3 py-2.5 text-sm" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Périmètre brachial (mm)</label>
          <input v-model.number="form.perimetre_brachial" type="number" step="1"
            class="w-full border rounded-xl px-3 py-2.5 text-sm" />
        </div>
        <div v-if="resultat" class="bg-white rounded-xl shadow-sm p-4">
          <p class="text-sm font-bold text-gray-800 mb-2">Résultat</p>
          <p class="text-sm">Z-score : <strong>{{ resultat.z_score }}</strong></p>
          <p class="text-sm">Statut : <strong>{{ statutLabel(resultat.statut) }}</strong></p>
          <p v-if="resultat.alerte" class="text-amber-700 text-xs mt-2">⚠ Une alerte nutritionnelle a été générée.</p>
        </div>
        <div v-if="erreur" class="bg-red-50 border border-red-300 text-red-700 text-sm rounded-xl p-3">{{ erreur }}</div>
        <button @click="enregistrer" :disabled="chargement || !form.poids || !form.taille"
          class="w-full bg-green-700 text-white font-bold py-4 rounded-xl disabled:opacity-50">
          {{ chargement ? 'Enregistrement...' : 'Enregistrer la mesure' }}
        </button>
      </div>
    </div>
    <BottomNav :active="1" role="encadreur" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';

const props = defineProps({ id: { type: Number, required: true } });
const router = useRouter();
const enfant = ref(null);
const chargement = ref(false);
const erreur = ref('');
const resultat = ref(null);
const form = ref({
  date_mesure: new Date().toISOString().slice(0, 10),
  poids: null, taille: null, perimetre_brachial: null,
});

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/enfants/${props.id}`);
    enfant.value = data;
  } catch {}
});

const statutLabel = s => ({ normal: 'Normal', malnutrition_moderee: 'Malnutrition modérée', malnutrition_severe: 'Malnutrition sévère' }[s] || s);

async function enregistrer() {
  erreur.value = '';
  chargement.value = true;
  try {
    const { data } = await axios.post(`/api/mesures/${props.id}`, form.value);
    resultat.value = data;
    setTimeout(() => router.push(`/enfants/${props.id}`), 1500);
  } catch (e) {
    erreur.value = e.response?.data?.message || 'Erreur lors de l\'enregistrement.';
  } finally { chargement.value = false; }
}
</script>
