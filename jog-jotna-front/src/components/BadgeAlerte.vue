<template>
  <span :class="classes" class="text-xs px-2 py-1 rounded-full font-semibold inline-block">
    {{ icone }} {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ niveau: { type: String, default: 'informatif' } });
const label   = computed(() => ({ urgent:'Urgent', modere:'Modéré', informatif:'Info' }[props.niveau] || props.niveau));
const icone   = computed(() => ({ urgent:'🔴', modere:'🟡', informatif:'🔵' }[props.niveau] || '⚪'));
const classes = computed(() => ({
  urgent:    'bg-red-100 text-red-700',
  modere:    'bg-amber-100 text-amber-700',
  informatif:'bg-blue-100 text-blue-700',
}[props.niveau] || 'bg-gray-100 text-gray-500'));
</script>
