<template>
  <div class="w-full h-72 sm:h-80">
    <v-chart :option="chartOption" autoresize />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
})

const chartOption = computed(() => {
  const list = Array.isArray(props.data) && props.data.length > 0 ? props.data : [
    { group: '< 13 Thn', male: 22, female: 14 },
    { group: '13-15 Thn (SMP)', male: 56, female: 41 },
    { group: '16-18 Thn (SMA)', male: 84, female: 62 },
    { group: '19-22 Thn (Mhs)', male: 42, female: 21 },
  ]
  const groups = list.map((d) => d.group || d.age_group || '')
  const male = list.map((d) => typeof d.male === 'number' ? d.male : (typeof d.male_count === 'number' ? d.male_count : 0))
  const female = list.map((d) => typeof d.female === 'number' ? d.female : (typeof d.female_count === 'number' ? d.female_count : 0))

  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        let res = `<div class="font-bold text-xs mb-1">${params[0].axisValue}</div>`
        let total = 0
        params.forEach((p) => {
          total += p.value
          res += `<div class="flex items-center justify-between text-xs gap-3">
            <span>${p.marker} ${p.seriesName}</span>
            <span class="font-bold">${p.value} atlet</span>
          </div>`
        })
        res += `<div class="border-t border-gray-100 mt-1 pt-1 text-xs font-bold flex justify-between">
          <span>Total:</span><span>${total} atlet</span>
        </div>`
        return res
      },
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: { fontSize: 11 },
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '12%',
      top: '6%',
      containLabel: true,
    },
    xAxis: {
      type: 'value',
      axisLabel: { fontSize: 11 },
      splitLine: { lineStyle: { type: 'dashed', color: '#E2E8F0' } },
    },
    yAxis: {
      type: 'category',
      data: groups,
      axisLabel: { fontSize: 11 },
    },
    series: [
      {
        name: 'Atlet Putra',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#1D6FA5', borderRadius: [0, 0, 0, 0] },
        data: male,
      },
      {
        name: 'Atlet Putri',
        type: 'bar',
        stack: 'total',
        itemStyle: { color: '#F2A900', borderRadius: [0, 4, 4, 0] },
        data: female,
      },
    ],
  }
})
</script>