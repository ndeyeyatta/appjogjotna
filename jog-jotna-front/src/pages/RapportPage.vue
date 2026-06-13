<template>
  <AppLayout>
    <div class="bg-red-800 px-4 pt-4 pb-6">
      <h1 class="text-white text-lg font-bold">📊 Rapports</h1>
      <p class="text-red-200 text-sm">Génération PDF et Excel</p>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div class="bg-white rounded-xl shadow-sm p-4 mb-6 space-y-3">
        <h2 class="font-bold text-gray-800 text-sm">Nouveau rapport</h2>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Titre (optionnel)</label>
          <input v-model="form.titre" type="text" placeholder="Rapport mensuel JÒG JOTNA"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Début *</label>
            <input v-model="form.periode_debut" type="date" required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Fin *</label>
            <input v-model="form.periode_fin" type="date" required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <button @click="generer('pdf')" :disabled="chargement || !form.periode_debut || !form.periode_fin"
            class="bg-red-700 text-white font-bold py-3 rounded-xl disabled:opacity-50 text-sm">
            {{ chargement === 'pdf' ? '...' : '📄 PDF' }}
          </button>
          <button @click="generer('excel')" :disabled="chargement || !form.periode_debut || !form.periode_fin"
            class="bg-green-700 text-white font-bold py-3 rounded-xl disabled:opacity-50 text-sm">
            {{ chargement === 'excel' ? '...' : '📊 Excel' }}
          </button>
        </div>
      </div>
      <div v-if="erreur" class="bg-red-50 border border-red-300 text-red-700 text-sm rounded-xl p-3 mb-4">{{ erreur }}</div>
      <div v-if="succes" class="bg-green-50 border border-green-300 text-green-700 text-sm rounded-xl p-3 mb-4">{{ succes }}</div>
      <h2 class="font-bold text-gray-800 mb-3">Rapports récents</h2>
      <div v-if="listeChargement" class="text-center py-6 text-gray-400">Chargement...</div>
      <div v-for="r in rapports" :key="r.id" class="bg-white rounded-xl shadow-sm p-4 mb-3 flex justify-between items-center">
        <div>
          <p class="text-sm font-semibold">{{ r.titre || 'Rapport' }}</p>
          <p class="text-xs text-gray-400">{{ formatDate(r.date_generation || r.created_at) }} : {{ r.format }}</p>
        </div>
        <button @click="telecharger(r.id)" class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded-lg font-semibold">
          Télécharger
        </button>
      </div>
      <div v-if="!listeChargement && !rapports.length" class="text-center text-gray-400 py-8">Aucun rapport disponible</div>
    </div>
    <BottomNav :active="1" :role="authStore.user?.role === 'admin' ? 'admin' : 'responsable'" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const rapports = ref([]);
const chargement = ref(false);
const listeChargement = ref(true);
const erreur = ref('');
const succes = ref('');

const debutMois = () => new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
const finMois = () => new Date().toISOString().slice(0, 10);
const form = ref({ titre: '', periode_debut: debutMois(), periode_fin: finMois() });

async function charger() {
  listeChargement.value = true;
  try {
    const { data } = await axios.get('/api/rapports');
    rapports.value = data.data || data;
  } catch {}
  finally { listeChargement.value = false; }
}

async function generer(type) {
  erreur.value = '';
  succes.value = '';
  chargement.value = type;
  try {
    const url = type === 'pdf' ? '/api/rapports/pdf' : '/api/rapports/excel';
    const { data } = await axios.post(url, form.value);
    succes.value = data.message || 'Rapport généré avec succès.';
    await charger();
  } catch (e) {
    const msg = e.response?.data?.message;
    const errs = e.response?.data?.errors;
    erreur.value = msg || (errs ? Object.values(errs).flat().join(' ') : 'Erreur lors de la génération.');
  } finally { chargement.value = false; }
}

async function telecharger(id) {
  try {
    const { data } = await axios.get(`/api/rapports/${id}/download`, { responseType: 'blob' });
    const url = URL.createObjectURL(data);
    const a = document.createElement('a');
    a.href = url;
    a.download = `rapport-${id}`;
    a.click();
    URL.revokeObjectURL(url);
  } catch {
    erreur.value = 'Erreur lors du téléchargement.';
  }
}

const formatDate = d => d ? new Date(d).toLocaleDateString('fr-FR') : ':';

onMounted(charger);
</script>
