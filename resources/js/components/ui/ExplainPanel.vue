<template>
  <div class="border border-brand-100 rounded-xl bg-brand-50/40 overflow-hidden my-4">
    <button
      @click="isOpen = !isOpen"
      type="button"
      class="w-full px-4 py-3 flex items-center justify-between text-left text-sm font-medium text-brand-900 hover:bg-brand-50 transition-colors focus:outline-none"
    >
      <div class="flex items-center gap-2">
        <HelpCircle class="w-4 h-4 text-brand-600 shrink-0" />
        <span>{{ title }}</span>
      </div>
      <ChevronDown
        class="w-4 h-4 text-brand-600 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
      />
    </button>

    <div v-show="isOpen" class="px-4 pb-4 pt-1 text-xs sm:text-sm text-text-main/80 space-y-2.5 border-t border-brand-100/60">
      <slot>
        <p v-if="formula" class="font-mono bg-white p-2.5 rounded-lg border border-brand-100 text-xs text-brand-900">
          <strong>Rumus:</strong> {{ formula }}
        </p>
        <p v-if="description">{{ description }}</p>
        <div v-if="assumptions && assumptions.length" class="mt-2">
          <strong class="text-xs uppercase tracking-wider text-muted block mb-1">Asumsi & Batasan:</strong>
          <ul class="list-disc list-inside space-y-1 text-xs text-muted">
            <li v-for="(item, idx) in assumptions" :key="idx">{{ item }}</li>
          </ul>
        </div>
      </slot>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { HelpCircle, ChevronDown } from 'lucide-vue-next'

defineProps({
  title: {
    type: String,
    default: 'Bagaimana ini dihitung? (Metodologi & Batasan)',
  },
  formula: {
    type: String,
    default: '',
  },
  description: {
    type: String,
    default: '',
  },
  assumptions: {
    type: Array,
    default: () => [],
  },
})

const isOpen = ref(false)
</script>