<template>
  <nav class="fixed bottom-0 left-0 right-0 max-w-lg mx-auto bg-white border-t border-gray-200 flex z-50">
    <button
      v-for="(item, i) in items"
      :key="item.to"
      @click="$router.push(item.to)"
      class="flex-1 py-2 flex flex-col items-center gap-0.5 text-xs transition"
      :class="active === i ? 'text-blue-700 font-semibold' : 'text-gray-400'"
    >
      <span class="text-lg">{{ item.icon }}</span>
      <span>{{ item.label }}</span>
    </button>
  </nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  active: { type: Number, default: 0 },
  role:   { type: String, default: 'parent' },
});

const menus = {
  parent: [
    { to: '/dashboard/parent',    icon: '🏠', label: 'Accueil' },
    { to: '/observations/creer',  icon: '✏',  label: 'Signaler' },
    { to: '/notifications',       icon: '🔔', label: 'Alertes' },
    { to: '/profil',              icon: '👤', label: 'Profil' },
  ],
  encadreur: [
    { to: '/dashboard/encadreur',      icon: '🏠', label: 'Accueil' },
    { to: '/enfants',                  icon: '👶', label: 'Enfants' },
    { to: '/observations/validation',  icon: '📋', label: 'Signalements' },
    { to: '/profil',                   icon: '👤', label: 'Profil' },
  ],
  responsable: [
    { to: '/dashboard/responsable', icon: '🏠', label: 'Accueil' },
    { to: '/rapports',              icon: '📊', label: 'Rapports' },
    { to: '/alertes',               icon: '⚠',  label: 'Alertes' },
    { to: '/profil',                icon: '👤', label: 'Profil' },
  ],
  admin: [
    { to: '/dashboard/responsable', icon: '🏠', label: 'Accueil' },
    { to: '/admin/utilisateurs',    icon: '👥', label: 'Comptes' },
    { to: '/rapports',              icon: '📊', label: 'Rapports' },
    { to: '/profil',                icon: '👤', label: 'Profil' },
  ],
};

const items = computed(() => menus[props.role] || menus.parent);
</script>
