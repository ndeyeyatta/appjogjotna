<template>
  <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex z-50 safe-bottom">
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
import { getNavItems } from '@/config/navMenus';

const props = defineProps({
  active: { type: Number, default: 0 },
  role: { type: String, default: 'parent' },
});

const items = computed(() => getNavItems(props.role));
</script>
