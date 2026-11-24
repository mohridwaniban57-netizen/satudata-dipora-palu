<template>
  <div class="w-full h-96">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const chartOption = computed(() => {
  const nodes = [
    // Source: Anggaran
    { name: 'APBD Pembinaan Olahraga' },

    // Middle 1: Klaster Cabang Olahraga
    { name: 'Cabor Bela Diri (Silat, Tinju, Karate)' },
    { name: 'Cabor Akuatik & Terukur (Atletik, Renang)' },
    { name: 'Cabor Permainan Bola (Sepak Bola, Voli, Basket)' },
    { name: 'Cabor Akurasi (Panahan, Tenis Meja)' },

    // Middle 2: Jenjang Pembinaan
    { name: 'Jenjang Klub / Pemula' },
    { name: 'Jenjang Seleksi Kota' },
    { name: 'Puslatda Provinsi Sulteng' },
    { name: 'Pelatnas / Nasional' },

    // Target: Capaian Prestasi
    { name: 'Medali Emas' },
    { name: 'Medali Perak' },
    { name: 'Medali Perunggu' },
  ]

  const links = [
    // APBD to Cabor Clusters
    { source: 'APBD Pembinaan Olahraga', target: 'Cabor Bela Diri (Silat, Tinju, Karate)', value: 35 },
    { source: 'APBD Pembinaan Olahraga', target: 'Cabor Akuatik & Terukur (Atletik, Renang)', value: 45 },
    { source: 'APBD Pembinaan Olahraga', target: 'Cabor Permainan Bola (Sepak Bola, Voli, Basket)', value: 50 },
    { source: 'APBD Pembinaan Olahraga', target: 'Cabor Akurasi (Panahan, Tenis Meja)', value: 20 },

    // Cabor to Athlete Levels
    { source: 'Cabor Akuatik & Terukur (Atletik, Renang)', target: 'Puslatda Provinsi Sulteng', value: 25 },
    { source: 'Cabor Akuatik & Terukur (Atletik, Renang)', target: 'Pelatnas / Nasional', value: 12 },
    { source: 'Cabor Akuatik & Terukur (Atletik, Renang)', target: 'Jenjang Seleksi Kota', value: 8 },

    { source: 'Cabor Bela Diri (Silat, Tinju, Karate)', target: 'Puslatda Provinsi Sulteng', value: 18 },
    { source: 'Cabor Bela Diri (Silat, Tinju, Karate)', target: 'Pelatnas / Nasional', value: 10 },
    { source: 'Cabor Bela Diri (Silat, Tinju, Karate)', target: 'Jenjang Klub / Pemula', value: 7 },

    { source: 'Cabor Permainan Bola (Sepak Bola, Voli, Basket)', target: 'Jenjang Seleksi Kota', value: 25 },
    { source: 'Cabor Permainan Bola (Sepak Bola, Voli, Basket)', target: 'Puslatda Provinsi Sulteng', value: 15 },
    { source: 'Cabor Permainan Bola (Sepak Bola, Voli, Basket)', target: 'Jenjang Klub / Pemula', value: 10 },

    { source: 'Cabor Akurasi (Panahan, Tenis Meja)', target: 'Puslatda Provinsi Sulteng', value: 12 },
    { source: 'Cabor Akurasi (Panahan, Tenis Meja)', target: 'Jenjang Seleksi Kota', value: 8 },

    // Athlete Levels to Medals
    { source: 'Pelatnas / Nasional', target: 'Medali Emas', value: 16 },
    { source: 'Pelatnas / Nasional', target: 'Medali Perak', value: 6 },
    { source: 'Puslatda Provinsi Sulteng', target: 'Medali Emas', value: 6 },
    { source: 'Puslatda Provinsi Sulteng', target: 'Medali Perak', value: 7 },
    { source: 'Puslatda Provinsi Sulteng', target: 'Medali Perunggu', value: 8 },
    { source: 'Jenjang Seleksi Kota', target: 'Medali Perunggu', value: 2 },
  ]

  return {
    tooltip: {
      trigger: 'item',
      triggerOn: 'mousemove',
      formatter: (params) => {
        if (params.dataType === 'edge') {
          return `<div class="text-xs"><b>${params.data.source}</b> → <b>${params.data.target}</b>: ${params.data.value} unit/poin</div>`
        }
        return `<div class="text-xs font-bold">${params.name}</div>`
      },
    },
    series: [
      {
        type: 'sankey',
        layout: 'none',
        emphasis: { focus: 'adjacency' },
        nodeAlign: 'justify',
        nodeGap: 14,
        nodeWidth: 18,
        label: {
          fontSize: 10,
          color: '#1F2933',
        },
        lineStyle: {
          color: 'gradient',
          curveness: 0.5,
          opacity: 0.35,
        },
        data: nodes,
        links: links,
      },
    ],
  }
})
</script>