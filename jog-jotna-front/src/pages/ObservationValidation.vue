<template>
  <AppLayout>
    <div class="bg-amber-600 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">📋 Signalements à valider</h1>
      <p class="text-amber-100 text-sm">{{ observations.length }} en attente</p>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <div v-for="o in observations" :key="o.id" class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <div class="flex items-start justify-between gap-2 mb-2">
          <div>
            <p class="font-bold text-sm">{{ o.enfant?.prenom }} {{ o.enfant?.nom }}</p>
            <p class="text-xs text-gray-500">{{ o.categorie }} — {{ o.parent?.prenom }} {{ o.parent?.nom }}</p>
            <p class="text-xs text-gray-400">{{ formatDate(o.date_obs) }}</p>
          </div>
          <span class="text-xs px-2 py-1 rounded-full font-semibold"
            :class="o.urgence === 'urgent' ? 'bg-red-100 text-red-700' : o.urgence === 'a_surveiller' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'">
            {{ o.urgence }}
          </span>
        </div>
        <p class="text-sm text-gray-700 mb-3">{{ o.description }}</p>
        <textarea v-model="commentaires[o.id]" rows="2" placeholder="Commentaire (obligatoire pour rejeter)"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-amber-400" />
        <div class="flex gap-2">
          <button @click="valider(o.id)" :disabled="actions[o.id]"
            class="flex-1 bg-green-600 text-white text-xs py-2.5 rounded-lg font-semibold disabled:opacity-50">
            ✓ Valider
          </button>
          <button @click="rejeter(o.id)" :disabled="actions[o.id]"
            class="flex-1 bg-red-50 border border-red-300 text-red-700 text-xs py-2.5 rounded-lg font-semibold disabled:opacity-50">
            ✗ Rejeter
          </button>
        </div>
      </div>
      <div v-if="!chargement && !observations.length" class="text-center text-gray-400 py-10">
        <div class="text-4xl mb-2">✓</div>
        <p>Aucun signalement en attente</p>
      </div>
    </div>
    <BottomNav :active="2" role="encadreur" />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';

const observations = ref([]);
const chargement = ref(true);
const commentaires = reactive({});
const actions = reactive({});

async function charger() {
  chargement.value = true;
  try {
    const { data } = await axios.get('/api/observations/en-attente');
    observations.value = data;
  } catch {}
  finally { chargement.value = false; }
}

async function valider(id) {
  actions[id] = true;
  try {
    await axios.post(`/api/observations/${id}/valider`, { commentaire: commentaires[id] || '' });
    observations.value = observations.value.filter(o => o.id !== id);
  } catch {}
  finally { actions[id] = false; }
}

async function rejeter(id) {
  if (!commentaires[id]?.trim()) {
    alert('Veuillez saisir un commentaire pour rejeter.');
    return;
  }
  actions[id] = true;
  try {
    await axios.post(`/api/observations/${id}/rejeter`, { commentaire: commentaires[id] });
    observations.value = observations.value.filter(o => o.id !== id);
  } catch {}
  finally { actions[id] = false; }
}

const formatDate = d => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }) : '—';

onMounted(charger);
</script>
