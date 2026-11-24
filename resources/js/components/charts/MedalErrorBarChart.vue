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
  const projected = list.map((p) => p.projected_points ?? 0)

  // Error data: [categoryIndex, min_points, max_points, projected_points]
  const errorData = list.map((p, idx) => [
    idx,
    p.min_points ?? Math.max(0, (p.projected_points || 0) - 3),
    p.max_points ?? ((p.projected_points || 0) + 3),
    p.projected_points ?? 0,
  ])

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        const item = props.projections[params[0].dataIndex]
        return `<div class="font-bold text-xs mb-1">${item.sport_name}</div>
          <div class="text-xs">Proyeksi Poin OLS: <b>${item.projected_points} pts</b></div>
          <div class="text-xs">Batas Bawah (Min): <b>${item.min_points} pts</b></div>
          <div class="text-xs">Batas Atas (Max): <b>${item.max_points} pts</b></div>
          <div class="text-[11px] text-brand-600 font-semibold mt-1">Rentang Ketidakpastian: ± ${((item.max_points - item.min_points) / 2).toFixed(1)} pts (${item.confidence})</div>`
      },
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: { fontSize: 10 },
      data: ['Estimasi Poin OLS', 'Interval Keyakinan (Error Bar ±1 SE)'],
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
        rotate: 25,
      },
    },
    yAxis: {
      type: 'value',
      name: 'Estimasi Poin Medali',
      nameTextStyle: { fontSize: 10, color: '#64748B' },
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      // Projected point (Scatter or Bar)
      {
        name: 'Estimasi Poin OLS',
        type: 'scatter',
        symbol: 'circle',
        symbolSize: 12,
        itemStyle: {
          color: '#1D6FA5',
          borderColor: '#FFFFFF',
          borderWidth: 2,
          shadowBlur: 4,
          shadowColor: 'rgba(0,0,0,0.2)',
        },
        data: projected,
        z: 3,
      },
      // Error Bar Whisker
      {
        name: 'Interval Keyakinan (Error Bar ±1 SE)',
        type: 'custom',
        itemStyle: {
          color: '#D64545',
          borderColor: '#D64545',
          borderWidth: 2,
        },
        renderItem: (params, api) => {
          const xVal = api.value(0)
          const lowPoint = api.coord([xVal, api.value(1)])
          const highPoint = api.coord([xVal, api.value(2)])
          const halfWidth = 7

          return {
            type: 'group',
            children: [
              // Vertical connecting error line
              {
                type: 'line',
                shape: {
                  x1: lowPoint[0],
                  y1: lowPoint[1],
                  x2: highPoint[0],
                  y2: highPoint[1],
                },
                style: {
                  stroke: '#D64545',
                  lineWidth: 2,
                },
              },
              // Top horizontal whisker
              {
                type: 'line',
                shape: {
                  x1: highPoint[0] - halfWidth,
                  y1: highPoint[1],
                  x2: highPoint[0] + halfWidth,
                  y2: highPoint[1],
                },
                style: {
                  stroke: '#D64545',
                  lineWidth: 2,
                },
              },
              // Bottom horizontal whisker
              {
                type: 'line',
                shape: {
                  x1: lowPoint[0] - halfWidth,
                  y1: lowPoint[1],
                  x2: lowPoint[0] + halfWidth,
                  y2: lowPoint[1],
                },
                style: {
                  stroke: '#D64545',
                  lineWidth: 2,
                },
              },
            ],
          }
        },
        data: errorData,
        z: 2,
      },
    ],
  }
})
</script>
