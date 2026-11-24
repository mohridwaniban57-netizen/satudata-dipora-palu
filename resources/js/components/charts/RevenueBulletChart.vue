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
  // Categories of Venues
  const categories = [
    'Stadion Sepak Bola',
    'Gelanggang Olahraga (GOR)',
    'Kolam Renang',
    'Lapangan Terbuka',
    'Gedung Bulutangkis',
  ]

  // Actual Revenue achieved (in Juta Rupiah)
  const actualValues = [145, 118, 72, 45, 65]
  // Target PAD (in Juta Rupiah)
  const targetValues = [160, 130, 85, 50, 70]

  // Background ranges (Qualitative bands)
  // Range 1 (Kritis/Dasar - 40% of target)
  const rangePoor = targetValues.map((t) => t * 0.5)
  // Range 2 (Moderat - 80% of target)
  const rangeModerate = targetValues.map((t) => t * 0.85)
  // Range 3 (Ekspektasi Maksimum - 120% of target)
  const rangeTarget = targetValues.map((t) => t * 1.2)

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        const idx = params[0].dataIndex
        const cat = categories[idx]
        const actual = actualValues[idx]
        const target = targetValues[idx]
        const pct = ((actual / target) * 100).toFixed(1)
        return `<div class="font-bold text-xs mb-1">${cat}</div>
          <div class="text-xs">Realisasi: <b>Rp ${actual} Juta</b></div>
          <div class="text-xs">Target PAD: <b>Rp ${target} Juta</b></div>
          <div class="text-xs mt-1 font-semibold ${actual >= target ? 'text-emerald-600' : 'text-amber-600'}">
            Pencapaian: ${pct}%
          </div>`
      },
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: { fontSize: 10 },
      data: ['Realisasi Aktual', 'Target Anggaran PAD'],
    },
    grid: {
      left: '3%',
      right: '6%',
      bottom: '12%',
      top: '6%',
      containLabel: true,
    },
    xAxis: {
      type: 'value',
      name: 'Realisasi Retribusi (Juta Rp)',
      nameLocation: 'middle',
      nameGap: 28,
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      type: 'category',
      data: categories,
      axisLabel: { fontSize: 10 },
    },
    series: [
      // Qualitative band 3 (Light gray background)
      {
        name: 'Batas Maksimum',
        type: 'bar',
        barWidth: 24,
        barGap: '-100%',
        itemStyle: { color: '#F1F5F9' },
        data: rangeTarget,
        silent: true,
        z: 1,
      },
      // Qualitative band 2 (Medium gray background)
      {
        name: 'Batas Moderat',
        type: 'bar',
        barWidth: 24,
        barGap: '-100%',
        itemStyle: { color: '#E2E8F0' },
        data: rangeModerate,
        silent: true,
        z: 2,
      },
      // Qualitative band 1 (Darker gray background)
      {
        name: 'Batas Kritis',
        type: 'bar',
        barWidth: 24,
        barGap: '-100%',
        itemStyle: { color: '#CBD5E1' },
        data: rangePoor,
        silent: true,
        z: 3,
      },
      // Actual Bar (Primary Brand Bar)
      {
        name: 'Realisasi Aktual',
        type: 'bar',
        barWidth: 10,
        barGap: '-100%',
        itemStyle: {
          color: '#1D6FA5',
          borderRadius: [0, 3, 3, 0],
        },
        data: actualValues,
        z: 4,
      },
      // Target Marker (Vertical tick line on bar)
      {
        name: 'Target Anggaran PAD',
        type: 'scatter',
        symbol: 'rect',
        symbolSize: [4, 28],
        itemStyle: {
          color: '#D64545',
        },
        data: targetValues,
        z: 5,
      },
    ],
  }
})
</script>