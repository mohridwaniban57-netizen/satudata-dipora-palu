<template>
  <span
    :class="tagClasses"
    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold shrink-0 cursor-help"
    :title="tooltip"
  >
    <span class="w-1.5 h-1.5 rounded-full" :class="dotClass"></span>
    <span>Keyakinan: {{ displayLabel }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  level: { type: String, default: 'Sedang' }, // 'Tinggi'|'Sedang'|'Rendah' or 'high'|'medium'|'low'
  rSquared: { type: Number, default: null },
})

const normalizedLevel = computed(() => {
  const l = (props.level || '').toLowerCase()
  if (l.includes('tinggi') || l.includes('high')) return 'high'
  if (l.includes('sedang') || l.includes('medium')) return 'medium'
  return 'low'
})

const displayLabel = computed(() => {
  switch (normalizedLevel.value) {
    case 'high':
      return 'Tinggi'
    case 'medium':
      return 'Sedang'
    default:
      return 'Rendah'
  }
})

const tagClasses = computed(() => {
  switch (normalizedLevel.value) {
    case 'high':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200'
    case 'medium':
      return 'bg-amber-50 text-amber-800 border border-amber-200'
    default:
      return 'bg-rose-50 text-rose-700 border border-rose-200'
  }
})

const dotClass = computed(() => {
  switch (normalizedLevel.value) {
    case 'high':
      return 'bg-emerald-500'
    case 'medium':
      return 'bg-amber-500'
    default:
      return 'bg-rose-500'
  }
})

const tooltip = computed(() => {
  if (props.rSquared !== null) {
    return `Tingkat Keyakinan: ${displayLabel.value} (R² = ${props.rSquared.toFixed(2)})`
  }
  return `Tingkat Keyakinan: ${displayLabel.value}`
})
</script>