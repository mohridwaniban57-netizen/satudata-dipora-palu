<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
          Kelola Riwayat Medali & Prestasi Olahraga
        </h1>
        <p class="text-xs sm:text-sm text-muted mt-1">
          Catatan perolehan medali Pekan Olahraga Pelajar Daerah (Popda) dan Porprov kontingen Kota Palu.
        </p>
      </div>

      <button
        @click="openAddModal"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-900 hover:bg-brand-800 transition shadow-sm"
      >
        <Plus class="w-4 h-4" />
        <span>Tambah Riwayat Medali</span>
      </button>
    </div>

    <!-- Data Table -->
    <DataTable
      :columns="columns"
      :rows="medalsList"
      :page-size="8"
    >
      <template #rowActions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <button
            @click="openEditModal(row)"
            class="p-1.5 text-muted hover:text-brand-900 hover:bg-gray-100 rounded-lg transition"
          >
            <Pencil class="w-4 h-4" />
          </button>
          <button
            @click="deleteMedal(row)"
            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition"
          >
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
      <div class="bg-surface rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
          <h3 class="font-bold text-base text-brand-900">
            {{ isEditing ? 'Edit Riwayat Medali' : 'Tambah Riwayat Medali Baru' }}
          </h3>
          <button @click="showModal = false" class="p-1 text-muted hover:text-text-main"><X class="w-5 h-5" /></button>
        </div>

        <form @submit.prevent="saveMedal" class="space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="block font-semibold text-text-main mb-1">Nama Kejuaraan / Event *</label>
              <input v-model="form.event" required type="text" placeholder="Popda Sulteng 2024" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Cabang Olahraga *</label>
              <select v-model="form.sport_name" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option v-for="s in store.sports" :key="s.id" :value="s.name">{{ s.name }}</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Tahun Kejuaraan *</label>
              <input v-model.number="form.year" required type="number" placeholder="2024" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-amber-600 mb-1">Medali Emas</label>
              <input v-model.number="form.gold" type="number" min="0" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-gray-500 mb-1">Medali Perak</label>
              <input v-model.number="form.silver" type="number" min="0" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-amber-800 mb-1">Medali Perunggu</label>
              <input v-model.number="form.bronze" type="number" min="0" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Status Validasi</label>
              <select v-model="form.state" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="draft">Draft</option>
                <option value="validated">Validated</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs font-semibold">Batal</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-800 text-white text-xs font-semibold">Simpan Medali</button>
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
  { key: 'event', label: 'Event / Kejuaraan' },
  { key: 'sport_name', label: 'Cabang Olahraga' },
  { key: 'year', label: 'Tahun' },
  { key: 'gold', label: 'Emas' },
  { key: 'silver', label: 'Perak' },
  { key: 'bronze', label: 'Perunggu' },
  { key: 'state', label: 'Status' },
]

const medalsList = ref([
  { id: 1, event: 'Popda Sulteng 2024', sport_name: 'Atletik', year: 2024, gold: 6, silver: 4, bronze: 5, state: 'published', is_dummy: false },
  { id: 2, event: 'Popda Sulteng 2024', sport_name: 'Pencak Silat', year: 2024, gold: 4, silver: 5, bronze: 4, state: 'published', is_dummy: false },
  { id: 3, event: 'Popda Sulteng 2024', sport_name: 'Taekwondo', year: 2024, gold: 3, silver: 3, bronze: 4, state: 'published', is_dummy: false },
  { id: 4, event: 'Popda Sulteng 2024', sport_name: 'Dayung', year: 2024, gold: 3, silver: 2, bronze: 3, state: 'published', is_dummy: false },
  { id: 5, event: 'Porprov IX Banggai 2022', sport_name: 'Atletik', year: 2022, gold: 8, silver: 6, bronze: 9, state: 'published', is_dummy: false },
  { id: 6, event: 'Porprov IX Banggai 2022', sport_name: 'Karate', year: 2022, gold: 5, silver: 4, bronze: 6, state: 'published', is_dummy: false },
  { id: 7, event: 'Popda Sulteng 2021', sport_name: 'Bulu Tangkis', year: 2021, gold: 2, silver: 3, bronze: 3, state: 'published', is_dummy: false },
])

const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
  id: null,
  event: 'Popda Sulteng 2024',
  sport_name: 'Atletik',
  year: 2024,
  gold: 0,
  silver: 0,
  bronze: 0,
  state: 'published',
})

const openAddModal = () => {
  isEditing.value = false
  form.id = null
  form.event = 'Popda Sulteng 2024'
  form.sport_name = 'Atletik'
  form.year = 2024
  form.gold = 0
  form.silver = 0
  form.bronze = 0
  form.state = 'published'
  showModal.value = true
}

const openEditModal = (row) => {
  isEditing.value = true
  Object.assign(form, row)
  showModal.value = true
}

const saveMedal = () => {
  if (isEditing.value) {
    const idx = medalsList.value.findIndex((m) => m.id === form.id)
    if (idx !== -1) medalsList.value[idx] = { ...form }
  } else {
    medalsList.value.unshift({ ...form, id: Date.now() })
  }
  showModal.value = false
}

const deleteMedal = (row) => {
  if (confirm(`Hapus data medali event ${row.event} (${row.sport_name})?`)) {
    medalsList.value = medalsList.value.filter((m) => m.id !== row.id)
  }
}
</script>
