<template>
  <AppLayout>
    <div class="bg-blue-800 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <div class="flex items-center justify-between">
        <h1 class="text-white text-lg font-bold">🔔 Notifications</h1>
        <button @click="toutLire" class="text-blue-200 text-xs underline">Tout marquer lu</button>
      </div>
    </div>

    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>

      <div v-else>
        <div v-for="n in notifications" :key="n.id"
          class="bg-white rounded-xl shadow-sm p-4 mb-3 flex items-start gap-3"
          :class="!n.lu ? 'border-l-4 border-blue-500' : ''">
          <span class="text-2xl flex-shrink-0">{{ typeIcone(n.type) }}</span>
          <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-800" :class="!n.lu ? 'font-semibold' : ''">{{ n.message }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatDate(n.date_envoi) }}</p>
          </div>
          <button v-if="!n.lu" @click="marquerLue(n.id)"
            class="text-blue-500 text-xs flex-shrink-0">✓ Lu</button>
        </div>

        <div v-if="!notifications.length" class="text-center text-gray-400 py-10">
          <div class="text-4xl mb-2">🔔</div>
          <p>Aucune notification</p>
        </div>
      </div>
    </div>
    <BottomNav :active="3" :role="authStore.user?.role" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore     = useAuthStore();
const notifications = ref([]);
const chargement    = ref(true);

onMounted(async () => {
  const { data } = await axios.get('/api/notifications');
  notifications.value = data.data || [];
  chargement.value = false;
});

async function marquerLue(id) {
  await axios.post(`/api/notifications/${id}/lue`);
  notifications.value = notifications.value.map(n => n.id === id ? { ...n, lu: true } : n);
}

async function toutLire() {
  await axios.post('/api/notifications/tout-lire');
  notifications.value = notifications.value.map(n => ({ ...n, lu: true }));
}

const typeIcone  = t => ({ alerte:'⚠', observation:'💬', seance:'📅', rapport:'📄', systeme:'⚙' }[t] || '🔔');
const formatDate = d => d ? new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'short', hour:'2-digit', minute:'2-digit' }) : ':';
</script>
