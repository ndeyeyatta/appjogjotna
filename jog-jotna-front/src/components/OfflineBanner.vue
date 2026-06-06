<template>
  <div v-if="!online || pending > 0"
    class="bg-amber-500 text-white text-xs text-center py-1.5 px-3 z-50"
    :class="online ? 'bg-teal-600' : 'bg-amber-600'">
    <span v-if="!online">📴 Mode hors ligne — les observations seront synchronisées à la reconnexion</span>
    <span v-else-if="pending > 0">🔄 Synchronisation de {{ pending }} élément(s)...</span>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { isOnline, pendingCount, flushQueue } from '@/offline/sync';

const online = ref(isOnline());
const pending = ref(0);

async function refresh() {
  online.value = isOnline();
  pending.value = await pendingCount();
  if (online.value && pending.value > 0) {
    await flushQueue();
    pending.value = await pendingCount();
  }
}

let interval;
onMounted(() => {
  refresh();
  window.addEventListener('online', refresh);
  window.addEventListener('offline', refresh);
  interval = setInterval(refresh, 10000);
});
onUnmounted(() => {
  window.removeEventListener('online', refresh);
  window.removeEventListener('offline', refresh);
  clearInterval(interval);
});
</script>
