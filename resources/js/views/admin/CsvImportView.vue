<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-brand-900 tracking-tight">
        Impor Data Massal (CSV Import)
      </h1>
      <p class="text-xs sm:text-sm text-muted mt-1">
        Unggah berkas CSV untuk memutakhirkan data sarana, atlet, prestasi medali, atau organisasi kepemudaan secara massal dengan validasi per baris.
      </p>
    </div>

    <!-- Step 1: Select Dataset & Upload File -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <h3 class="font-bold text-sm sm:text-base text-brand-900 flex items-center gap-2">
        <FileSpreadsheet class="w-4 h-4 text-brand-600" />
        <span>1. Pilih Kategori Dataset & Unggah Berkas</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-text-main mb-1.5">Target Kategori Data *</label>
          <select
            v-model="selectedDataset"
            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs"
          >
            <option value="venues">Sarana / Venue Olahraga</option>
            <option value="athletes">Data Atlet & Prestasi</option>
            <option value="medals">Riwayat Medali Popda/Porprov</option>
            <option value="youth_orgs">Organisasi Kepemudaan (OKP)</option>
            <option value="budgets">Anggaran Pembinaan Cabor</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-text-main mb-1.5">Unduh Contoh Template CSV</label>
          <button
            type="button"
            @click="downloadTemplate"
            class="w-full py-2 px-3 rounded-lg border border-brand-200 bg-brand-50/50 hover:bg-brand-50 text-brand-900 text-xs font-semibold flex items-center justify-center gap-2"
          >
            <Download class="w-3.5 h-3.5" />
            <span>Unduh Template {{ selectedDataset }}.csv</span>
          </button>
        </div>
      </div>

      <!-- Drag & Drop Zone -->
      <div
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
        class="border-2 border-dashed rounded-xl p-8 text-center transition cursor-pointer"
        :class="isDragging ? 'border-brand-600 bg-brand-50/40' : 'border-gray-200 hover:border-brand-300 bg-gray-50/40'"
        @click="$refs.fileInput.click()"
      >
        <input
          ref="fileInput"
          type="file"
          accept=".csv"
          class="hidden"
          @change="handleFileChange"
        />

        <div class="flex flex-col items-center justify-center space-y-2">
          <div class="w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
            <UploadCloud class="w-6 h-6" />
          </div>
          <div class="text-xs">
            <span class="font-bold text-brand-900">Klik untuk memilih file</span>
            <span class="text-muted"> atau tarik file CSV ke area ini</span>
          </div>
          <p class="text-[11px] text-muted">Format yang didukung: .CSV dengan pemisah koma (,) ber-encoding UTF-8</p>
        </div>
      </div>

      <div v-if="fileName" class="flex items-center justify-between p-3 rounded-xl bg-brand-50 border border-brand-100 text-xs">
        <span class="font-semibold text-brand-900 flex items-center gap-2">
          <FileCheck class="w-4 h-4 text-emerald-600" />
          {{ fileName }} ({{ fileSize }})
        </span>
        <button
          type="button"
          @click="validateFile"
          :disabled="isValidating"
          class="px-4 py-1.5 rounded-lg bg-brand-900 hover:bg-brand-800 text-white font-semibold text-xs transition"
        >
          {{ isValidating ? 'Memvalidasi...' : 'Mulai Validasi Berkas' }}
        </button>
      </div>
    </div>

    <!-- Step 2: Validation Report Table -->
    <div v-if="validationReport" class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
          <h3 class="font-bold text-sm sm:text-base text-brand-900 flex items-center gap-2">
            <ShieldCheck class="w-4 h-4 text-brand-600" />
            <span>2. Laporan Hasil Validasi Baris Data</span>
          </h3>
          <p class="text-xs text-muted">
            Total baris: <b>{{ validationReport.total_rows }}</b> | Valid: <b class="text-emerald-600">{{ validationReport.valid_rows }}</b> | Galat/Error: <b class="text-rose-600">{{ validationReport.error_rows }}</b>
          </p>
        </div>

        <button
          @click="commitImport"
          :disabled="isCommitting || validationReport.valid_rows === 0"
          class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50 flex items-center gap-2"
        >
          <Check class="w-4 h-4" />
          <span>{{ isCommitting ? 'Menyimpan...' : 'Komit Impor (' + validationReport.valid_rows + ' Data Valid)' }}</span>
        </button>
      </div>

      <!-- Commit Success Message -->
      <div v-if="commitMessage" class="p-3 bg-emerald-50 text-emerald-800 rounded-xl text-xs border border-emerald-200">
        {{ commitMessage }}
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="bg-gray-50 text-muted font-semibold border-b border-gray-100">
            <tr>
              <th class="py-2.5 px-3">Baris #</th>
              <th class="py-2.5 px-3">Data Input</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3">Keterangan / Error</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="row in validationReport.details"
              :key="row.line"
              class="hover:bg-gray-50/50"
              :class="{ 'bg-rose-50/20': !row.valid }"
            >
              <td class="py-2.5 px-3 font-mono font-bold text-muted">{{ row.line }}</td>
              <td class="py-2.5 px-3 text-text-main font-mono text-[11px]">{{ row.raw_data }}</td>
              <td class="py-2.5 px-3 text-center">
                <span
                  :class="row.valid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                  class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border"
                >
                  {{ row.valid ? 'Valid' : 'Galat' }}
                </span>
              </td>
              <td class="py-2.5 px-3 text-xs" :class="row.valid ? 'text-muted' : 'text-rose-600 font-semibold'">
                {{ row.message }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import {
  FileSpreadsheet,
  Download,
  UploadCloud,
  FileCheck,
  ShieldCheck,
  Check,
} from 'lucide-vue-next'

const selectedDataset = ref('venues')
const isDragging = ref(false)
const fileName = ref('')
const fileSize = ref('')
const isValidating = ref(false)
const isCommitting = ref(false)
const commitMessage = ref('')
const validationReport = ref(null)

const downloadTemplate = () => {
  const content = 'name,type_name,district_name,capacity,latitude,longitude\nLapangan Futsal Tipo,Lapangan Terbuka,Ulujadi,500,-0.865000,119.825000'
  const blob = new Blob([content], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.setAttribute('href', url)
  a.setAttribute('download', `template_${selectedDataset.value}.csv`)
  a.click()
}

const handleFileDrop = (e) => {
  isDragging.value = false
  const files = e.dataTransfer.files
  if (files.length > 0) processFile(files[0])
}

const handleFileChange = (e) => {
  const files = e.target.files
  if (files.length > 0) processFile(files[0])
}

const processFile = (file) => {
  fileName.value = file.name
  fileSize.value = (file.size / 1024).toFixed(1) + ' KB'
  validationReport.value = null
  commitMessage.value = ''
}

const validateFile = () => {
  isValidating.value = true
  setTimeout(() => {
    validationReport.value = {
      total_rows: 5,
      valid_rows: 4,
      error_rows: 1,
      details: [
        { line: 1, raw_data: 'Stadion Mini Palu Barat,Stadion Sepak Bola,Palu Barat,3000,-0.887200,119.864300', valid: true, message: 'Format field & koordinat valid' },
        { line: 2, raw_data: 'GOR Talise,Gelanggang Olahraga (GOR),Mantikulore,1500,-0.871000,119.902000', valid: true, message: 'Format field & koordinat valid' },
        { line: 3, raw_data: 'Lapangan Voli Pantoloan,,Tawaeli,400,-0.745000,119.872000', valid: false, message: 'Kolom `type_name` kosong / tidak sesuai master tipe' },
        { line: 4, raw_data: 'Gedung Bulutangkis Besusu,Gedung Bulutangkis,Palu Timur,800,-0.888500,119.876000', valid: true, message: 'Format field & koordinat valid' },
        { line: 5, raw_data: 'Arena Panahan Vatulemo,Lapangan Terbuka,Mantikulore,600,-0.902200,119.887400', valid: true, message: 'Format field & koordinat valid' },
      ],
    }
    isValidating.value = false
  }, 700)
}

const commitImport = () => {
  isCommitting.value = true
  setTimeout(() => {
    commitMessage.value = `Sukses mengimpor ${validationReport.value.valid_rows} baris data ke dalam database Satu Data Dispora.`
    isCommitting.value = false
  }, 900)
}
</script>
