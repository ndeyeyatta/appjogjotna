<template>
  <AppLayout>
    <div class="bg-teal-700 px-4 pt-4 pb-6"><button @click="$router.back()" class="text-white mb-2 text-sm">← Retour</button><h1 class="text-white text-lg font-bold">Signaler une observation</h1></div>
    <div class="px-4 pt-4 pb-24">
      <div class="flex rounded-xl bg-gray-100 p-1 mb-5"><button @click="langue='fr'" :class="langue==='fr'?'bg-teal-600 text-white':'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold">🇫🇷 Français</button><button @click="langue='wo'" :class="langue==='wo'?'bg-teal-600 text-white':'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold">🇸🇳 Wolof</button></div>
      <div class="mb-4"><label class="block text-sm font-bold text-gray-700 mb-1">Enfant concerné *</label>
        <select v-model="form.enfant_id" class="w-full border-2 border-teal-400 rounded-xl px-4 py-3 text-sm focus:outline-none"><option value="">-- Choisir --</option><option v-for="e in enfants" :key="e.id" :value="e.id">{{ e.prenom }} {{ e.nom }}</option></select>
      </div>
      <div class="mb-5"><label class="block text-sm font-bold text-gray-700 mb-2">Catégorie *</label>
        <div class="grid grid-cols-3 gap-2">
          <button v-for="cat in categories" :key="cat.val" @click="form.categorie=cat.val" class="p-3 rounded-xl border-2 text-center transition" :class="form.categorie===cat.val?'border-teal-500 bg-teal-50':'border-gray-200 bg-white'">
            <div class="text-2xl mb-1">{{ cat.icone }}</div><div class="text-xs font-semibold" :class="form.categorie===cat.val?'text-teal-700':'text-gray-500'">{{ langue==='fr'?cat.fr:cat.wo }}</div>
          </button>
        </div>
      </div>
      <div class="mb-4"><label class="block text-sm font-bold text-gray-700 mb-1">Description *</label>
        <textarea v-model="form.description" rows="4" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400" :placeholder="langue==='fr'?'Décrivez ce que vous avez observé...':'Bind ko ci wolof...'"></textarea>
      </div>
      <div class="mb-5"><label class="block text-sm font-bold text-gray-700 mb-2">Niveau d'urgence</label>
        <div class="flex gap-2">
          <button v-for="u in urgences" :key="u.val" @click="form.urgence=u.val" class="flex-1 py-3 rounded-xl border-2 text-xs font-semibold transition" :class="form.urgence===u.val?u.actif:u.inactif">{{ langue==='fr'?u.fr:u.wo }}</button>
        </div>
      </div>
      <div v-if="erreur" class="bg-red-50 border border-red-300 text-red-700 text-sm rounded-xl p-3 mb-4">{{ erreur }}</div>
      <button @click="envoyer" :disabled="chargement||!form.enfant_id||!form.categorie||!form.description" class="w-full bg-teal-700 text-white font-bold py-4 rounded-xl disabled:opacity-50">
        {{ chargement?'Envoi en cours...':'📤 Envoyer l\'observation' }}
      </button>
    </div>
    <BottomNav :active="1" role="parent" />
  </AppLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
const props = defineProps({ enfantId:{ type:Number, default:null } });
const router = useRouter();
const langue=ref('fr'); const enfants=ref([]); const chargement=ref(false); const erreur=ref('');
const form=ref({ enfant_id:props.enfantId||'', categorie:'', description:'', urgence:'informatif' });
const categories=[{val:'comportement',icone:'🗣',fr:'Comportement',wo:'Jëf'},{val:'alimentation',icone:'🍽',fr:'Alimentation',wo:'Lekk'},{val:'motricite',icone:'🏃',fr:'Motricité',wo:'Yëgël'},{val:'sommeil',icone:'😴',fr:'Sommeil',wo:'Naxx'},{val:'relations',icone:'👥',fr:'Relations',wo:'Jëkker'},{val:'autre',icone:'❓',fr:'Autre',wo:'Añ'}];
const urgences=[{val:'informatif',fr:'Informatif',wo:'Xam-xam',actif:'border-green-500 bg-green-50 text-green-700',inactif:'border-gray-200 text-gray-400'},{val:'a_surveiller',fr:'À surveiller',wo:'Xool-xool',actif:'border-amber-500 bg-amber-50 text-amber-700',inactif:'border-gray-200 text-gray-400'},{val:'urgent',fr:'Urgent',wo:'Gaaw-gaaw',actif:'border-red-500 bg-red-50 text-red-700',inactif:'border-gray-200 text-gray-400'}];
onMounted(async()=>{ const{data}=await axios.get('/api/dashboard/parent'); enfants.value=data.enfants||[]; });
async function envoyer(){
  if(!form.value.enfant_id||!form.value.categorie||!form.value.description) return;
  erreur.value=''; chargement.value=true;
  try{
    const { data } = await axios.post('/api/observations', form.value);
    if (data.offline) alert(data.message);
    router.push('/dashboard/parent');
  }catch(e){
    erreur.value=e.response?.data?.message||'Erreur lors de l\'envoi.';
  }finally{chargement.value=false;}
}
</script>
