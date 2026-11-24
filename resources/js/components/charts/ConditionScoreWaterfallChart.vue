<template>
  <div class="w-full h-72 sm:h-80">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  venue: {
    type: Object,
    default: () => ({
      name: 'Fasilitas',
      score: 54,
    }),
  },
})

const chartOption = computed(() => {
  const finalScore = props.venue?.score ?? 54
  // Baseline: 100
  // Age factor penalty: ~ 15
  // Inspection deficit penalty: ~ 20
  // Incident & damage penalty: ~ 11
  // Total deduction = 100 - finalScore
  const totalDeduction = Math.max(0, 100 - finalScore)
  const agePenalty = Math.round(totalDeduction * 0.32)
  const inspectionPenalty = Math.round(totalDeduction * 0.48)
  const incidentPenalty = totalDeduction - agePenalty - inspectionPenalty

  const categories = [
    'Baseline Kondisi Baru',
    'Faktor Usia Teknis',
    'Defisit Skor Inspeksi',
    'Insiden Kerusakan',
    'Skor Kondisi Akhir',
  ]

  // Base transparent spacer
  const bottomPlaceholder = [
    0,
    100 - agePenalty,
    100 - agePenalty - inspectionPenalty,
    finalScore,
    0,
  ]

  // Value bars
  const values = [
    100,
    agePenalty,
    inspectionPenalty,
    incidentPenalty,
    finalScore,
  ]

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        const p = params[1]
        const val = p.value
        let sign = ''
        if (p.dataIndex > 0 && p.dataIndex < 4) sign = '- '
        return `<div class="font-bold text-xs mb-1">${p.name}</div>
          <div class="text-xs">Pengaruh Skor: <b>${sign}${val} poin</b></div>`
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
      data: categories,
      axisLabel: {
        fontSize: 10,
        interval: 0,
        rotate: 15,
      },
    },
    yAxis: {
      type: 'value',
      min: 0,
      max: 110,
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      // Transparent placeholder
      {
        name: 'Placeholder',
        type: 'bar',
        stack: 'total',
        itemStyle: {
          borderColor: 'transparent',
          color: 'transparent',
        },
        emphasis: {
          itemStyle: {
            borderColor: 'transparent',
            color: 'transparent',
          },
        },
        data: bottomPlaceholder,
      },
      // Value bar
      {
        name: 'Poin',
        type: 'bar',
        stack: 'total',
        label: {
          show: true,
          position: 'top',
          fontSize: 10,
          fontWeight: 'bold',
          formatter: (param) => {
            if (param.dataIndex === 0) return '100'
            if (param.dataIndex === 4) return `${finalScore}`
            return `-${param.value}`
          },
        },
        itemStyle: {
          color: (param) => {
            if (param.dataIndex === 0) return '#0B3C5D' // Initial baseline
            if (param.dataIndex === 4) {
              if (finalScore >= 70) return '#2E9E5B'
              if (finalScore >= 45) return '#E0A100'
              return '#D64545'
            }
            return '#D64545' // Deductions (Red)
          },
          borderRadius: 4,
        },
        data: values,
      },
    ],
  }
})
</script>