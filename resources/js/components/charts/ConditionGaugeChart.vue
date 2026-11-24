<template>
  <div class="w-full h-60 sm:h-64">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  score: {
    type: Number,
    required: true,
    default: 0,
  },
  title: {
    type: String,
    default: 'Skor Kondisi Aset',
  },
})

const chartOption = computed(() => {
  return {
    tooltip: {
      formatter: '{a} <br/>{b} : {c} / 100',
    },
    series: [
      {
        name: props.title,
        type: 'gauge',
        startAngle: 180,
        endAngle: 0,
        min: 0,
        max: 100,
        splitNumber: 5,
        radius: '95%',
        center: ['50%', '70%'],
        axisLine: {
          lineStyle: {
            width: 14,
            color: [
              [0.45, '#D64545'], // Critical
              [0.70, '#E0A100'], // Watch
              [1, '#2E9E5B'],    // Good
            ],
          },
        },
        pointer: {
          icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
          length: '12%',
          width: 10,
          offsetCenter: [0, '-50%'],
          itemStyle: { color: '#0B3C5D' },
        },
        axisTick: {
          length: 6,
          lineStyle: { color: 'auto', width: 1.5 },
        },
        splitLine: {
          length: 12,
          lineStyle: { color: 'auto', width: 2 },
        },
        axisLabel: {
          color: '#64748B',
          fontSize: 10,
          distance: -40,
        },
        title: {
          offsetCenter: [0, '-15%'],
          fontSize: 13,
          color: '#64748B',
        },
        detail: {
          fontSize: 28,
          offsetCenter: [0, '-35%'],
          valueAnimation: true,
          formatter: (value) => {
            let label = 'Kritis'
            if (value >= 70) label = 'Baik'
            else if (value >= 45) label = 'Waspada'
            return `{value|${Math.round(value)}}\n{status|${label}}`
          },
          rich: {
            value: {
              fontSize: 32,
              fontWeight: 'bolder',
              color: '#0B3C5D',
            },
            status: {
              fontSize: 12,
              color: '#64748B',
              padding: [4, 0, 0, 0],
            },
          },
        },
        data: [{ value: props.score, name: props.title }],
      },
    ],
  }
})
</script>