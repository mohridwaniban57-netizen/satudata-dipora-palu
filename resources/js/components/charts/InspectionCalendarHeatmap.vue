<template>
  <div class="w-full h-56 sm:h-64">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  year: {
    type: Number,
    default: 2026,
  },
})

const chartOption = computed(() => {
  // Generate sample date data for the year 2026
  function getVirtualData(year) {
    const date = +new Date(year, 0, 1)
    const end = +new Date(year + 1, 0, 1)
    const dayTime = 3600 * 24 * 1000
    const data = []
    for (let time = date; time < end; time += dayTime) {
      const d = new Date(time)
      const dayStr = [
        d.getFullYear(),
        String(d.getMonth() + 1).padStart(2, '0'),
        String(d.getDate()).padStart(2, '0'),
      ].join('-')

      // Activity simulation: inspections & maintenance peaks on weekdays
      const dayOfWeek = d.getDay()
      let activity = 0
      if (dayOfWeek >= 1 && dayOfWeek <= 5) {
        // Pseudo-random deterministic activity
        const seed = (d.getDate() * 17 + d.getMonth() * 31) % 100
        if (seed > 75) activity = Math.floor((seed - 75) / 5) + 1
      }
      data.push([dayStr, activity])
    }
    return data
  }

  const data = getVirtualData(2026)

  return {
    tooltip: {
      formatter: (p) => {
        const date = p.data[0]
        const count = p.data[1]
        return `<div class="text-xs">Tanggal: <b>${date}</b></div>
          <div class="text-xs">Aktivitas Inspeksi/Servis: <b>${count}</b> agenda</div>`
      },
    },
    visualMap: {
      min: 0,
      max: 5,
      type: 'piecewise',
      orient: 'horizontal',
      left: 'center',
      top: 0,
      textStyle: { fontSize: 10, color: '#64748B' },
      pieces: [
        { min: 0, max: 0, label: 'Tidak Ada', color: '#F1F5F9' },
        { min: 1, max: 1, label: '1 Inspeksi', color: '#BAE6FD' },
        { min: 2, max: 2, label: '2 Agenda', color: '#38BDF8' },
        { min: 3, max: 3, label: '3 Agenda', color: '#0284C7' },
        { min: 4, max: 5, label: '4-5 Agenda (Intensif)', color: '#0B3C5D' },
      ],
    },
    calendar: {
      top: 45,
      left: 30,
      right: 30,
      cellSize: ['auto', 13],
      range: '2026',
      itemStyle: {
        borderWidth: 1.5,
        borderColor: '#fff',
      },
      yearLabel: { show: false },
      dayLabel: {
        firstDay: 1,
        nameMap: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        fontSize: 9,
        color: '#64748B',
      },
      monthLabel: {
        nameMap: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        fontSize: 10,
        color: '#475569',
      },
    },
    series: {
      type: 'heatmap',
      coordinateSystem: 'calendar',
      data,
    },
  }
})
</script>
