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
  // Format scatter points: [visits / 1000, revenue / 1000000, venue_name, quadrant]
  const data = props.venues.map((v) => {
    const visitsK = (v.annual_visits || 0) / 1000
    const revenueM = (v.annual_revenue || 0) / 1000000
    return [visitsK, revenueM, v.name, v.quadrant || 'Umum', v.status]
  })

  // Midpoints for quadrant dividing lines
  const medianVisits = 15 // 15 ribu kunjungan
  const medianRevenue = 40 // 40 juta rupiah

  return {
    tooltip: {
      formatter: (param) => {
        const d = param.data
        return `<div class="font-bold text-xs mb-1">${d[2]}</div>
          <div class="text-xs">Kunjungan: <b>${(d[0] * 1000).toLocaleString('id-ID')}</b> orang/thn</div>
          <div class="text-xs">Retribusi: <b>Rp ${(d[1] * 1000000).toLocaleString('id-ID')}</b></div>
          <div class="mt-1 text-[11px] font-semibold text-brand-600">Klasifikasi: ${d[3]}</div>`
      },
    },
    grid: {
      left: '4%',
      right: '6%',
      bottom: '10%',
      top: '12%',
      containLabel: true,
    },
    xAxis: {
      name: 'Kunjungan (Ribu Orang/Tahun)',
      nameLocation: 'middle',
      nameGap: 30,
      type: 'value',
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      name: 'Realisasi Retribusi (Juta Rupiah)',
      type: 'value',
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    series: [
      {
        name: 'Fasilitas',
        type: 'scatter',
        symbolSize: 18,
        itemStyle: {
          color: (param) => {
            const quadrant = param.data[3]
            if (quadrant === 'Bintang') return '#2E9E5B' // Green
            if (quadrant === 'Berpotensi Bocor') return '#D64545' // Red
            if (quadrant === 'Niche') return '#1D6FA5' // Blue
            return '#E0A100' // Yellow/Orange
          },
          borderColor: '#ffffff',
          borderWidth: 2,
          shadowBlur: 4,
          shadowColor: 'rgba(0,0,0,0.15)',
        },
        markLine: {
          silent: true,
          lineStyle: { type: 'dashed', color: '#94A3B8', width: 1.5 },
          data: [
            { xAxis: medianVisits, label: { formatter: 'Batas Kunjungan (15k)' } },
            { yAxis: medianRevenue, label: { formatter: 'Batas Retribusi (40jt)' } },
          ],
        },
        data,
      },
    ],
  }
})
</script>