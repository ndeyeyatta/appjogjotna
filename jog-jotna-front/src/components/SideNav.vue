<template>
  <aside class="hidden md:flex md:flex-col md:w-64 lg:w-72 bg-white border-r border-gray-200 shrink-0 sticky top-0 h-screen">
    <div class="px-5 py-6 border-b border-gray-100">
      <h1 class="text-xl font-bold text-jog-blue">JÒG</h1>
      <p class="text-jog-teal text-sm font-semibold">JOTNA</p>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <button
        v-for="(item, i) in items"
        :key="item.to"
        @click="$router.push(item.to)"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition"
        :class="active === i
          ? 'bg-blue-50 text-blue-700 font-semibold'
          : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
      >
        <span class="text-xl">{{ item.icon }}</span>
        <span>{{ item.label }}</span>
      </button>
    </nav>
    <div class="px-5 py-4 border-t border-gray-100 text-xs text-gray-400">
      Suivi Enfant v1.0
    </div>
  </aside>
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
