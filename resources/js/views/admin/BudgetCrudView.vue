<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
          Kelola Anggaran Pembinaan Olahraga (APBD)
        </h1>
        <p class="text-xs sm:text-sm text-muted mt-1">
          Alokasi dana pembinaan cabor, pemusatan latihan (Pelatda Popda), dan pengadaan sarana latihan atlet Kota Palu.
        </p>
      </div>

      <button
        @click="openAddModal"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-900 hover:bg-brand-800 transition shadow-sm"
      >
        <Plus class="w-4 h-4" />
        <span>Tambah Alokasi Anggaran</span>
      </button>
    </div>

    <DataTable
      :columns="columns"
      :rows="budgetsList"
      :page-size="8"
    >
      <template #amount="{ row }">
        <span class="font-bold font-mono text-brand-900">
          Rp {{ Number(row.amount).toLocaleString('id-ID') }}
        </span>
      </template>

      <template #rowActions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <button @click="openEditModal(row)" class="p-1.5 text-muted hover:text-brand-900 hover:bg-gray-100 rounded-lg">
            <Pencil class="w-4 h-4" />
          </button>
          <button @click="deleteBudget(row)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg">
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
      <div class="bg-surface rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
          <h3 class="font-bold text-base text-brand-900">{{ isEditing ? 'Edit Alokasi Anggaran' : 'Tambah Alokasi Anggaran' }}</h3>
          <button @click="showModal = false" class="p-1 text-muted hover:text-text-main"><X class="w-5 h-5" /></button>
        </div>

        <form @submit.prevent="saveBudget" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-text-main mb-1">Cabang Olahraga *</label>
            <select v-model="form.sport_name" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
              <option v-for="s in store.sports" :key="s.id" :value="s.name">{{ s.name }}</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-text-main mb-1">Tahun Anggaran *</label>
              <input v-model.number="form.year" required type="number" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Status</label>
              <select v-model="form.state" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="draft">Draft</option>
                <option value="validated">Validated</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-text-main mb-1">Besaran Anggaran (Rupiah) *</label>
            <input v-model.number="form.amount" required type="number" step="1000000" placeholder="75000000" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs font-mono" />
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs font-semibold">Batal</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-800 text-white text-xs font-semibold">Simpan Anggaran</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { usePublicDataStore } from '@/stores/publicData'
import DataTable from '@/components/ui/DataTable.vue'
import { Plus, Pencil, Trash2, X } from 'lucide-vue-next'

const store = usePublicDataStore()

const columns = [
  { key: 'sport_name', label: 'Cabang Olahraga' },
  { key: 'year', label: 'Tahun APBD' },
  { key: 'amount', label: 'Alokasi Anggaran' },
  { key: 'state', label: 'Status' },
]

const budgetsList = ref([
  { id: 1, sport_name: 'Atletik', year: 2024, amount: 95000000, state: 'published', is_dummy: false },
  { id: 2, sport_name: 'Pencak Silat', year: 2024, amount: 80000000, state: 'published', is_dummy: false },
  { id: 3, sport_name: 'Taekwondo', year: 2024, amount: 75000000, state: 'published', is_dummy: false },
  { id: 4, sport_name: 'Sepak Bola', year: 2024, amount: 140000000, state: 'published', is_dummy: false },
  { id: 5, sport_name: 'Bulu Tangkis', year: 2024, amount: 65000000, state: 'published', is_dummy: false },
  { id: 6, sport_name: 'Karate', year: 2024, amount: 60000000, state: 'published', is_dummy: false },
  { id: 7, sport_name: 'Dayung', year: 2024, amount: 85000000, state: 'published', is_dummy: false },
  { id: 8, sport_name: 'Renang', year: 2024, amount: 50000000, state: 'published', is_dummy: false },
])

const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
  id: null,
  sport_name: 'Atletik',
  year: 2024,
  amount: 50000000,
  state: 'published',
})

const openAddModal = () => {
  isEditing.value = false
  form.id = null
  form.sport_name = 'Atletik'
  form.year = 2024
  form.amount = 50000000
  form.state = 'published'
  showModal.value = true
}

const openEditModal = (row) => {
  isEditing.value = true
  Object.assign(form, row)
  showModal.value = true
}

const saveBudget = () => {
  if (isEditing.value) {
    const idx = budgetsList.value.findIndex((b) => b.id === form.id)
    if (idx !== -1) budgetsList.value[idx] = { ...form }
  } else {
    budgetsList.value.unshift({ ...form, id: Date.now(), is_dummy: false })
  }
  showModal.value = false
}

const deleteBudget = (row) => {
  if (confirm(`Hapus anggaran ${row.sport_name} tahun ${row.year}?`)) {
    budgetsList.value = budgetsList.value.filter((b) => b.id !== row.id)
  }
}
</script>
