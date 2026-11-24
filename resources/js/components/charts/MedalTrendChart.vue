<template>
  <div class="w-full h-72 sm:h-80">
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
  const categories = list.map((item) => item.event || item.year || '')
  const gold = list.map((item) => item.gold ?? 0)
  const silver = list.map((item) => item.silver ?? 0)
  const bronze = list.map((item) => item.bronze ?? 0)

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        let total = 0
        let res = `<div class="font-semibold text-xs mb-1">${params[0].axisValue}</div>`
        params.forEach((p) => {
          total += Number(p.value || 0)
          res += `<div class="flex items-center justify-between text-xs gap-3">
            <span class="flex items-center gap-1.5">${p.marker} ${p.seriesName}</span>
            <span class="font-bold">${p.value}</span>
          </div>`
        })
        res += `<div class="border-t border-gray-200 mt-1 pt-1 flex justify-between text-xs font-bold">
          <span>Total Medali:</span><span>${total}</span>
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
      bottom: '12%',
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: categories,
      axisLabel: { fontSize: 11, interval: 0, rotate: categories.length > 4 ? 20 : 0 },
    },
    yAxis: {
      type: 'value',
      axisLabel: { fontSize: 11 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Emas',
        type: 'bar',
        stack: 'total',
        emphasis: { focus: 'series' },
        itemStyle: { color: '#F2A900' }, // Gold
        data: gold,
      },
      {
        name: 'Perak',
        type: 'bar',
        stack: 'total',
        emphasis: { focus: 'series' },
        itemStyle: { color: '#94A3B8' }, // Silver
        data: silver,
      },
      {
        name: 'Perunggu',
        type: 'bar',
        stack: 'total',
        emphasis: { focus: 'series' },
        itemStyle: { color: '#B45309' }, // Bronze
        data: bronze,
      },
    ],
  }
})
</script>