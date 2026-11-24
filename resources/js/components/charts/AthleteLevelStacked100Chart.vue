<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  sports: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  const sportNames = Array.isArray(props.sports) && props.sports.length > 0
    ? props.sports.slice(0, 8).map((s) => s.sport_name)
    : []
  const categories = sportNames.length >= 4 ? sportNames : [
    'Sepak Bola', 'Atletik', 'Pencak Silat', 'Bulu Tangkis',
    'Renang', 'Bola Voli', 'Taekwondo', 'Panahan',
  ]

  // Proportions that sum to 100% per sport
  const clubData = [55, 45, 40, 50, 48, 60, 52, 40]
  const districtData = [25, 25, 30, 25, 26, 22, 24, 30]
  const provinceData = [15, 20, 20, 18, 18, 14, 16, 20]
  const nationalData = [5, 10, 10, 7, 8, 4, 8, 10]

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        let res = `<div class="font-bold text-xs mb-1">${params[0].axisValue}</div>`
        params.forEach((p) => {
          res += `<div class="flex items-center justify-between text-xs gap-3">
            <span>${p.marker} ${p.seriesName}</span>
            <span class="font-bold">${p.value}%</span>
          </div>`
        })
        return res
      },
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: { fontSize: 11 },
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '12%',
      top: '6%',
      containLabel: true,
    },
    xAxis: {
      type: 'value',
      min: 0,
      max: 100,
      axisLabel: { formatter: '{value}%', fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      type: 'category',
      data: categories,
      axisLabel: { fontSize: 10 },
    },
    series: [
      {
        name: 'Klub / Pemula',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#94A3B8' },
        data: clubData,
      },
      {
        name: 'Tingkat Daerah / Kota',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#2585C4' },
        data: districtData,
      },
      {
        name: 'Puslatda Provinsi',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#1D6FA5' },
        data: provinceData,
      },
      {
        name: 'Pelatnas / Nasional',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#F2A900', borderRadius: [0, 4, 4, 0] },
        data: nationalData,
      },
    ],
  }
})
</script>
