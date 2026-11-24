<template>
  <div class="bg-surface rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Header Controls -->
    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
      <div class="flex items-center gap-2 flex-1">
        <div class="relative flex-1 max-w-sm">
          <Search class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari data..."
            class="w-full pl-9 pr-3 py-1.5 text-xs sm:text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
          />
        </div>

        <select
          v-if="showStateFilter"
          v-model="selectedState"
          class="text-xs sm:text-sm rounded-lg border border-gray-200 px-3 py-1.5 bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
        >
          <option value="all">Semua Status Validasi</option>
          <option value="published">Published (Publik)</option>
          <option value="validated">Validated (Tervalidasi)</option>
          <option value="draft">Draft (Konsep)</option>
        </select>
      </div>

      <div class="flex items-center gap-2 justify-end">
        <slot name="actions" />
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-gray-50/70 border-b border-gray-100 text-muted font-semibold">
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3 cursor-pointer select-none hover:text-brand-900 transition-colors"
              @click="sortBy(col.key)"
            >
              <div class="flex items-center gap-1">
                <span>{{ col.label }}</span>
                <span v-if="sortKey === col.key" class="text-brand-600 font-bold">
                  {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </span>
              </div>
            </th>
            <th v-if="$slots.rowActions" class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="(row, idx) in paginatedRows"
            :key="row.id || idx"
            class="hover:bg-gray-50/50 transition-colors"
          >
            <td
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3 text-text-main"
            >
              <slot :name="col.key" :row="row" :value="row[col.key]">
                <template v-if="col.key === 'is_dummy'">
                  <span v-if="row.is_dummy" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-dummy-light text-dummy border border-dummy-border">
                    Dummy
                  </span>
                  <span v-else class="text-xs text-emerald-600 font-medium">Riil</span>
                </template>
                <template v-else-if="col.key === 'state'">
                  <span
                    :class="{
                      'bg-emerald-50 text-emerald-700 border-emerald-200': row.state === 'published',
                      'bg-blue-50 text-blue-700 border-blue-200': row.state === 'validated',
                      'bg-amber-50 text-amber-700 border-amber-200': row.state === 'draft',
                    }"
                    class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border uppercase tracking-wider"
                  >
                    {{ row.state }}
                  </span>
                </template>
                <template v-else>
                  {{ row[col.key] }}
                </template>
              </slot>
            </td>

            <td v-if="$slots.rowActions" class="px-4 py-3 text-right">
              <slot name="rowActions" :row="row" />
            </td>
          </tr>

          <tr v-if="filteredRows.length === 0">
            <td :colspan="columns.length + ($slots.rowActions ? 1 : 0)" class="p-8 text-center text-muted">
              Tidak ada data yang sesuai dengan filter pencarian.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-muted">
      <div>
        Menampilkan
        <span class="font-medium text-text-main">{{ startItem }}</span> -
        <span class="font-medium text-text-main">{{ endItem }}</span> dari
        <span class="font-medium text-text-main">{{ filteredRows.length }}</span> data
      </div>

      <div class="flex items-center gap-1">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Sebelumnya
        </button>
        <span class="px-2 font-medium text-text-main">{{ currentPage }} / {{ totalPages || 1 }}</span>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage >= totalPages"
          class="px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Berikutnya
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Search } from 'lucide-vue-next'

const props = defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, required: true },
  pageSize: { type: Number, default: 10 },
  showStateFilter: { type: Boolean, default: true },
})

const searchQuery = ref('')
const selectedState = ref('all')
const currentPage = ref(1)
const sortKey = ref('')
const sortOrder = ref('asc')

const sortBy = (key) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
}

const filteredRows = computed(() => {
  let list = [...props.rows]

  // State filter
  if (selectedState.value !== 'all') {
    list = list.filter((r) => r.state === selectedState.value)
  }

  // Search query
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter((r) => {
      return Object.values(r).some((val) => {
        if (val === null || val === undefined) return false
        return String(val).toLowerCase().includes(q)
      })
    })
  }

  // Sorting
  if (sortKey.value) {
    list.sort((a, b) => {
      let va = a[sortKey.value]
      let vb = b[sortKey.value]
      if (typeof va === 'string') va = va.toLowerCase()
      if (typeof vb === 'string') vb = vb.toLowerCase()
      if (va < vb) return sortOrder.value === 'asc' ? -1 : 1
      if (va > vb) return sortOrder.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return list
})

const totalPages = computed(() => Math.ceil(filteredRows.value.length / props.pageSize))

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * props.pageSize
  return filteredRows.value.slice(start, start + props.pageSize)
})

const startItem = computed(() => {
  if (filteredRows.value.length === 0) return 0
  return (currentPage.value - 1) * props.pageSize + 1
})

const endItem = computed(() => {
  return Math.min(currentPage.value * props.pageSize, filteredRows.value.length)
})
</script>