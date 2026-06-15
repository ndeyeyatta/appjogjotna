<template>
  <div class="min-h-screen bg-gray-50 flex">
    <SideNav v-if="navRole" :active="navActive" :role="navRole" />
    <div class="flex-1 flex flex-col min-h-screen min-w-0">
      <OfflineBanner />
      <div class="flex-1 w-full mx-auto max-w-7xl relative">
        <slot />
      </div>
      <BottomNav v-if="navRole" :active="navActive" :role="navRole" />
    </div>
  </div>
</template>

<script setup>
import OfflineBanner from '@/components/OfflineBanner.vue';
import BottomNav from '@/components/BottomNav.vue';
import SideNav from '@/components/SideNav.vue';

defineProps({
  navActive: { type: Number, default: null },
  navRole: { type: String, default: null },
});
</script>
