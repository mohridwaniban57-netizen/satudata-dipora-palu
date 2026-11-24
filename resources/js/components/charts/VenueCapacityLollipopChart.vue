<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  venues: {
    type: Array,
    default: () => [],
  },
  metric: {
    type: String,
    default: 'capacity', // 'capacity' or 'score'
  },
})

const chartOption = computed(() => {
  const list = Array.isArray(props.venues) ? props.venues : []
  // Take top 10 venues sorted by capacity descending
  const sorted = [...list]
    .filter((v) => v.capacity && v.capacity > 0)
    .sort((a, b) => b.capacity - a.capacity)
    .slice(0, 10)
    .reverse() // for bottom-to-top presentation

  const names = sorted.map((v) => v.name)
  const capacities = sorted.map((v) => v.capacity)

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        const item = sorted[params[0].dataIndex]
        return `<div class="font-bold text-xs mb-1">${item.name}</div>
          <div class="text-xs">Kecamatan: <b>${item.district_name}</b></div>
          <div class="text-xs">Tipe: <b>${item.type_name}</b></div>
          <div class="text-xs mt-1 text-brand-600 font-bold">Kapasitas: ${item.capacity.toLocaleString('id-ID')} penonton</div>`
      },
    },
    grid: {
      left: '3%',
      right: '8%',
      bottom: '6%',
      top: '6%',
      containLabel: true,
    },
    xAxis: {
      type: 'value',
      name: 'Kapasitas (Penonton)',
      nameLocation: 'middle',
      nameGap: 28,
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      type: 'category',
      data: names,
      axisLabel: {
        fontSize: 10,
        formatter: (val) => (val.length > 18 ? val.substring(0, 18) + '...' : val),
      },
    },
    series: [
      // Lollipop stem (thin bar)
      {
        name: 'Kapasitas',
        type: 'bar',
        barWidth: 3,
        itemStyle: {
          color: '#94A3B8',
        },
        data: capacities,
        z: 1,
      },
      // Lollipop head (scatter point on the end of the stem)
      {
        name: 'Titik Kapasitas',
        type: 'scatter',
        symbolSize: 18,
        itemStyle: {
          color: (param) => {
            const v = sorted[param.dataIndex]
            if (v.status === 'good') return '#2E9E5B'
            if (v.status === 'watch') return '#E0A100'
            return '#D64545'
          },
          borderColor: '#FFFFFF',
          borderWidth: 2,
          shadowBlur: 4,
          shadowColor: 'rgba(0,0,0,0.2)',
        },
        data: capacities,
        z: 2,
      },
    ],
  }
})
</script>
