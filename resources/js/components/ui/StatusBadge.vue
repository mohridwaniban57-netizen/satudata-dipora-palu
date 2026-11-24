<template>
  <span
    :class="[badgeClasses, sizeClasses]"
    class="inline-flex items-center gap-1.5 font-medium rounded-full shrink-0"
  >
    <component :is="currentIcon" :class="iconSizeClass" />
    <span>{{ label }}</span>
    <span v-if="score !== null && score !== undefined" class="opacity-80 tabular-nums">
      ({{ score }})
    </span>
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { CheckCircle2, AlertTriangle, XCircle, HelpCircle } from 'lucide-vue-next'

const props = defineProps({
  status: { type: String, default: 'good' }, // 'good'|'watch'|'critical' | 'Baik'|'Waspada'|'Kritis'
  score: { type: [Number, String], default: null },
  size: { type: String, default: 'md' }, // 'sm', 'md', 'lg'
})

const normalizedStatus = computed(() => {
  if (props.score !== null && props.score !== undefined) {
    const s = Number(props.score)
    if (s >= 70) return 'good'
    if (s >= 45) return 'watch'
    return 'critical'
  }

  const st = (props.status || '').toLowerCase()
  if (st === 'good' || st === 'baik') return 'good'
  if (st === 'watch' || st === 'waspada') return 'watch'
  if (st === 'critical' || st === 'kritis') return 'critical'
  return 'unknown'
})

const label = computed(() => {
  switch (normalizedStatus.value) {
    case 'good':
      return 'Kondisi Baik'
    case 'watch':
      return 'Perlu Waspada'
    case 'critical':
      return 'Kritis / Butuh Perbaikan'
    default:
      return props.status || 'Tidak Diketahui'
  }
})

const currentIcon = computed(() => {
  switch (normalizedStatus.value) {
    case 'good':
      return CheckCircle2
    case 'watch':
      return AlertTriangle
    case 'critical':
      return XCircle
    default:
      return HelpCircle
  }
})

const badgeClasses = computed(() => {
  switch (normalizedStatus.value) {
    case 'good':
      return 'bg-status-good-bg text-status-good border border-emerald-200'
    case 'watch':
      return 'bg-status-watch-bg text-status-watch border border-amber-200'
    case 'critical':
      return 'bg-status-critical-bg text-status-critical border border-rose-200'
    default:
      return 'bg-gray-100 text-gray-700 border border-gray-200'
  }
})

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'px-2 py-0.5 text-xs'
    case 'lg':
      return 'px-3.5 py-1.5 text-sm'
    default:
      return 'px-2.5 py-1 text-xs'
  }
})

const iconSizeClass = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'w-3 h-3'
    case 'lg':
      return 'w-4 h-4'
    default:
      return 'w-3.5 h-3.5'
  }
})
</script>