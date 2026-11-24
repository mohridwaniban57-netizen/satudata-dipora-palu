<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
          Kelola Organisasi Kepemudaan (OKP & Karang Taruna)
        </h1>
        <p class="text-xs sm:text-sm text-muted mt-1">
          Database legalitas organisasi pemuda, jumlah keanggotaan aktif, dan distribusi wilayah di 8 kecamatan Kota Palu.
        </p>
      </div>

      <button
        @click="openAddModal"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-900 hover:bg-brand-800 transition shadow-sm"
      >
        <Plus class="w-4 h-4" />
        <span>Tambah Organisasi Baru</span>
      </button>
    </div>

    <DataTable
      :columns="columns"
      :rows="orgsList"
      :page-size="8"
    >
      <template #rowActions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <button @click="openEditModal(row)" class="p-1.5 text-muted hover:text-brand-900 hover:bg-gray-100 rounded-lg">
            <Pencil class="w-4 h-4" />
          </button>
          <button @click="deleteOrg(row)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg">
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
      <div class="bg-surface rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
          <h3 class="font-bold text-base text-brand-900">{{ isEditing ? 'Edit Data Organisasi' : 'Tambah Organisasi Baru' }}</h3>
          <button @click="showModal = false" class="p-1 text-muted hover:text-text-main"><X class="w-5 h-5" /></button>
        </div>

        <form @submit.prevent="saveOrg" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-text-main mb-1">Nama Organisasi *</label>
            <input v-model="form.name" required type="text" placeholder="KNPI Kota Palu / Karang Taruna Duyu" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-text-main mb-1">Tipe Organisasi</label>
              <select v-model="form.type" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="OKP">OKP (Organisasi Kemasyarakatan Pemuda)</option>
                <option value="Karang Taruna">Karang Taruna</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Kecamatan</label>
              <select v-model="form.district_name" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option v-for="d in store.districts" :key="d.id" :value="d.name">{{ d.name }}</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Estimasi Anggota</label>
              <input v-model.number="form.members" type="number" min="0" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs" />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Tingkat Aktivitas</label>
              <select v-model="form.activity_level" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="Tinggi">Tinggi</option>
                <option value="Sedang">Sedang</option>
                <option value="Rendah">Rendah</option>
              </select>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
            <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs font-semibold">Batal</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-800 text-white text-xs font-semibold">Simpan Organisasi</button>
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
  { key: 'name', label: 'Nama Organisasi' },
  { key: 'type', label: 'Tipe' },
  { key: 'district_name', label: 'Kecamatan' },
  { key: 'members', label: 'Jumlah Anggota' },
  { key: 'activity_level', label: 'Aktivitas' },
  { key: 'state', label: 'Status' },
]

const orgsList = ref([
  { id: 1, name: 'Dewan Pengurus Daerah KNPI Kota Palu', type: 'OKP', district_name: 'Palu Timur', members: 450, activity_level: 'Tinggi', state: 'published', is_dummy: false },
  { id: 2, name: 'Karang Taruna Tanamodindi', type: 'Karang Taruna', district_name: 'Mantikulore', members: 180, activity_level: 'Tinggi', state: 'published', is_dummy: false },
  { id: 3, name: 'Himpunan Mahasiswa Islam (HMI) Cabang Palu', type: 'OKP', district_name: 'Palu Timur', members: 320, activity_level: 'Tinggi', state: 'published', is_dummy: false },
  { id: 4, name: 'Gerakan Pemuda Ansor Kota Palu', type: 'OKP', district_name: 'Palu Barat', members: 210, activity_level: 'Sedang', state: 'published', is_dummy: false },
  { id: 5, name: 'Karang Taruna Duyu', type: 'Karang Taruna', district_name: 'Tatanga', members: 120, activity_level: 'Sedang', state: 'published', is_dummy: false },
  { id: 6, name: 'Pemuda Muhammadiyah Palu', type: 'OKP', district_name: 'Palu Selatan', members: 190, activity_level: 'Tinggi', state: 'published', is_dummy: false },
  { id: 7, name: 'Karang Taruna Tipo', type: 'Karang Taruna', district_name: 'Ulujadi', members: 95, activity_level: 'Sedang', state: 'draft', is_dummy: true },
])

const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
  id: null,
  name: '',
  type: 'OKP',
  district_name: 'Palu Timur',
  members: 100,
  activity_level: 'Sedang',
  state: 'published',
})

const openAddModal = () => {
  isEditing.value = false
  form.id = null
  form.name = ''
  form.type = 'OKP'
  form.district_name = 'Palu Timur'
  form.members = 100
  form.activity_level = 'Sedang'
  form.state = 'published'
  showModal.value = true
}

const openEditModal = (row) => {
  isEditing.value = true
  Object.assign(form, row)
  showModal.value = true
}

const saveOrg = () => {
  if (isEditing.value) {
    const idx = orgsList.value.findIndex((o) => o.id === form.id)
    if (idx !== -1) orgsList.value[idx] = { ...form }
  } else {
    orgsList.value.unshift({ ...form, id: Date.now(), is_dummy: false })
  }
  showModal.value = false
}

const deleteOrg = (row) => {
  if (confirm(`Hapus data ${row.name}?`)) {
    orgsList.value = orgsList.value.filter((o) => o.id !== row.id)
  }
}
</script>
