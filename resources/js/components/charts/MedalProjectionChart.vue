<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  projections: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  const list = Array.isArray(props.projections) && props.projections.length > 0 ? props.projections : []
  const sports = list.map((p) => p.sport_name || '')
  const lastPoints = list.map((p) => p.last_points ?? 0)
  const projectedPoints = list.map((p) => p.projected_points ?? 0)

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        const item = props.projections[params[0].dataIndex]
        let res = `<div class="font-semibold text-xs mb-1.5">${item.sport_name}</div>`
        params.forEach((p) => {
          res += `<div class="flex items-center justify-between text-xs gap-4 mb-0.5">
            <span class="flex items-center gap-1">${p.marker} ${p.seriesName}</span>
            <span class="font-bold">${p.value} pts</span>
          </div>`
        })
        res += `<div class="text-[11px] text-gray-500 mt-1 border-t border-gray-100 pt-1">
          Rentang: ${item.min_points} - ${item.max_points} pts (${item.confidence})
        </div>`
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
      bottom: '15%',
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: sports,
      axisLabel: {
        fontSize: 10,
        interval: 0,
        rotate: 30,
      },
    },
    yAxis: {
      type: 'value',
      name: 'Estimasi Poin',
      nameTextStyle: { fontSize: 11, color: '#64748B' },
      axisLabel: { fontSize: 11 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Poin Terakhir (Popda 2024)',
        type: 'bar',
        itemStyle: { color: '#94A3B8', borderRadius: [4, 4, 0, 0] },
        data: lastPoints,
      },
      {
        name: 'Proyeksi Event Berikutnya (OLS Model)',
        type: 'bar',
        itemStyle: { color: '#1D6FA5', borderRadius: [4, 4, 0, 0] },
        data: projectedPoints,
      },
    ],
  }
})
</script>