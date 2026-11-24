<template>
  <div class="flex flex-col items-center justify-center p-8 text-center bg-white rounded-xl border border-dashed border-gray-200 my-4">
    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-muted mb-3">
      <component :is="iconComponent" class="w-6 h-6 text-gray-400" />
    </div>
    <h4 class="text-sm font-semibold text-text-main">{{ title }}</h4>
    <p class="text-xs text-muted max-w-sm mt-1">{{ description }}</p>
    <div v-if="$slots.action" class="mt-4">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Inbox, FileSearch, AlertCircle } from 'lucide-vue-next'

const props = defineProps({
  title: { type: String, default: 'Tidak Ada Data' },
  description: { type: String, default: 'Belum ada data yang tersedia untuk kategori ini.' },
  type: { type: String, default: 'inbox' },
})

const iconComponent = computed(() => {
  if (props.type === 'search') return FileSearch
  if (props.type === 'error') return AlertCircle
  return Inbox
})
</script>
