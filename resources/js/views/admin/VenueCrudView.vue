<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
          Kelola Sarana Prasarana Olahraga
        </h1>
        <p class="text-xs sm:text-sm text-muted mt-1">
          Master data aset venue, koordinat geografis, riwayat audit inspeksi, dan penerimaan retribusi.
        </p>
      </div>

      <button
        @click="openAddModal"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-900 hover:bg-brand-800 transition shadow-sm"
      >
        <Plus class="w-4 h-4" />
        <span>Tambah Sarana Baru</span>
      </button>
    </div>

    <!-- Data Table -->
    <DataTable
      :columns="columns"
      :rows="store.venues"
      :page-size="8"
    >
      <template #status="{ row }">
        <StatusBadge :status="row.status" :score="row.score" size="sm" />
      </template>

      <template #rowActions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <button
            @click="openEditModal(row)"
            class="p-1.5 text-muted hover:text-brand-900 hover:bg-gray-100 rounded-lg transition"
            title="Edit Venue"
          >
            <Pencil class="w-4 h-4" />
          </button>
          <button
            @click="deleteVenue(row)"
            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition"
            title="Hapus"
          >
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Modal Form Tambah/Edit Venue -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
      <div class="bg-surface rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
          <h3 class="font-bold text-base text-brand-900">
            {{ isEditing ? 'Edit Data Sarana Olahraga' : 'Tambah Sarana Olahraga Baru' }}
          </h3>
          <button @click="showModal = false" class="p-1 text-muted hover:text-text-main">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="saveVenue" class="space-y-4 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-text-main mb-1">Nama Sarana Olahraga *</label>
              <input
                v-model="form.name"
                required
                type="text"
                placeholder="Contoh: Stadion Gawalise"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Kecamatan *</label>
              <select
                v-model="form.district_name"
                required
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              >
                <option v-for="d in store.districts" :key="d.id" :value="d.name">
                  {{ d.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Tipe Sarana *</label>
              <select
                v-model="form.type_name"
                required
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              >
                <option v-for="t in store.venueTypes" :key="t.id" :value="t.name">
                  {{ t.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Kapasitas Penonton</label>
              <input
                v-model.number="form.capacity"
                type="number"
                placeholder="20000"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Status Publikasi</label>
              <select
                v-model="form.state"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              >
                <option value="draft">Draft</option>
                <option value="validated">Validated</option>
                <option value="published">Published</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Tipe Data</label>
              <select
                v-model="form.is_dummy"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              >
                <option :value="false">Data Riil (Terverifikasi)</option>
                <option :value="true">Data Simulasi (Dummy)</option>
              </select>
            </div>
          </div>

          <!-- Koordinat Picker Mini -->
          <div class="space-y-1.5 pt-2 border-t border-gray-100">
            <span class="block font-semibold text-text-main">
              Koordinat Lokasi di Palu (Latitude, Longitude)
            </span>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <input
                  v-model.number="form.latitude"
                  type="number"
                  step="0.000001"
                  placeholder="Latitude (-0.891700)"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs font-mono"
                />
              </div>
              <div>
                <input
                  v-model.number="form.longitude"
                  type="number"
                  step="0.000001"
                  placeholder="Longitude (119.870700)"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs font-mono"
                />
              </div>
            </div>
            <p class="text-[11px] text-muted">
              Pusat Kota Palu: Lat <code>-0.891700</code>, Long <code>119.870700</code>
            </p>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs font-semibold"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-800 text-white text-xs font-semibold"
            >
              Simpan Data
            </button>
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
import StatusBadge from '@/components/ui/StatusBadge.vue'
import { Plus, Pencil, Trash2, X } from 'lucide-vue-next'

const store = usePublicDataStore()

const columns = [
  { key: 'name', label: 'Nama Sarana' },
  { key: 'type_name', label: 'Tipe' },
  { key: 'district_name', label: 'Kecamatan' },
  { key: 'status', label: 'Kondisi' },
  { key: 'score', label: 'Skor' },
  { key: 'state', label: 'Validasi' },
  { key: 'is_dummy', label: 'Tipe Data' },
]

const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
  id: null,
  name: '',
  district_name: 'Palu Timur',
  type_name: 'Gelanggang Olahraga (GOR)',
  capacity: 1000,
  latitude: -0.891700,
  longitude: 119.870700,
  score: 80,
  status: 'good',
  state: 'published',
  is_dummy: false,
})

const openAddModal = () => {
  isEditing.value = false
  form.id = null
  form.name = ''
  form.district_name = 'Palu Timur'
  form.type_name = 'Gelanggang Olahraga (GOR)'
  form.capacity = 1000
  form.latitude = -0.891700
  form.longitude = 119.870700
  form.score = 75
  form.status = 'good'
  form.state = 'published'
  form.is_dummy = false
  showModal.value = true
}

const openEditModal = (row) => {
  isEditing.value = true
  Object.assign(form, row)
  showModal.value = true
}

const saveVenue = () => {
  if (isEditing.value) {
    const idx = store.venues.findIndex((v) => v.id === form.id)
    if (idx !== -1) {
      store.venues[idx] = { ...form }
    }
  } else {
    const newId = store.venues.length + 1
    store.venues.unshift({
      ...form,
      id: newId,
    })
  }
  showModal.value = false
}

const deleteVenue = (row) => {
  if (confirm(`Apakah Anda yakin ingin menghapus data venue ${row.name}?`)) {
    const idx = store.venues.findIndex((v) => v.id === row.id)
    if (idx !== -1) {
      store.venues.splice(idx, 1)
    }
  }
}
</script>
