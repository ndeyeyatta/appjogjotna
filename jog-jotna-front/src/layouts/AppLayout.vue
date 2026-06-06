<template>
  <div class="min-h-screen bg-gray-50 max-w-lg mx-auto relative">
    <OfflineBanner />
    <slot />
  </div>
</template>

<script setup>
import OfflineBanner from '@/components/OfflineBanner.vue';
</script>
