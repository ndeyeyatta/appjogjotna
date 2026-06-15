<template>
  <div class="min-h-screen bg-gradient-to-b from-blue-900 to-blue-100 flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-sm sm:max-w-md lg:max-w-lg">
      <div class="text-center mb-8">
        <div class="inline-block bg-white rounded-2xl px-8 py-4 shadow-lg mb-4">
          <h1 class="text-3xl sm:text-4xl font-bold text-blue-900">JÒG</h1>
          <p class="text-blue-600 font-semibold text-sm">JOTNA</p>
        </div>
        <h2 class="text-white text-xl sm:text-2xl font-semibold">Bienvenue</h2>
        <p class="text-blue-200 text-sm">Connectez-vous à votre espace</p>
      </div>
      <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8">
        <div class="flex rounded-xl bg-gray-100 p-1 mb-6">
          <button @click="langue='fr'" :class="langue==='fr'?'bg-teal-600 text-white':'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition">🇫🇷 Français</button>
          <button @click="langue='wo'" :class="langue==='wo'?'bg-teal-600 text-white':'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition">🇸🇳 Wolof</button>
        </div>
        <form @submit.prevent="seConnecter">
          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ langue==='fr'?'Email':'Email bi' }}</label>
            <div class="relative"><span class="absolute left-3 top-3 text-gray-400">✉</span>
              <input v-model="form.email" type="email" required class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm" placeholder="exemple@email.com" />
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ langue==='fr'?'Mot de passe':'Wàllu xel bi' }}</label>
            <div class="relative"><span class="absolute left-3 top-3 text-gray-400">🔒</span>
              <input v-model="form.mot_de_passe" :type="showPwd?'text':'password'" required class="w-full pl-9 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm" placeholder="••••••••" />
              <button type="button" @click="showPwd=!showPwd" class="absolute right-3 top-3 text-gray-400 text-sm">{{ showPwd?'🙈':'👁' }}</button>
            </div>
          </div>
          <div v-if="erreur" class="bg-red-50 border border-red-300 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">{{ erreur }}</div>
          <button type="submit" :disabled="chargement" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50">
            {{ chargement?'Connexion...':(langue==='fr'?'Se connecter':'Duggu') }}
          </button>
        </form>
        <div class="mt-6">
          <p class="text-center text-xs text-gray-400 mb-3">Accès selon votre rôle</p>
          <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 text-center py-2 rounded-lg bg-teal-50 border border-teal-200"><div class="text-lg">👨‍👩‍👧</div><div class="text-xs font-semibold text-teal-700">Parent</div></div>
            <div class="flex-1 text-center py-2 rounded-lg bg-blue-50 border border-blue-200"><div class="text-lg">👩‍🏫</div><div class="text-xs font-semibold text-blue-700">Encadreur</div></div>
            <div class="flex-1 text-center py-2 rounded-lg bg-red-50 border border-red-200"><div class="text-lg">📊</div><div class="text-xs font-semibold text-red-700">Responsable</div></div>
          </div>
        </div>
        <p class="text-center text-xs text-gray-400 mt-4">🔒 Connexion sécurisée HTTPS</p>
      </div>
      <p class="text-center text-blue-200 text-xs mt-4">JÒG JOTNA v1.0 - Diagambal © 2025</p>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
const router = useRouter();
const authStore = useAuthStore();
const langue = ref('fr'); const showPwd = ref(false); const chargement = ref(false); const erreur = ref('');
const form = ref({ email:'', mot_de_passe:'' });
async function seConnecter() {
  erreur.value=''; chargement.value=true;
  try {
    const data = await authStore.login(form.value.email, form.value.mot_de_passe);
    const role = data.utilisateur.role;
    if (role === 'parent') router.push('/dashboard/parent');
    else if (role === 'encadreur') router.push('/dashboard/encadreur');
    else if (role === 'admin') router.push('/dashboard/responsable');
    else router.push('/dashboard/responsable');
  } catch { erreur.value=langue.value==='fr'?'Email ou mot de passe incorrect.':'Email walla wàllu xel bi dafa fey.'; }
  finally { chargement.value=false; }
}
</script>
nd