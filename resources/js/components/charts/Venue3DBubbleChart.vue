<template>
  <div class="w-full h-80 sm:h-96">
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
  const list = Array.isArray(props.venues) ? props.venues : []
  // Format bubble data: [annual_visits / 1000, annual_revenue / 1000000, capacity / 100, venue_name, status, district]
  const data = list.map((v) => {
    const visitsK = (v.annual_visits || 12000) / 1000
    const revenueM = (v.annual_revenue || 35000000) / 1000000
    const cap = v.capacity || 500
    return [visitsK, revenueM, cap, v.name, v.status, v.district_name]
  })

  return {
    tooltip: {
      formatter: (param) => {
        const d = param.data
        return `<div class="font-bold text-xs mb-1">${d[3]}</div>
          <div class="text-xs">Kecamatan: <b>${d[5]}</b></div>
          <div class="text-xs">Kunjungan: <b>${(d[0] * 1000).toLocaleString('id-ID')}</b> org/thn</div>
          <div class="text-xs">Retribusi: <b>Rp ${(d[1] * 1000000).toLocaleString('id-ID')}</b></div>
          <div class="text-xs font-semibold text-brand-600 mt-1">Kapasitas: ${d[2].toLocaleString('id-ID')} orang</div>`
      },
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: { fontSize: 10 },
      data: ['Kondisi Baik', 'Perlu Waspada', 'Kondisi Kritis'],
    },
    grid: {
      left: '4%',
      right: '6%',
      bottom: '12%',
      top: '8%',
      containLabel: true,
    },
    xAxis: {
      name: 'Kunjungan (Ribu Orang/Tahun)',
      nameLocation: 'middle',
      nameGap: 28,
      type: 'value',
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      name: 'Realisasi Retribusi (Juta Rp)',
      type: 'value',
      axisLabel: { fontSize: 10 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Kondisi Baik',
        type: 'scatter',
        data: data.filter((d) => d[4] === 'good'),
        symbolSize: (val) => Math.max(12, Math.min(48, Math.sqrt(val[2]) * 1.2)),
        itemStyle: {
          color: 'rgba(46, 158, 91, 0.75)',
          borderColor: '#2E9E5B',
          borderWidth: 1.5,
          shadowBlur: 6,
          shadowColor: 'rgba(46, 158, 91, 0.3)',
        },
      },
      {
        name: 'Perlu Waspada',
        type: 'scatter',
        data: data.filter((d) => d[4] === 'watch'),
        symbolSize: (val) => Math.max(12, Math.min(48, Math.sqrt(val[2]) * 1.2)),
        itemStyle: {
          color: 'rgba(224, 161, 0, 0.75)',
          borderColor: '#E0A100',
          borderWidth: 1.5,
          shadowBlur: 6,
          shadowColor: 'rgba(224, 161, 0, 0.3)',
        },
      },
      {
        name: 'Kondisi Kritis',
        type: 'scatter',
        data: data.filter((d) => d[4] === 'critical'),
        symbolSize: (val) => Math.max(12, Math.min(48, Math.sqrt(val[2]) * 1.2)),
        itemStyle: {
          color: 'rgba(214, 69, 69, 0.75)',
          borderColor: '#D64545',
          borderWidth: 1.5,
          shadowBlur: 6,
          shadowColor: 'rgba(214, 69, 69, 0.3)',
        },
      },
    ],
  }
})
</script>
