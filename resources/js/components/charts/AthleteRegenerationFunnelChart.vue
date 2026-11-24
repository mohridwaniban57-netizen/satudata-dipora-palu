<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const chartOption = computed(() => {
  const data = [
    { value: 100, name: '1. Registrasi Atlet Muda Pemula', count: '342 atlet' },
    { value: 68, name: '2. Terseleksi Pembinaan PPLPD', count: '232 atlet' },
    { value: 42, name: '3. Kontingen Popda Kota Palu', count: '144 atlet' },
    { value: 24, name: '4. Skuad Utama Porprov Sulteng', count: '82 atlet' },
    { value: 14, name: '5. Peraih Medali Resmi', count: '48 atlet/tim' },
  ]

  return {
    tooltip: {
      trigger: 'item',
      formatter: (param) => {
        const d = param.data
        return `<div class="font-bold text-xs mb-1">${d.name}</div>
          <div class="text-xs">Jumlah: <b>${d.count}</b></div>
          <div class="text-xs text-brand-600 font-semibold">Tingkat Konversi: ${d.value}%</div>`
      },
    },
    legend: {
      bottom: '2%',
      left: 'center',
      icon: 'circle',
      textStyle: { fontSize: 10 },
    },
    color: ['#0B3C5D', '#1D6FA5', '#2585C4', '#F2A900', '#2E9E5B'],
    series: [
      {
        name: 'Piramida Regenerasi',
        type: 'funnel',
        left: '10%',
        top: 20,
        bottom: 60,
        width: '80%',
        min: 0,
        max: 100,
        minSize: '0%',
        maxSize: '100%',
        sort: 'descending',
        gap: 4,
        label: {
          show: true,
          position: 'inside',
          formatter: '{b}\n({c}%)',
          fontSize: 10,
          color: '#FFFFFF',
          fontWeight: 'bold',
        },
        labelLine: {
          length: 10,
          lineStyle: { width: 1, type: 'solid' },
        },
        itemStyle: {
          borderColor: '#fff',
          borderWidth: 2,
        },
        emphasis: {
          label: {
            fontSize: 12,
          },
        },
        data,
      },
    ],
  }
})
</script>