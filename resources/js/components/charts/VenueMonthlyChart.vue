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
  const list = Array.isArray(props.stats) ? props.stats : []
  const months = list.map((s) => s.month)
  const visits = list.map((s) => s.visits)
  const revenues = list.map((s) => Math.round((s.revenue || 0) / 1000000))

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'cross' },
      formatter: (params) => {
        let res = `<div class="font-bold text-xs mb-1">Bulan: ${params[0].axisValue}</div>`
        params.forEach((p) => {
          const unit = p.seriesName.includes('Kunjungan') ? 'orang' : 'juta Rp'
          res += `<div class="flex items-center justify-between text-xs gap-3">
            <span>${p.marker} ${p.seriesName}</span>
            <span class="font-bold">${p.value} ${unit}</span>
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
      top: '10%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: months,
      axisLabel: { fontSize: 10 },
    },
    yAxis: [
      {
        type: 'value',
        name: 'Kunjungan',
        axisLabel: { fontSize: 10 },
        splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
      },
      {
        type: 'value',
        name: 'Retribusi (Jt Rp)',
        axisLabel: { fontSize: 10 },
        splitLine: { show: false },
      },
    ],
    series: [
      {
        name: 'Tren Kunjungan',
        type: 'line',
        smooth: true,
        itemStyle: { color: '#1D6FA5' },
        lineStyle: { width: 3 },
        data: visits,
      },
      {
        name: 'Realisasi Retribusi',
        type: 'bar',
        yAxisIndex: 1,
        itemStyle: { color: '#F2A900', borderRadius: [4, 4, 0, 0] },
        data: revenues,
      },
    ],
  }
})
</script>