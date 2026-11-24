<template>
  <div class="w-full h-72 sm:h-80">
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
})

const chartOption = computed(() => {
  // Define 6 score bins: <50 (Kritis), 50-59 (Waspada Rendah), 60-69 (Waspada Tinggi), 70-79 (Baik), 80-89 (Sangat Baik), 90-100 (Prima)
  const bins = [
    { label: '< 50 (Kritis)', min: 0, max: 49.9, color: '#D64545' },
    { label: '50 - 59', min: 50, max: 59.9, color: '#E0A100' },
    { label: '60 - 69', min: 60, max: 69.9, color: '#F2A900' },
    { label: '70 - 79', min: 70, max: 79.9, color: '#2585C4' },
    { label: '80 - 89', min: 80, max: 89.9, color: '#1D6FA5' },
    { label: '90 - 100 (Prima)', min: 90, max: 100, color: '#2E9E5B' },
  ]

  const venuesList = Array.isArray(props.venues) ? props.venues : []
  const counts = bins.map((bin) => {
    return venuesList.filter((v) => {
      const s = Number(v.score || 0)
      return s >= bin.min && s <= bin.max
    }).length
  })

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        const idx = params[0].dataIndex
        const bin = bins[idx]
        const total = venuesList.length || 1
        const pct = ((counts[idx] / total) * 100).toFixed(1)
        return `<div class="font-bold text-xs mb-1">Rentang Skor: ${bin.label}</div>
          <div class="text-xs">Frekuensi: <b>${counts[idx]}</b> fasilitas (${pct}%)</div>`
      },
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '12%',
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: bins.map((b) => b.label),
      axisLabel: {
        fontSize: 10,
        interval: 0,
        rotate: 15,
      },
      name: 'Interval Skor Kondisi Fisik',
      nameLocation: 'middle',
      nameGap: 30,
      nameTextStyle: { fontSize: 10, color: '#64748B' },
    },
    yAxis: {
      type: 'value',
      name: 'Jumlah Fasilitas',
      nameTextStyle: { fontSize: 10, color: '#64748B' },
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Distribusi Frekuensi Fasilitas',
        type: 'bar',
        barWidth: '70%',
        itemStyle: {
          color: (param) => bins[param.dataIndex]?.color || '#1D6FA5',
          borderRadius: [4, 4, 0, 0],
        },
        label: {
          show: true,
          position: 'top',
          fontSize: 11,
          fontWeight: 'bold',
          color: '#1F2933',
        },
        data: counts,
      },
    ],
  }
})
</script>