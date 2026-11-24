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
  districts: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  const districtList = props.districts.length > 0
    ? props.districts.map((d) => d.name)
    : ['Palu Barat', 'Palu Timur', 'Palu Selatan', 'Palu Utara', 'Mantikulore', 'Ulujadi', 'Tatanga', 'Tawaeli']

  const goodData = []
  const watchData = []
  const criticalData = []

  districtList.forEach((dName) => {
    const list = props.venues.filter((v) => v.district_name === dName)
    const good = list.filter((v) => v.status === 'good').length
    const watch = list.filter((v) => v.status === 'watch').length
    const critical = list.filter((v) => v.status === 'critical').length

    goodData.push(good)
    watchData.push(watch)
    criticalData.push(critical)
  })

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
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
      top: '6%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: districtList,
      axisLabel: {
        fontSize: 10,
        interval: 0,
        rotate: 25,
      },
    },
    yAxis: {
      type: 'value',
      axisLabel: { fontSize: 11 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Kondisi Baik',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#2E9E5B' }, // status-good
        data: goodData,
      },
      {
        name: 'Perlu Waspada',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#E0A100' }, // status-watch
        data: watchData,
      },
      {
        name: 'Kritis / Rusak',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#D64545' }, // status-critical
        data: criticalData,
      },
    ],
  }
})
</script>