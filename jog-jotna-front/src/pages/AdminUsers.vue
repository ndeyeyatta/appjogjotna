<template>
  <AppLayout>
    <div class="bg-violet-900 px-4 pt-4 pb-6">
      <h1 class="text-white text-lg font-bold">👥 Gestion des comptes</h1>
      <p class="text-violet-200 text-sm">Administration des utilisateurs</p>
    </div>
    <div class="px-4 pt-4 pb-24">
      <button @click="afficherForm = !afficherForm"
        class="w-full bg-violet-700 text-white font-semibold py-3 rounded-xl mb-4 text-sm">
        {{ afficherForm ? 'Annuler' : '+ Nouveau compte' }}
      </button>

      <div v-if="afficherForm" class="bg-white rounded-xl shadow-sm p-4 mb-4 space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <input v-model="form.prenom" placeholder="Prénom *" class="border rounded-lg px-3 py-2 text-sm" />
          <input v-model="form.nom" placeholder="Nom *" class="border rounded-lg px-3 py-2 text-sm" />
        </div>
        <input v-model="form.email" type="email" placeholder="Email *" class="w-full border rounded-lg px-3 py-2 text-sm" />
        <input v-model="form.telephone" placeholder="Téléphone" class="w-full border rounded-lg px-3 py-2 text-sm" />
        <input v-model="form.mot_de_passe" type="password" placeholder="Mot de passe * (min 6)" class="w-full border rounded-lg px-3 py-2 text-sm" />
        <div class="grid grid-cols-2 gap-3">
          <select v-model="form.role" class="border rounded-lg px-3 py-2 text-sm">
            <option value="parent">Parent</option>
            <option value="encadreur">Encadreur</option>
            <option value="responsable">Responsable</option>
            <option value="admin">Admin</option>
          </select>
          <select v-model="form.langue" class="border rounded-lg px-3 py-2 text-sm">
            <option value="fr">Français</option>
            <option value="wo">Wolof</option>
          </select>
        </div>
        <div v-if="formErreur" class="text-red-600 text-xs">{{ formErreur }}</div>
        <button @click="creer" :disabled="formChargement" class="w-full bg-violet-600 text-white py-3 rounded-lg font-semibold text-sm disabled:opacity-50">
          {{ formChargement ? 'Création...' : 'Créer le compte' }}
        </button>
      </div>

      <input v-model="search" type="search" placeholder="Rechercher..."
        class="w-full border rounded-xl px-4 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-violet-400" />

      <div v-if="chargement" class="text-center py-8 text-gray-400">Chargement...</div>
      <div v-for="u in utilisateurs" :key="u.id" class="bg-white rounded-xl shadow-sm p-4 mb-3">
        <div class="flex items-start justify-between">
          <div>
            <p class="font-bold text-sm">{{ u.prenom }} {{ u.nom }}</p>
            <p class="text-xs text-gray-500">{{ u.email }}</p>
            <div class="flex gap-2 mt-2">
              <span class="text-xs px-2 py-0.5 rounded-full font-semibold" :class="roleBadge(u.role)">{{ u.role }}</span>
              <span class="text-xs px-2 py-0.5 rounded-full" :class="u.actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                {{ u.actif ? 'Actif' : 'Inactif' }}
              </span>
            </div>
          </div>
          <button v-if="u.actif && u.id !== authStore.user?.id" @click="desactiver(u.id)"
            class="text-xs text-red-600 font-semibold px-2 py-1">Désactiver</button>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3 mt-2">
        <button @click="$router.push('/admin/config')"
          class="bg-violet-600 text-white rounded-xl p-3 text-center text-sm font-semibold">⚙ Configuration</button>
        <button @click="$router.push('/admin/sauvegarde')"
          class="bg-violet-800 text-white rounded-xl p-3 text-center text-sm font-semibold">💾 Sauvegarde</button>
      </div>
    </div>
    <BottomNav :active="1" role="admin" />
  </AppLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const utilisateurs = ref([]);
const chargement = ref(true);
const search = ref('');
const afficherForm = ref(false);
const formChargement = ref(false);
const formErreur = ref('');
const form = ref({ nom: '', prenom: '', email: '', telephone: '', mot_de_passe: '', role: 'parent', langue: 'fr' });

const roleBadge = r => ({
  parent: 'bg-teal-100 text-teal-700',
  encadreur: 'bg-blue-100 text-blue-700',
  responsable: 'bg-red-100 text-red-700',
  admin: 'bg-violet-100 text-violet-700',
}[r] || 'bg-gray-100 text-gray-500');

async function charger() {
  chargement.value = true;
  try {
    const { data } = await axios.get('/api/utilisateurs', { params: { search: search.value || undefined } });
    utilisateurs.value = data.data || data;
  } catch {}
  finally { chargement.value = false; }
}

async function creer() {
  formErreur.value = '';
  formChargement.value = true;
  try {
    await axios.post('/api/utilisateurs', form.value);
    form.value = { nom: '', prenom: '', email: '', telephone: '', mot_de_passe: '', role: 'parent', langue: 'fr' };
    afficherForm.value = false;
    await charger();
  } catch (e) {
    formErreur.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat().join(' ') || 'Erreur.';
  } finally { formChargement.value = false; }
}

async function desactiver(id) {
  if (!confirm('Désactiver ce compte ?')) return;
  await axios.delete(`/api/utilisateurs/${id}`);
  await charger();
}

onMounted(charger);
watch(search, () => { clearTimeout(charger._t); charger._t = setTimeout(charger, 400); });
</script>
