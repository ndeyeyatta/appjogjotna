<template>
  <div @click="$router.push(`/enfants/${enfant.id}`)"
    class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 cursor-pointer active:bg-gray-50">
    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
      :class="enfant.sexe==='M' ? 'bg-blue-100 border-2 border-blue-400' : 'bg-pink-100 border-2 border-pink-400'">
      <span class="font-bold text-sm" :class="enfant.sexe==='M' ? 'text-blue-700' : 'text-pink-700'">
        {{ initiales }}
      </span>
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2">
        <p class="font-bold text-gray-800 truncate">{{ enfant.prenom }} {{ enfant.nom }}</p>
        <BadgeAlerte v-if="enAlerte" :niveau="niveauAlerte" />
      </div>
      <p class="text-xs text-gray-400">{{ enfant.age_ans }} — {{ enfant.village }}</p>
      <div v-if="enfant.score_global" class="mt-1 flex items-center gap-2">
        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
          <div class="h-1.5 rounded-full" :class="barreScore(enfant.score_global)"
            :style="`width:${enfant.score_global}%`"></div>
        </div>
        <span class="text-xs font-semibold" :class="couleurScore(enfant.score_global)">
          {{ enfant.score_global }}%
        </span>
      </div>
    </div>
    <span class="text-gray-300 text-lg flex-shrink-0">›</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import BadgeAlerte from './BadgeAlerte.vue';
const props   = defineProps({ enfant: { type: Object, required: true } });
const initiales = computed(() => (props.enfant.prenom[0] + props.enfant.nom[0]).toUpperCase());
const enAlerte  = computed(() => props.enfant.alertes?.length > 0);
const niveauAlerte = computed(() => props.enfant.alertes?.[0]?.niveau || 'informatif');
const couleurScore = s => s >= 75 ? 'text-green-600' : s >= 60 ? 'text-amber-600' : 'text-red-600';
const barreScore   = s => s >= 75 ? 'bg-green-500' : s >= 60 ? 'bg-amber-500' : 'bg-red-500';
</script>
