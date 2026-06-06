<template>
  <AppLayout>
    <div class="bg-red-700 px-4 pt-4 pb-6">
      <h1 class="text-white text-lg font-bold">⚠ Alertes actives</h1>
      <p class="text-red-200 text-sm">{{ alertes.length }} alerte(s)</p>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <div v-for="a in alertes" :key="a.id"
        class="bg-white rounded-xl shadow-sm p-4 mb-3"
        :class="a.niveau === 'urgent' ? 'border-l-4 border-red-500' : 'border-l-4 border-amber-400'">
        <div class="flex items-start justify-between gap-2">
          <div>
            <p class="font-bold text-sm">{{ a.enfant?.prenom }} {{ a.enfant?.nom }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ a.type_alerte }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatDate(a.created_at) }}</p>
          </div>
          <BadgeAlerte :niveau="a.niveau" />
        </div>
        <div class="flex gap-2 mt-3">
          <button @click="prendreEnCharge(a.id)" class="flex-1 bg-blue-600 text-white text-xs py-2 rounded-lg font-semibold">
            Prendre en charge
          </button>
          <button @click="cloturer(a.id)" class="flex-1 bg-gray-100 text-gray-700 text-xs py-2 rounded-lg font-semibold">
            Clôturer
          </button>
        </div>
      </div>
      <div v-if="!chargement && !alertes.length" class="text-center text-gray-400 py-8">Aucune alerte active</div>
    </div>
    <BottomNav :active="2" :role="authStore.user?.role" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import BadgeAlerte from '@/components/BadgeAlerte.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const alertes = ref([]);
const chargement = ref(true);

async function charger() {
  chargement.value = true;
  try {
    const { data } = await axios.get('/api/alertes');
    alertes.value = data.data || data;
  } catch {}
  finally { chargement.value = false; }
}

async function prendreEnCharge(id) {
  await axios.post(`/api/alertes/${id}/prendre-en-charge`);
  await charger();
}

async function cloturer(id) {
  await axios.post(`/api/alertes/${id}/cloturer`);
  await charger();
}

const formatDate = d => d ? new Date(d).toLocaleDateString('fr-FR') : '—';

onMounted(charger);
</script>
