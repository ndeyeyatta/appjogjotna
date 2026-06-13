<template>
  <AppLayout>
    <div class="bg-gray-800 px-4 pt-4 pb-6">
      <div class="flex items-center justify-between">
        <h1 class="text-white text-lg font-bold">👶 Liste des enfants</h1>
        <button v-if="peutGerer" @click="$router.push('/enfants/creer')"
          class="bg-blue-600 text-white text-xs px-3 py-2 rounded-lg font-semibold">+ Nouveau</button>
      </div>
      <input v-model="search" type="search" placeholder="Rechercher..."
        class="mt-3 w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <div v-for="e in enfants" :key="e.id" class="mb-3">
        <CarteEnfant :enfant="e" />
      </div>
      <div v-if="!chargement && !enfants.length" class="text-center text-gray-400 py-8">Aucun enfant trouvé</div>
    </div>
    <BottomNav :active="1" :role="navRole" />
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import CarteEnfant from '@/components/CarteEnfant.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const enfants = ref([]);
const search = ref('');
const chargement = ref(true);

const peutGerer = computed(() => ['encadreur', 'responsable', 'admin'].includes(authStore.user?.role));
const navRole = computed(() => {
  if (authStore.user?.role === 'parent') return 'parent';
  if (authStore.user?.role === 'admin') return 'admin';
  return 'encadreur';
});

async function charger() {
  chargement.value = true;
  try {
    const { data } = await axios.get('/api/enfants', { params: { search: search.value || undefined } });
    enfants.value = data.data || data;
  } catch {}
  finally { chargement.value = false; }
}

onMounted(charger);
watch(search, () => { clearTimeout(charger._t); charger._t = setTimeout(charger, 400); });
</script>
