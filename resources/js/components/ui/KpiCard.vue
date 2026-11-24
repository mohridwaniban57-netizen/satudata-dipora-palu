<template>
  <div class="bg-surface rounded-2xl border border-gray-100/90 p-3.5 sm:p-4 shadow-2xs hover:shadow-md transition-all duration-200 relative overflow-hidden flex flex-col justify-between h-full group">
    <!-- Top Row: Title + Optional Icon/Badge -->
    <div class="flex items-start justify-between gap-1.5 min-w-0 mb-1.5">
      <div class="min-w-0 flex-1">
        <p
          class="text-[11px] sm:text-xs font-semibold text-muted uppercase tracking-wider whitespace-nowrap truncate"
          :title="title"
        >
          {{ title }}
        </p>
        <span
          v-if="dummy"
          class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-dummy-light text-dummy border border-dummy-border shrink-0 mt-0.5"
        >
          Simulasi
        </span>
      </div>

      <div
        v-if="icon"
        :class="iconBgClass"
        class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center shrink-0 shadow-2xs transition-transform duration-200 group-hover:scale-105"
      >
        <component :is="icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4" :class="iconColorClass" />
      </div>
    </div>

    <!-- Middle Row: Value (Always full width, never wrapping) -->
    <div class="my-auto py-1 sm:py-1.5 min-w-0 overflow-hidden">
      <div class="flex items-baseline gap-1 min-w-0 flex-nowrap">
        <h3
          class="text-xl sm:text-2xl font-extrabold text-text-main tracking-tight tabular-nums whitespace-nowrap leading-none truncate"
          :title="formattedValue"
        >
          {{ formattedValue }}
        </h3>
      </div>
    </div>

    <!-- Bottom Row: Subtitle / Trend -->
    <div
      v-if="subtitle || trend"
      class="mt-auto pt-1.5 flex items-center gap-1.5 text-[11px] sm:text-xs text-muted min-w-0 overflow-hidden border-t border-gray-50"
    >
      <span
        v-if="trend"
        :class="trendClass"
        class="font-semibold inline-flex items-center shrink-0 text-[11px]"
      >
        <component :is="trendIcon" class="w-3.5 h-3.5 mr-0.5" />
        {{ trend }}
      </span>
      <span
        class="truncate whitespace-nowrap text-muted/90 leading-tight"
        :title="subtitle"
      >
        {{ subtitle }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { TrendingUp, TrendingDown, Minus } from 'lucide-vue-next'

const props = defineProps({
  title: { type: String, required: true },
  value: { type: [Number, String], required: true },
  subtitle: { type: String, default: '' },
  icon: { type: Object, default: null },
  color: { type: String, default: 'brand' }, // brand, good, watch, critical, accent
  trend: { type: String, default: '' },
  trendDirection: { type: String, default: 'neutral' }, // 'up', 'down', 'neutral'
  dummy: { type: Boolean, default: false },
})

const formattedValue = computed(() => {
  if (typeof props.value === 'number') {
    return props.value.toLocaleString('id-ID')
  }
  return props.value
})

const iconBgClass = computed(() => {
  switch (props.color) {
    case 'good':
      return 'bg-status-good-bg text-status-good'
    case 'watch':
      return 'bg-status-watch-bg text-status-watch'
    case 'critical':
      return 'bg-status-critical-bg text-status-critical'
    case 'accent':
      return 'bg-amber-50 text-accent-500'
    default:
      return 'bg-brand-50 text-brand-600'
  }
})

const iconColorClass = computed(() => {
  switch (props.color) {
    case 'good':
      return 'text-status-good'
    case 'watch':
      return 'text-status-watch'
    case 'critical':
      return 'text-status-critical'
    case 'accent':
      return 'text-accent-500'
    default:
      return 'text-brand-600'
  }
})

const trendClass = computed(() => {
  if (props.trendDirection === 'up') return 'text-status-good'
  if (props.trendDirection === 'down') return 'text-status-critical'
  return 'text-muted'
})

const trendIcon = computed(() => {
  if (props.trendDirection === 'up') return TrendingUp
  if (props.trendDirection === 'down') return TrendingDown
  return Minus
})
</script>