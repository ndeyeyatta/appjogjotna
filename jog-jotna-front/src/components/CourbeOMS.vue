<template>
  <div class="bg-white rounded-xl p-4">
    <h3 class="font-bold text-gray-700 mb-3 text-sm">📈 Courbe de croissance — Normes OMS</h3>
    <Line :data="chartData" :options="chartOptions" />
    <div class="flex gap-3 mt-2 text-xs">
      <span class="flex items-center gap-1"><span class="w-3 h-1 bg-blue-500 inline-block rounded"></span>Enfant</span>
      <span class="flex items-center gap-1"><span class="w-3 h-1 bg-gray-300 inline-block rounded"></span>Médiane OMS</span>
      <span class="flex items-center gap-1"><span class="w-3 h-1 bg-amber-400 inline-block rounded"></span>Seuil -2DS</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend, Filler } from 'chart.js';
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend, Filler);

const props = defineProps({
  mesures: { type: Array, default: () => [] },
  sexe:    { type: String, default: 'M' },
});

// Médianes OMS poids-pour-âge simplifiées
const oms = {
  M: { 24:12.2, 30:13.3, 36:14.3, 42:15.3, 48:16.3, 54:17.3, 60:18.3 },
  F: { 24:11.5, 30:12.7, 36:13.9, 42:14.9, 48:15.9, 54:16.9, 60:18.0 },
};
const seuils = {
  M: { 24:10.0, 30:11.0, 36:11.8, 42:12.5, 48:13.3, 54:14.1, 60:15.0 },
  F: { 24: 9.4, 30:10.3, 36:11.3, 42:12.0, 48:12.8, 54:13.6, 60:14.5 },
};

const chartData = computed(() => {
  const labels = props.mesures.map(m => m.date_mesure || '');
  const omsRef  = props.sexe === 'M' ? oms.M : oms.F;
  const sRef    = props.sexe === 'M' ? seuils.M : seuils.F;

  return {
    labels,
    datasets: [
      {
        label: 'Poids enfant (kg)',
        data: props.mesures.map(m => m.poids),
        borderColor: '#2E75B6',
        backgroundColor: 'rgba(46,117,182,0.1)',
        borderWidth: 2,
        pointRadius: 5,
        pointBackgroundColor: '#2E75B6',
        tension: 0.3,
        fill: false,
      },
      {
        label: 'Médiane OMS',
        data: props.mesures.map(m => {
          const months = m.age_mois || 36;
          const keys = Object.keys(omsRef).map(Number);
          const closest = keys.reduce((a,b) => Math.abs(b-months) < Math.abs(a-months) ? b : a);
          return omsRef[closest];
        }),
        borderColor: '#9CA3AF',
        borderWidth: 1,
        borderDash: [5,5],
        pointRadius: 0,
        fill: false,
      },
      {
        label: 'Seuil -2 DS',
        data: props.mesures.map(m => {
          const months = m.age_mois || 36;
          const keys = Object.keys(sRef).map(Number);
          const closest = keys.reduce((a,b) => Math.abs(b-months) < Math.abs(a-months) ? b : a);
          return sRef[closest];
        }),
        borderColor: '#F59E0B',
        borderWidth: 1,
        borderDash: [3,3],
        pointRadius: 0,
        fill: false,
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: false },
    tooltip: { mode: 'index', intersect: false },
  },
  scales: {
    y: {
      title: { display: true, text: 'Poids (kg)' },
      beginAtZero: false,
    },
    x: {
      title: { display: true, text: 'Date mesure' },
    },
  },
};
</script>
