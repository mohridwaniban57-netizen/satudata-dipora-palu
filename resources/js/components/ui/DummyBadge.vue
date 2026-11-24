<template>
  <span
    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-dummy-light text-dummy border border-dummy-border cursor-help transition-colors"
    :title="tooltipText"
  >
    <Sparkles class="w-3 h-3 text-dummy shrink-0" />
    <span>{{ label }}</span>
  </span>
</template>

<script setup>
import { Sparkles } from 'lucide-vue-next'

defineProps({
  label: { type: String, default: 'Data Simulasi' },
  tooltipText: {
    type: String,
    default: 'Data ini merupakan model estimasi/simulasi untuk pengujian analitik.',
  },
})
</script>