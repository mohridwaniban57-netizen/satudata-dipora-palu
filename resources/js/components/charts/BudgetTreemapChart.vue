<template>
  <div class="w-full h-80 sm:h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  budgets: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  const data = [
    {
      name: 'Pembinaan Olahraga Prestasi',
      value: 650,
      children: [
        { name: 'Sepak Bola', value: 160 },
        { name: 'Atletik', value: 120 },
        { name: 'Renang', value: 110 },
        { name: 'Pencak Silat', value: 95 },
        { name: 'Bulu Tangkis', value: 85 },
        { name: 'Bola Voli', value: 80 },
      ],
    },
    {
      name: 'Pemberdayaan Kepemudaan & OKP',
      value: 350,
      children: [
        { name: 'Karang Taruna 8 Kecamatan', value: 140 },
        { name: 'Forum Pemuda Pelopor Palu', value: 80 },
        { name: 'Pelatihan Kewirausahaan Pemuda', value: 75 },
        { name: 'Paskibraka Kota Palu', value: 55 },
      ],
    },
    {
      name: 'Pemeliharaan Sarana Prasarana',
      value: 480,
      children: [
        { name: 'Perawatan Stadion Utama & Mini', value: 200 },
        { name: 'Perawatan Gedung Olahraga (GOR)', value: 150 },
        { name: 'Perawatan Kolam Renang', value: 80 },
        { name: 'Fasilitas Lapangan Terbuka', value: 50 },
      ],
    },
  ]

  return {
    tooltip: {
      formatter: (info) => {
        const val = info.value
        const treePathInfo = info.treePathInfo
        const treePath = []
        for (let i = 1; i < treePathInfo.length; i++) {
          treePath.push(treePathInfo[i].name)
        }
        return `<div class="font-bold text-xs mb-1">${treePath.join(' / ')}</div>
          <div class="text-xs">Alokasi: <b>Rp ${val.toLocaleString('id-ID')} Juta</b></div>`
      },
    },
    series: [
      {
        name: 'Alokasi Sumber Daya',
        type: 'treemap',
        visibleMin: 300,
        label: {
          show: true,
          formatter: '{b}\nRp {c} Jt',
          fontSize: 11,
        },
        upperLabel: {
          show: true,
          height: 25,
          color: '#0B3C5D',
          fontWeight: 'bold',
          fontSize: 11,
        },
        itemStyle: {
          borderColor: '#fff',
          borderWidth: 2,
          gapWidth: 2,
        },
        levels: [
          {
            itemStyle: {
              borderColor: '#64748B',
              borderWidth: 0,
              gapWidth: 3,
            },
          },
          {
            color: ['#0B3C5D', '#1D6FA5', '#F2A900', '#2E9E5B'],
            colorMappingBy: 'id',
            itemStyle: {
              gapWidth: 2,
            },
          },
          {
            colorSaturation: [0.35, 0.6],
            itemStyle: {
              gapWidth: 1,
            },
          },
        ],
        data,
      },
    ],
  }
})
</script>
