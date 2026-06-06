<template>
  <AppLayout>
    <div class="bg-blue-800 px-4 pt-4 pb-10">
      <h1 class="text-white text-lg font-bold">👤 Mon profil</h1>
    </div>

    <div class="px-4 -mt-4 pb-24">
      <!-- Carte profil -->
      <div class="bg-white rounded-2xl shadow-sm p-5 mb-4 text-center">
        <div class="w-20 h-20 rounded-full bg-blue-100 border-4 border-blue-500 flex items-center justify-center mx-auto mb-3">
          <span class="text-blue-700 font-bold text-2xl">{{ initiales }}</span>
        </div>
        <h2 class="text-xl font-bold text-gray-800">{{ authStore.nomComplet }}</h2>
        <p class="text-gray-400 text-sm">{{ authStore.user?.email }}</p>
        <span :class="roleBadge" class="mt-2 inline-block text-xs px-3 py-1 rounded-full font-semibold">
          {{ roleLabel }}
        </span>
      </div>

      <!-- Infos -->
      <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <h3 class="font-bold text-gray-700 mb-3">Informations</h3>
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-gray-400 text-sm">Téléphone</span>
            <span class="text-gray-700 text-sm font-semibold">{{ authStore.user?.telephone || '—' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400 text-sm">Langue</span>
            <span class="text-gray-700 text-sm font-semibold">{{ authStore.user?.langue === 'wo' ? '🇸🇳 Wolof' : '🇫🇷 Français' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400 text-sm">Rôle</span>
            <span class="text-gray-700 text-sm font-semibold">{{ roleLabel }}</span>
          </div>
        </div>
      </div>

      <!-- Changer mot de passe -->
      <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <h3 class="font-bold text-gray-700 mb-3">Changer le mot de passe</h3>
        <div class="space-y-3">
          <input v-model="pwd.ancien" type="password" placeholder="Ancien mot de passe"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <input v-model="pwd.nouveau" type="password" placeholder="Nouveau mot de passe (min 8 caractères)"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <input v-model="pwd.confirmation" type="password" placeholder="Confirmer le nouveau mot de passe"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>
        <div v-if="pwdErreur" class="mt-2 text-red-600 text-xs">{{ pwdErreur }}</div>
        <div v-if="pwdSucces" class="mt-2 text-green-600 text-xs">✓ {{ pwdSucces }}</div>
        <button @click="changerPwd" :disabled="pwdChargement"
          class="mt-3 w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-sm disabled:opacity-50">
          {{ pwdChargement ? 'Modification...' : 'Modifier le mot de passe' }}
        </button>
      </div>

      <!-- Déconnexion -->
      <button @click="deconnecter"
        class="w-full bg-red-50 border border-red-200 text-red-600 font-semibold py-3 rounded-xl">
        🚪 Se déconnecter
      </button>
    </div>

    <BottomNav :active="3" :role="authStore.user?.role" />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const router    = useRouter();

const pwd = ref({ ancien: '', nouveau: '', confirmation: '' });
const pwdErreur    = ref('');
const pwdSucces    = ref('');
const pwdChargement = ref(false);

const initiales = computed(() => {
  const u = authStore.user;
  return u ? (u.prenom[0] + u.nom[0]).toUpperCase() : '?';
});

const roleLabel = computed(() => ({
  parent:'Parent / Tuteur', encadreur:'Encadreur', responsable:'Responsable', admin:'Administrateur'
}[authStore.user?.role] || ''));

const roleBadge = computed(() => ({
  parent:'bg-teal-100 text-teal-700', encadreur:'bg-blue-100 text-blue-700',
  responsable:'bg-red-100 text-red-700', admin:'bg-violet-100 text-violet-700'
}[authStore.user?.role] || 'bg-gray-100 text-gray-500'));

async function changerPwd() {
  pwdErreur.value = ''; pwdSucces.value = '';
  if (pwd.value.nouveau !== pwd.value.confirmation) {
    pwdErreur.value = 'Les mots de passe ne correspondent pas.'; return;
  }
  if (pwd.value.nouveau.length < 8) {
    pwdErreur.value = 'Le nouveau mot de passe doit contenir au moins 8 caractères.'; return;
  }
  pwdChargement.value = true;
  try {
    await axios.post('/api/changer-mot-de-passe', {
      ancien_mot_de_passe: pwd.value.ancien,
      nouveau_mot_de_passe: pwd.value.nouveau,
      nouveau_mot_de_passe_confirmation: pwd.value.confirmation,
    });
    pwdSucces.value = 'Mot de passe modifié avec succès.';
    pwd.value = { ancien: '', nouveau: '', confirmation: '' };
  } catch(e) {
    pwdErreur.value = e.response?.data?.message || 'Erreur lors de la modification.';
  } finally { pwdChargement.value = false; }
}

async function deconnecter() {
  await authStore.logout();
  router.push('/login');
}
</script>
