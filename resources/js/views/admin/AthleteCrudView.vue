<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
          Kelola Data Atlet & Prestasi Binaan
        </h1>
        <p class="text-xs sm:text-sm text-muted mt-1">
          Master data atlet terenkripsi. Sesuai hak akses admin, nama asli ditampilkan untuk keperluan pembinaan daerah.
        </p>
      </div>

      <button
        @click="openAddModal"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-900 hover:bg-brand-800 transition shadow-sm"
      >
        <Plus class="w-4 h-4" />
        <span>Registrasi Atlet Baru</span>
      </button>
    </div>

    <!-- Security Alert -->
    <div class="p-3 bg-blue-50 text-blue-900 rounded-xl text-xs border border-blue-200 flex items-center gap-2">
      <ShieldAlert class="w-4 h-4 text-blue-700 shrink-0" />
      <span>
        <strong>Mode Privasi Terproteksi:</strong> Nama asli di bawah ini terenkripsi AES-256 pada database server dan hanya didekripsi untuk sesi pengguna Administrator Dispora.
      </span>
    </div>

    <!-- Data Table -->
    <DataTable
      :columns="columns"
      :rows="athletesList"
      :page-size="8"
    >
      <template #rowActions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <button
            @click="openEditModal(row)"
            class="p-1.5 text-muted hover:text-brand-900 hover:bg-gray-100 rounded-lg transition"
            title="Edit Data Atlet"
          >
            <Pencil class="w-4 h-4" />
          </button>
          <button
            @click="deleteAthlete(row)"
            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition"
            title="Hapus"
          >
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Modal Form Tambah/Edit Atlet -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
      <div class="bg-surface rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
          <h3 class="font-bold text-base text-brand-900">
            {{ isEditing ? 'Edit Data Atlet Binaan' : 'Registrasi Atlet Baru' }}
          </h3>
          <button @click="showModal = false" class="p-1 text-muted hover:text-text-main">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="saveAthlete" class="space-y-4 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-text-main mb-1">Kode Atlet (Pseudonim) *</label>
              <input
                v-model="form.code"
                required
                type="text"
                placeholder="ATL-0101"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs font-mono bg-gray-50"
              />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Nama Lengkap (Dekripsi Admin) *</label>
              <input
                v-model="form.name_real"
                required
                type="text"
                placeholder="Nama asli atlet..."
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Cabang Olahraga *</label>
              <select
                v-model="form.sport_name"
                required
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs"
              >
                <option v-for="s in store.sports" :key="s.id" :value="s.name">
                  {{ s.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Kecamatan Domisili *</label>
              <select
                v-model="form.district_name"
                required
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs"
              >
                <option v-for="d in store.districts" :key="d.id" :value="d.name">
                  {{ d.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Jenis Kelamin</label>
              <select v-model="form.gender" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="L">Laki-Laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Tahun Lahir</label>
              <input
                v-model.number="form.birth_year"
                type="number"
                placeholder="2008"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs"
              />
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Tingkat Prestasi</label>
              <select v-model="form.level" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="Daerah">Tingkat Daerah / Kota</option>
                <option value="Provinsi">Tingkat Provinsi</option>
                <option value="Nasional">Tingkat Nasional</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-text-main mb-1">Status Publikasi</label>
              <select v-model="form.state" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs">
                <option value="draft">Draft</option>
                <option value="validated">Validated</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" id="consent" v-model="form.consent_recorded" class="rounded text-brand-600" />
            <label for="consent" class="text-xs text-text-main font-medium">
              Surat persetujuan pelindungan data anak (UU PDP) dari orang tua/wali telah diverifikasi.
            </label>
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
              Simpan Data Atlet
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
import { Plus, Pencil, Trash2, X, ShieldAlert } from 'lucide-vue-next'

const store = usePublicDataStore()

const columns = [
  { key: 'code', label: 'Kode Pseudonim' },
  { key: 'name_real', label: 'Nama Asli (Dekripsi)' },
  { key: 'sport_name', label: 'Cabang Olahraga' },
  { key: 'district_name', label: 'Kecamatan' },
  { key: 'gender', label: 'Gender' },
  { key: 'birth_year', label: 'Tahun Lahir' },
  { key: 'level', label: 'Tingkat' },
  { key: 'state', label: 'Status' },
]

const athletesList = ref([
  { id: 1, code: 'ATL-0001', name_real: 'Rahmat Hidayat', sport_name: 'Atletik', district_name: 'Palu Timur', gender: 'L', birth_year: 2007, level: 'Provinsi', state: 'published', is_dummy: false, consent_recorded: true },
  { id: 2, code: 'ATL-0002', name_real: 'Nur Fadilah', sport_name: 'Pencak Silat', district_name: 'Mantikulore', gender: 'P', birth_year: 2008, level: 'Provinsi', state: 'published', is_dummy: false, consent_recorded: true },
  { id: 3, code: 'ATL-0003', name_real: 'Mohammad Farhan', sport_name: 'Taekwondo', district_name: 'Palu Selatan', gender: 'L', birth_year: 2006, level: 'Nasional', state: 'published', is_dummy: false, consent_recorded: true },
  { id: 4, code: 'ATL-0004', name_real: 'Siti Rahmawati', sport_name: 'Bulu Tangkis', district_name: 'Palu Barat', gender: 'P', birth_year: 2009, level: 'Daerah', state: 'validated', is_dummy: false, consent_recorded: true },
  { id: 5, code: 'ATL-0005', name_real: 'Aldi Saputra', sport_name: 'Sepak Bola', district_name: 'Tatanga', gender: 'L', birth_year: 2007, level: 'Provinsi', state: 'published', is_dummy: false, consent_recorded: true },
  { id: 6, code: 'ATL-0006', name_real: 'Jessica Tania', sport_name: 'Karate', district_name: 'Ulujadi', gender: 'P', birth_year: 2008, level: 'Daerah', state: 'draft', is_dummy: true, consent_recorded: false },
  { id: 7, code: 'ATL-0007', name_real: 'Budi Santoso', sport_name: 'Renang', district_name: 'Palu Timur', gender: 'L', birth_year: 2010, level: 'Daerah', state: 'published', is_dummy: true, consent_recorded: true },
  { id: 8, code: 'ATL-0008', name_real: 'Fauzan Pratama', sport_name: 'Dayung', district_name: 'Tawaeli', gender: 'L', birth_year: 2006, level: 'Provinsi', state: 'published', is_dummy: false, consent_recorded: true },
])

const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
  id: null,
  code: '',
  name_real: '',
  sport_name: 'Atletik',
  district_name: 'Palu Timur',
  gender: 'L',
  birth_year: 2008,
  level: 'Provinsi',
  state: 'published',
  consent_recorded: true,
})

const openAddModal = () => {
  isEditing.value = false
  const nextNum = athletesList.value.length + 1
  form.id = null
  form.code = `ATL-00${nextNum < 10 ? '0' + nextNum : nextNum}`
  form.name_real = ''
  form.sport_name = 'Atletik'
  form.district_name = 'Palu Timur'
  form.gender = 'L'
  form.birth_year = 2008
  form.level = 'Provinsi'
  form.state = 'published'
  form.consent_recorded = true
  showModal.value = true
}

const openEditModal = (row) => {
  isEditing.value = true
  Object.assign(form, row)
  showModal.value = true
}

const saveAthlete = () => {
  if (isEditing.value) {
    const idx = athletesList.value.findIndex((a) => a.id === form.id)
    if (idx !== -1) {
      athletesList.value[idx] = { ...form }
    }
  } else {
    athletesList.value.unshift({
      ...form,
      id: Date.now(),
      is_dummy: false,
    })
  }
  showModal.value = false
}

const deleteAthlete = (row) => {
  if (confirm(`Hapus data atlet ${row.name_real} (${row.code})?`)) {
    athletesList.value = athletesList.value.filter((a) => a.id !== row.id)
  }
}
</script>
