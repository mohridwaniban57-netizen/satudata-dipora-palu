<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  sportsData: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  // Evaluasi 5 indikator kinerja cabang olahraga
  const indicator = [
    { name: 'Rasio Emas', max: 100 },
    { name: 'Kuantitas Atlet', max: 100 },
    { name: 'Dukungan Anggaran', max: 100 },
    { name: 'Keterlibatan Putri', max: 100 },
    { name: 'Konsistensi Prestasi', max: 100 },
    { name: 'Kemandirian Sarana', max: 100 },
  ]

  // Default comparison profile for Palu's top sports
  const seriesData = [
    {
      value: [88, 75, 70, 80, 85, 65],
      name: 'Atletik',
      itemStyle: { color: '#0B3C5D' },
      areaStyle: { color: 'rgba(11, 60, 93, 0.25)' },
    },
    {
      value: [78, 85, 90, 60, 75, 80],
      name: 'Renang',
      itemStyle: { color: '#1D6FA5' },
      areaStyle: { color: 'rgba(29, 111, 165, 0.25)' },
    },
    {
      value: [72, 70, 65, 75, 80, 70],
      name: 'Pencak Silat',
      itemStyle: { color: '#F2A900' },
      areaStyle: { color: 'rgba(242, 169, 0, 0.25)' },
    },
    {
      value: [65, 60, 55, 70, 70, 60],
      name: 'Bulu Tangkis',
      itemStyle: { color: '#2E9E5B' },
      areaStyle: { color: 'rgba(46, 158, 91, 0.25)' },
    },
  ]

  return {
    tooltip: {
      trigger: 'item',
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: { fontSize: 11 },
    },
    radar: {
      indicator,
      radius: '65%',
      center: ['50%', '48%'],
      splitNumber: 4,
      axisName: {
        color: '#475569',
        fontSize: 10,
        fontWeight: 'bold',
      },
      splitLine: {
        lineStyle: {
          color: '#E2E8F0',
        },
      },
      splitArea: {
        show: true,
        areaStyle: {
          color: ['#F8FAFC', '#FFFFFF'],
        },
      },
    },
    series: [
      {
        name: 'Profil Multi-Dimensi Cabor',
        type: 'radar',
        data: seriesData,
      },
    ],
  }
})
</script>