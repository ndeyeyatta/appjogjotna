<template>
  <AppLayout>
    <div class="bg-violet-700 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">📅 Planifier une séance</h1>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Date *</label>
          <input v-model="form.date_seance" type="date" required
            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400" />
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Heure *</label>
          <input v-model="form.heure" type="time" required
            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400" />
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Lieu</label>
          <input v-model="form.lieu" type="text" placeholder="Centre JÒG JOTNA"
            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400" />
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Enfants participants *</label>
          <div v-for="e in enfants" :key="e.id" class="flex items-center gap-2 mb-2">
            <input :id="'e'+e.id" v-model="form.enfant_ids" type="checkbox" :value="e.id" class="rounded" />
            <label :for="'e'+e.id" class="text-sm text-gray-700">{{ e.prenom }} {{ e.nom }}</label>
          </div>
        </div>
        <div v-if="erreur" class="bg-red-50 border border-red-300 text-red-700 text-sm rounded-xl p-3">{{ erreur }}</div>
        <button @click="envoyer" :disabled="chargement || !form.date_seance || !form.heure || !form.enfant_ids.length"
          class="w-full bg-violet-700 text-white font-bold py-4 rounded-xl disabled:opacity-50">
          {{ chargement ? 'Création...' : 'Créer la séance' }}
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

const router = useRouter();
const enfants = ref([]);
const chargement = ref(false);
const erreur = ref('');
const form = ref({ date_seance: '', heure: '', lieu: '', enfant_ids: [] });

onMounted(async () => {
  try {
    const [eRes, pRes] = await Promise.all([
      axios.get('/api/enfants'),
      axios.get('/api/parametres'),
    ]);
    enfants.value = eRes.data.data || eRes.data;
    form.value.lieu = pRes.data.lieu_seance_defaut || '';
  } catch {}
});

async function envoyer() {
  erreur.value = '';
  chargement.value = true;
  try {
    await axios.post('/api/seances', form.value);
    router.push('/dashboard/encadreur');
  } catch (e) {
    erreur.value = e.response?.data?.message || 'Erreur lors de la création.';
  } finally { chargement.value = false; }
}
</script>
