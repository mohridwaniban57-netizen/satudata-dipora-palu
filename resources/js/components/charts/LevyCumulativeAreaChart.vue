<template>
  <div class="w-full h-72 sm:h-80">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  stats: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  // 24 Months timeline
  const months = [
    '23-01', '23-02', '23-03', '23-04', '23-05', '23-06',
    '23-07', '23-08', '23-09', '23-10', '23-11', '23-12',
    '24-01', '24-02', '24-03', '24-04', '24-05', '24-06',
    '24-07', '24-08', '24-09', '24-10', '24-11', '24-12',
  ]

  // Monthly increments (Juta Rupiah)
  const monthlyRevenue = [
    18, 22, 20, 25, 28, 30, 26, 32, 29, 34, 31, 38,
    22, 25, 24, 30, 35, 38, 32, 40, 36, 42, 39, 48,
  ]

  // Calculate cumulative real revenue
  let sum = 0
  const cumulativeData = monthlyRevenue.map((v) => {
    sum += v
    return sum
  })

  // Target line: Linear progression up to 750 Juta
  const targetData = months.map((_, idx) => Math.round((750 / 24) * (idx + 1)))

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'line' },
      formatter: (params) => {
        let res = `<div class="font-bold text-xs mb-1">Periode: ${params[0].axisValue}</div>`
        params.forEach((p) => {
          res += `<div class="flex items-center justify-between text-xs gap-3">
            <span>${p.marker} ${p.seriesName}</span>
            <span class="font-bold">Rp ${p.value.toLocaleString('id-ID')} Jt</span>
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
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: months,
      axisLabel: { fontSize: 9, interval: 2 },
    },
    yAxis: {
      type: 'value',
      name: 'Kumulatif (Juta Rp)',
      nameTextStyle: { fontSize: 10, color: '#64748B' },
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Realisasi Kumulatif',
        type: 'line',
        smooth: true,
        symbol: 'circle',
        symbolSize: 4,
        itemStyle: { color: '#1D6FA5' },
        lineStyle: { width: 3, color: '#1D6FA5' },
        areaStyle: {
          color: {
            type: 'linear',
            x: 0,
            y: 0,
            x2: 0,
            y2: 1,
            colorStops: [
              { offset: 0, color: 'rgba(29, 111, 165, 0.45)' },
              { offset: 1, color: 'rgba(29, 111, 165, 0.02)' },
            ],
          },
        },
        data: cumulativeData,
      },
      {
        name: 'Target Anggaran PAD',
        type: 'line',
        symbol: 'none',
        lineStyle: { width: 2, type: 'dashed', color: '#F2A900' },
        itemStyle: { color: '#F2A900' },
        data: targetData,
      },
    ],
  }
})
</script>
