<template>
  <AppLayout :nav-active="2" nav-role="admin">
    <div class="bg-violet-900 page-header">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">⚙ Configuration système</h1>
    </div>
    <div class="page-body max-w-3xl">
      <div class="bg-white rounded-xl shadow-sm p-4 mb-4 space-y-3">
        <h2 class="font-bold text-gray-800 text-sm">Paramètres généraux</h2>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Nom du programme</label>
          <input v-model="parametres.nom_programme" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Village par défaut</label>
          <input v-model="parametres.village_defaut" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Lieu séance par défaut</label>
          <input v-model="parametres.lieu_seance_defaut" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div class="grid-form">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Seuil alerte (%)</label>
            <input v-model.number="parametres.score_seuil_alerte_defaut" type="number" min="0" max="100"
              class="w-full border rounded-lg px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Seuil urgent (%)</label>
            <input v-model.number="parametres.score_seuil_urgent_defaut" type="number" min="0" max="100"
              class="w-full border rounded-lg px-3 py-2 text-sm" />
          </div>
        </div>
        <button @click="sauverParametres" :disabled="savingParams"
          class="w-full bg-violet-700 text-white py-3 rounded-lg font-semibold text-sm disabled:opacity-50">
          {{ savingParams ? 'Enregistrement...' : 'Enregistrer les paramètres' }}
        </button>
      </div>

      <h2 class="font-bold text-gray-800 mb-3">Indicateurs OMS (seuils)</h2>
      <div v-if="chargement" class="text-center py-8 text-gray-400">Chargement...</div>
      <div v-for="ind in indicateurs" :key="ind.id" class="bg-white rounded-xl shadow-sm p-3 mb-2">
        <p class="text-xs text-gray-500 capitalize">{{ ind.dimension }} — {{ ind.tranche_age_min }}-{{ ind.tranche_age_max }} mois</p>
        <p class="text-sm font-semibold text-gray-800 mb-2">{{ ind.libelle }}</p>
        <div class="grid grid-cols-2 gap-2">
          <input v-model.number="ind.score_seuil_alerte" type="number" placeholder="Seuil alerte"
            class="border rounded-lg px-2 py-1.5 text-xs" @change="sauverIndicateur(ind)" />
          <input v-model.number="ind.score_seuil_urgent" type="number" placeholder="Seuil urgent"
            class="border rounded-lg px-2 py-1.5 text-xs" @change="sauverIndicateur(ind)" />
        </div>
      </div>
      <div v-if="msg" class="mt-3 text-green-600 text-sm">{{ msg }}</div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';

const parametres = ref({});
const indicateurs = ref([]);
const chargement = ref(true);
const savingParams = ref(false);
const msg = ref('');

onMounted(async () => {
  try {
    const [p, i] = await Promise.all([axios.get('/api/parametres'), axios.get('/api/indicateurs')]);
    parametres.value = p.data;
    indicateurs.value = i.data;
  } catch {}
  finally { chargement.value = false; }
});

async function sauverParametres() {
  savingParams.value = true;
  msg.value = '';
  try {
    await axios.put('/api/parametres', parametres.value);
    msg.value = 'Paramètres enregistrés.';
  } catch {}
  finally { savingParams.value = false; }
}

async function sauverIndicateur(ind) {
  try {
    await axios.put(`/api/indicateurs/${ind.id}`, {
      score_seuil_alerte: ind.score_seuil_alerte,
      score_seuil_urgent: ind.score_seuil_urgent,
    });
    msg.value = `Indicateur « ${ind.libelle} » mis à jour.`;
  } catch {}
}
</script>
