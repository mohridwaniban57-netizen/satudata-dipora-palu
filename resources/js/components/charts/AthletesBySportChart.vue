<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  const list = Array.isArray(props.data) && props.data.length > 0 ? props.data : []
  // Sort ascending by count
  const sorted = [...list].sort((a, b) => {
    const valA = typeof a.count === 'number' ? a.count : (Number(a.count) || 0)
    const valB = typeof b.count === 'number' ? b.count : (Number(b.count) || 0)
    return valA - valB
  })
  const sports = sorted.map((s) => s.sport_name || '')
  const counts = sorted.map((s) => typeof s.count === 'number' ? s.count : (Number(s.count) || 0))

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: '{b}: <b>{c}</b> atlet binaan',
    },
    grid: {
      left: '3%',
      right: '6%',
      bottom: '5%',
      top: '5%',
      containLabel: true,
    },
    xAxis: {
      type: 'value',
      axisLabel: { fontSize: 11 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      type: 'category',
      data: sports,
      axisLabel: { fontSize: 11 },
    },
    series: [
      {
        name: 'Jumlah Atlet',
        type: 'bar',
        itemStyle: {
          color: '#0B3C5D',
          borderRadius: [0, 4, 4, 0],
        },
        data: counts,
      },
    ],
  }
})
</script>