<template>
  <AppLayout>
    <div class="bg-blue-700 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 v-if="enfant" class="text-white text-lg font-bold">{{ enfant.prenom }} {{ enfant.nom }}</h1>
      <p v-if="enfant" class="text-blue-200 text-sm">{{ enfant.age_ans }} ans — {{ enfant.village }}</p>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else-if="enfant">
        <div v-if="enfant.alertes?.length" class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
          <p class="text-amber-800 text-sm font-semibold">⚠ {{ enfant.alertes.length }} alerte(s) active(s)</p>
        </div>
        <h2 class="font-bold text-gray-800 mb-3">Courbe de croissance</h2>
        <CourbeOMS v-if="enfant.mesures_nutritionnelles?.length"
          :mesures="enfant.mesures_nutritionnelles" :sexe="enfant.sexe" class="mb-4" />
        <h2 class="font-bold text-gray-800 mb-3">Dernières évaluations</h2>
        <div v-for="ev in enfant.evaluations?.slice(0, 5)" :key="ev.id"
          @click="$router.push(`/evaluations/${ev.id}/resultat`)"
          class="bg-white rounded-xl shadow-sm p-3 mb-2 flex justify-between items-center cursor-pointer">
          <div>
            <p class="text-sm font-semibold capitalize">{{ ev.dimension }}</p>
            <p class="text-xs text-gray-400">{{ formatDate(ev.date_eval) }}</p>
          </div>
          <span class="text-sm font-bold text-blue-700">{{ ev.score_global }}%</span>
        </div>
        <button @click="$router.push(`/evaluations/creer?enfant=${enfant.id}`)"
          class="w-full mt-4 bg-blue-600 text-white py-3 rounded-xl font-semibold text-sm">
          Nouvelle évaluation
        </button>
      </template>
    </div>
    <BottomNav :active="1" :role="authStore.user?.role" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import CourbeOMS from '@/components/CourbeOMS.vue';
import { useAuthStore } from '@/stores/authStore';

const props = defineProps({ id: { type: Number, required: true } });
const authStore = useAuthStore();
const enfant = ref(null);
const chargement = ref(true);

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/enfants/${props.id}`);
    enfant.value = data;
  } catch {}
  finally { chargement.value = false; }
});

const formatDate = d => d ? new Date(d).toLocaleDateString('fr-FR') : '—';
</script>
