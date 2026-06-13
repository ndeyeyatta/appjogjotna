<template>
  <AppLayout>
    <div class="bg-gray-800 px-4 pt-4 pb-6">
      <button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button>
      <h1 class="text-white text-lg font-bold">{{ isEdit ? 'Modifier le profil' : 'Nouvel enfant' }}</h1>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Prénom *</label>
            <input v-model="form.prenom" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nom *</label>
            <input v-model="form.nom" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Date naissance *</label>
            <input v-model="form.date_naissance" type="date" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Sexe *</label>
            <select v-model="form.sexe" class="w-full border rounded-xl px-3 py-2.5 text-sm">
              <option value="M">Garçon</option>
              <option value="F">Fille</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Village *</label>
          <input v-model="form.village" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Parent / Tuteur *</label>
          <select v-model="form.tuteur_id" class="w-full border rounded-xl px-3 py-2.5 text-sm" :disabled="isEdit">
            <option value="">- Choisir -</option>
            <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.prenom }} {{ p.nom }}</option>
          </select>
        </div>
        <div v-if="isEdit">
          <label class="block text-xs font-semibold text-gray-600 mb-1">Statut</label>
          <select v-model="form.statut" class="w-full border rounded-xl px-3 py-2.5 text-sm">
            <option value="actif">Actif</option>
            <option value="pause">Pause</option>
            <option value="sorti">Sorti</option>
          </select>
        </div>
        <div v-else>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Date inscription *</label>
          <input v-model="form.date_inscription" type="date" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
          <textarea v-model="form.notes" rows="3" class="w-full border rounded-xl px-3 py-2.5 text-sm" />
        </div>
        <div v-if="erreur" class="bg-red-50 border border-red-300 text-red-700 text-sm rounded-xl p-3">{{ erreur }}</div>
        <button @click="enregistrer" :disabled="chargement"
          class="w-full bg-gray-800 text-white font-bold py-4 rounded-xl disabled:opacity-50">
          {{ chargement ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer le profil') }}
        </button>
      </div>
    </div>
    <BottomNav :active="1" role="encadreur" />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';

const props = defineProps({ id: { type: Number, default: null } });
const router = useRouter();
const isEdit = computed(() => !!props.id);
const parents = ref([]);
const chargement = ref(false);
const erreur = ref('');
const form = ref({
  nom: '', prenom: '', date_naissance: '', sexe: 'M', village: 'Diagambal',
  tuteur_id: '', date_inscription: new Date().toISOString().slice(0, 10),
  statut: 'actif', notes: '',
});

onMounted(async () => {
  try {
    const [pRes, paramRes] = await Promise.all([
      axios.get('/api/enfants-liste/parents'),
      axios.get('/api/parametres'),
    ]);
    parents.value = pRes.data;
    form.value.village = paramRes.data.village_defaut || 'Diagambal';
    if (props.id) {
      const { data } = await axios.get(`/api/enfants/${props.id}`);
      form.value = {
        nom: data.nom, prenom: data.prenom, date_naissance: data.date_naissance?.slice(0, 10),
        sexe: data.sexe, village: data.village, tuteur_id: data.tuteur_id,
        statut: data.statut, notes: data.notes || '',
      };
    }
  } catch {}
});

async function enregistrer() {
  erreur.value = '';
  chargement.value = true;
  try {
    if (isEdit.value) {
      await axios.put(`/api/enfants/${props.id}`, form.value);
      router.push(`/enfants/${props.id}`);
    } else {
      const { data } = await axios.post('/api/enfants', form.value);
      router.push(`/enfants/${data.id}`);
    }
  } catch (e) {
    erreur.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat().join(' ') || 'Erreur.';
  } finally { chargement.value = false; }
}
</script>
