<template>
  <AppLayout>
    <div class="bg-violet-900 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">💾 Sauvegarde des données</h1>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else>
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
          <h2 class="font-bold text-gray-800 mb-3">État de la base</h2>
          <div class="grid grid-cols-2 gap-3 text-center">
            <div v-for="(val, key) in statsLabels" :key="key" class="bg-gray-50 rounded-lg p-3">
              <div class="text-xl font-bold text-violet-700">{{ stats[key] ?? 0 }}</div>
              <div class="text-xs text-gray-500">{{ val }}</div>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-3 text-center">Taille estimée : ~{{ stats.taille_estimee_ko }} Ko</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
          <p class="text-amber-800 text-sm">La sauvegarde exporte toutes les données au format JSON (utilisateurs, enfants, évaluations, alertes, etc.).</p>
        </div>
        <button @click="exporter" :disabled="exporting"
          class="w-full bg-violet-700 text-white font-bold py-4 rounded-xl disabled:opacity-50">
          {{ exporting ? 'Export en cours...' : '📥 Télécharger la sauvegarde JSON' }}
        </button>
        <p v-if="succes" class="text-green-600 text-sm text-center mt-3">{{ succes }}</p>
        <p v-if="erreur" class="text-red-600 text-sm text-center mt-3">{{ erreur }}</p>
      </template>
    </div>
    <BottomNav :active="3" role="admin" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';

const stats = ref({});
const chargement = ref(true);
const exporting = ref(false);
const succes = ref('');
const erreur = ref('');

const statsLabels = {
  utilisateurs: 'Utilisateurs',
  enfants: 'Enfants',
  evaluations: 'Évaluations',
  observations: 'Observations',
  alertes: 'Alertes',
  seances: 'Séances',
  rapports: 'Rapports',
};

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/admin/backup/stats');
    stats.value = data;
  } catch {}
  finally { chargement.value = false; }
});

async function exporter() {
  exporting.value = true;
  succes.value = '';
  erreur.value = '';
  try {
    const { data } = await axios.get('/api/admin/backup/export');
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `jog-jotna-backup-${new Date().toISOString().slice(0, 10)}.json`;
    a.click();
    URL.revokeObjectURL(url);
    succes.value = 'Sauvegarde téléchargée avec succès.';
  } catch {
    erreur.value = 'Erreur lors de l\'export.';
  } finally { exporting.value = false; }
}
</script>
