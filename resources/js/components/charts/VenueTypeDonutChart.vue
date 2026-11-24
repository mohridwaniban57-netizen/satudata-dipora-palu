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
  // Aggregate count by type_name
  const counts = {}
  props.venues.forEach((v) => {
    const t = v.type_name || 'Lainnya'
    counts[t] = (counts[t] || 0) + 1
  })

  const data = Object.entries(counts).map(([name, value]) => ({ name, value }))

  return {
    tooltip: {
      trigger: 'item',
      formatter: '{b}: <b>{c}</b> venue ({d}%)',
    },
    legend: {
      bottom: '2%',
      left: 'center',
      icon: 'circle',
      textStyle: { fontSize: 11 },
    },
    color: ['#0B3C5D', '#1D6FA5', '#2585C4', '#F2A900', '#2E9E5B', '#8A63D2', '#D64545'],
    series: [
      {
        name: 'Jenis Fasilitas',
        type: 'pie',
        radius: ['45%', '70%'],
        center: ['50%', '45%'],
        avoidLabelOverlap: false,
        itemStyle: {
          borderRadius: 6,
          borderColor: '#fff',
          borderWidth: 2,
        },
        label: {
          show: false,
          position: 'center',
        },
        emphasis: {
          label: {
            show: true,
            fontSize: 14,
            fontWeight: 'bold',
            formatter: '{b}\n{c} unit',
          },
        },
        labelLine: { show: false },
        data,
      },
    ],
  }
})
</script>