<template>
  <div>
    <Radar :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Radar } from 'vue-chartjs';
import { Chart as ChartJS, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend } from 'chart.js';
ChartJS.register(RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend);

const props = defineProps({
  scores: { type: Object, default: () => ({ cognitif:0, moteur:0, socio_emotionnel:0, nutritionnel:0 }) }
});

const chartData = computed(() => ({
  labels: ['Cognitif', 'Moteur', 'Socio-émot.', 'Nutritionnel'],
  datasets: [{
    label: 'Score (%)',
    data: [
      props.scores.cognitif || 0,
      props.scores.moteur || 0,
      props.scores.socio_emotionnel || 0,
      props.scores.nutritionnel || 0,
    ],
    backgroundColor: 'rgba(46,117,182,0.2)',
    borderColor: '#2E75B6',
    pointBackgroundColor: '#2E75B6',
    borderWidth: 2,
  }],
}));

const chartOptions = {
  responsive: true,
  scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 25 } } },
  plugins: { legend: { display: false } },
};
</script>
