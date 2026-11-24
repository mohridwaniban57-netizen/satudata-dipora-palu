<template>
  <div>
    <!-- Floating Trigger Pill (Bottom Right) -->
    <button
      @click="isOpen = true"
      type="button"
      class="fixed bottom-5 right-5 z-40 bg-brand-900 hover:bg-brand-800 text-white px-3.5 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all flex items-center gap-2 border border-brand-700/60 group"
      title="Buka Daftar 25 Chart Analitik"
    >
      <span class="w-6 h-6 rounded-full bg-accent-500 text-brand-950 font-extrabold text-[11px] flex items-center justify-center shrink-0">
        25
      </span>
      <span class="text-xs font-bold tracking-wide hidden sm:inline">Katalog 25 Chart</span>
      <BarChart3 class="w-4 h-4 text-accent-400 group-hover:scale-110 transition-transform" />
    </button>

    <!-- Modal Dialog -->
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
      @click.self="isOpen = false"
    >
      <div class="bg-surface w-full max-w-4xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 bg-gradient-to-r from-brand-900 to-brand-800 text-white flex items-start justify-between gap-4">
          <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-brand-100 border border-white/20 mb-2">
              <span class="w-2 h-2 rounded-full bg-accent-400 animate-pulse"></span>
              Satu Data Dispora Kota Palu
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2.5">
              <span>Katalog 25 Visualisasi Data (Chart 1 - 25)</span>
            </h2>
            <p class="text-xs sm:text-sm text-brand-100/90 mt-1 max-w-2xl leading-relaxed">
              Daftar seluruh 25 format analitik grafis yang aktif dan terintegrasi di portal, mencakup prestasi olahraga, kepemudaan, dan optimalisasi aset.
            </p>
          </div>

          <button
            @click="isOpen = false"
            class="text-white/80 hover:text-white p-2 rounded-xl hover:bg-white/10 transition"
            aria-label="Tutup Dialog"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="p-4 sm:px-6 bg-gray-50/80 border-b border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
          <!-- Search -->
          <div class="relative flex-1">
            <Search class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari chart berdasarkan nama, tipe, atau sektor..."
              class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 outline-none"
            />
          </div>

          <!-- Sector Filter -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs">
            <button
              v-for="cat in categories"
              :key="cat.id"
              @click="selectedCategory = cat.id"
              class="px-2.5 py-1 rounded-lg font-medium whitespace-nowrap transition text-xs"
              :class="selectedCategory === cat.id ? 'bg-brand-900 text-white shadow-xs font-semibold' : 'bg-white text-muted hover:text-brand-900 border border-gray-200'"
            >
              {{ cat.label }}
            </button>
          </div>
        </div>

        <!-- Chart List Table / Grid -->
        <div class="p-4 sm:p-6 overflow-y-auto flex-1 space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div
              v-for="item in filteredCharts"
              :key="item.number"
              class="p-3.5 rounded-xl border border-gray-100 hover:border-brand-300 hover:bg-brand-50/20 transition-all flex flex-col justify-between gap-2.5 bg-white shadow-2xs"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-brand-900 text-white shrink-0">
                    Chart {{ item.number }}
                  </span>
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-brand-900 border border-gray-200">
                    {{ item.type }}
                  </span>
                </div>
                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full" :class="item.sectorClass">
                  {{ item.sector }}
                </span>
              </div>

              <div>
                <h4 class="font-bold text-xs sm:text-sm text-brand-900 line-clamp-1">
                  {{ item.title }}
                </h4>
                <p class="text-[11px] text-muted line-clamp-2 mt-0.5 leading-snug">
                  {{ item.description }}
                </p>
              </div>

              <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                <span class="text-muted text-[11px] flex items-center gap-1">
                  <FileText class="w-3 h-3 text-brand-600" />
                  {{ item.pageName }}
                </span>
                <router-link
                  :to="item.link"
                  @click="isOpen = false"
                  class="font-semibold text-brand-600 hover:text-brand-900 flex items-center gap-1 group text-[11px]"
                >
                  <span>Buka Chart</span>
                  <ArrowRight class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
                </router-link>
              </div>
            </div>
          </div>

          <div v-if="filteredCharts.length === 0" class="text-center py-10 text-muted text-xs">
            Tidak ada chart yang cocok dengan pencarian "{{ searchQuery }}".
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3.5 sm:px-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-muted">
          <span>Menampilkan {{ filteredCharts.length }} dari 25 visualisasi aktif</span>
          <button
            @click="isOpen = false"
            type="button"
            class="px-4 py-1.5 rounded-xl bg-gray-200 hover:bg-gray-300 font-semibold text-text-main transition text-xs"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { BarChart3, X, Search, FileText, ArrowRight } from 'lucide-vue-next'

const isOpen = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('all')

const categories = [
  { id: 'all', label: 'Semua (25)' },
  { id: 'olahraga', label: 'Prestasi Olahraga' },
  { id: 'aset', label: 'Optimalisasi Aset' },
  { id: 'pemuda', label: 'Kepemudaan' },
  { id: 'prediksi', label: 'Prediktif OLS' }
]

const chartsList = [
  {
    number: 1,
    type: 'Bar Chart',
    title: 'Distribusi Atlet per Cabang Olahraga',
    description: 'Visualisasi bar horizontal jumlah atlet binaan berprestasi PPLPD di 10 cabang olahraga unggulan.',
    sector: 'Prestasi Olahraga',
    sectorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    category: 'olahraga',
    pageName: 'Statistik Atlet & Pemuda',
    link: '/athletes#chart-1'
  },
  {
    number: 2,
    type: 'Grouped Bar Chart',
    title: 'Komparasi Poin Riil vs Proyeksi Model OLS',
    description: 'Grouped bar membandingkan poin capaian medali riil Popda 2024 vs hasil estimasi regresi linier.',
    sector: 'Prediktif OLS',
    sectorClass: 'bg-purple-50 text-purple-700 border border-purple-200',
    category: 'prediksi',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-2'
  },
  {
    number: 3,
    type: 'Column Chart',
    title: 'Tren Kunjungan & Retribusi Bulanan Venue',
    description: 'Grafik kolom bulanan volume pemanfaatan masyarakat dan kontribusi PAD sewa sarana olahraga.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Detail Fasilitas',
    link: '/venues/1#chart-3'
  },
  {
    number: 4,
    type: 'Stacked Column Chart',
    title: 'Distribusi Kondisi Fasilitas per Kecamatan',
    description: 'Stacked column sebaran status fisik sarana (Baik, Waspada, Kritis) di 8 kecamatan Kota Palu.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Beranda Eksekutif',
    link: '/#chart-4'
  },
  {
    number: 5,
    type: 'Lollipop Chart',
    title: 'Top 10 Kapasitas Fasilitas Olahraga',
    description: 'Grafik stem-and-bubble lollipop memeringkat 10 sarana olahraga dengan daya tampung penonton terbesar.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-5'
  },
  {
    number: 6,
    type: 'Radar Chart',
    title: 'Evaluasi Multi-Dimensi Daya Saing Cabor Unggulan',
    description: 'Pemetaan 6 dimensi (Rasio Emas, Kuantitas Atlet, Anggaran, Putri, Konsistensi, Ketersediaan Sarana).',
    sector: 'Prestasi Olahraga',
    sectorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    category: 'olahraga',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-6'
  },
  {
    number: 7,
    type: 'Histogram',
    title: 'Distribusi Frekuensi Skor Kondisi Fisik Fasilitas',
    description: 'Histogram binning sebaran skor kondisi aset untuk mendeteksi tendensi keausan sarana Dispora.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-7'
  },
  {
    number: 8,
    type: 'Scatter Plot',
    title: 'Matriks 4 Kuadran Utilisasi vs Realisasi Retribusi',
    description: 'Scatter plot sumbu X (kunjungan) vs Y (retribusi PAD) untuk klasifikasi Bintang, Bocor, Niche, Idle.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-8'
  },
  {
    number: 9,
    type: 'Sankey Diagram',
    title: 'Alur Pembiayaan Anggaran ke Prestasi Medali',
    description: 'Sankey flow: Aliran dana Kas Daerah -> Cabor -> Jenjang Pembinaan -> Capaian Medali Emas/Perak/Perunggu.',
    sector: 'Kepemudaan & Prestasi',
    sectorClass: 'bg-amber-50 text-amber-700 border border-amber-200',
    category: 'pemuda',
    pageName: 'Statistik Atlet & Pemuda',
    link: '/athletes#chart-9'
  },
  {
    number: 10,
    type: 'Area Chart',
    title: 'Tren Akumulasi Retribusi Bulanan vs Target',
    description: 'Area chart kurva laju pencapaian kumulatif target penerimaan PAD sarana olahraga per bulan.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-10'
  },
  {
    number: 11,
    type: 'Donut Chart',
    title: 'Komposisi Jenis Sarana Olahraga',
    description: 'Proporsi ragam sarana olahraga Dispora: Stadion, Lapangan Sepak Bola, GOR, Tenis, Beladiri, Kolam.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Beranda Eksekutif',
    link: '/#chart-11'
  },
  {
    number: 12,
    type: 'Treemap Chart',
    title: 'Peta Alokasi Anggaran Pembinaan Dispora',
    description: 'Hierarki proporsional alokasi dana pembinaan cabor, sarana prasarana, dan organisasi kepemudaan.',
    sector: 'Kepemudaan & Prestasi',
    sectorClass: 'bg-amber-50 text-amber-700 border border-amber-200',
    category: 'pemuda',
    pageName: 'Statistik Atlet & Pemuda',
    link: '/athletes#chart-12'
  },
  {
    number: 13,
    type: '100% Stacked Bar Chart',
    title: 'Komposisi Jenjang Pembinaan Atlet per Cabor',
    description: 'Proporsi skala 100% tingkatan atlet: Tingkat Klub, Tingkat Daerah, Tingkat Provinsi, Tingkat Nasional.',
    sector: 'Prestasi Olahraga',
    sectorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    category: 'olahraga',
    pageName: 'Statistik Atlet & Pemuda',
    link: '/athletes#chart-13'
  },
  {
    number: 14,
    type: 'Funnel Chart',
    title: 'Pipeline Regenerasi & Konversi Atlet Binaan',
    description: 'Corong seleksi berjenjang: Total Atlet Terdata -> Jenjang Daerah -> Provinsi -> Kontingen Nasional -> Juara.',
    sector: 'Prestasi Olahraga',
    sectorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    category: 'olahraga',
    pageName: 'Statistik Atlet & Pemuda',
    link: '/athletes#chart-14'
  },
  {
    number: 15,
    type: 'Calendar Heatmap',
    title: 'Kalender Aktivitas Inspeksi & Pemeliharaan',
    description: 'Matriks kalender harian sepanjang tahun untuk memantau intensitas inspeksi fisik berkala fasilitas.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Detail Fasilitas',
    link: '/venues/1#chart-15'
  },
  {
    number: 16,
    type: 'Choropleth Map',
    title: 'Peta Tematik Poligon Kepadatan Sarana Kecamatan',
    description: 'Peta gradasi warna poligon batas wilayah 8 kecamatan berdasarkan rasio fasilitas per 10.000 jiwa.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Peta Fasilitas',
    link: '/map'
  },
  {
    number: 17,
    type: 'Point Map',
    title: 'Peta Titik Sebaran Sarana Olahraga',
    description: 'Peta sebaran koordinat presisi fasilitas olahraga berkode warna kelaikan kondisi fisik di Kota Palu.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Beranda & Peta',
    link: '/#chart-17'
  },
  {
    number: 18,
    type: 'Bubble Map',
    title: 'Peta Proporsi Kapasitas Sarana Olahraga',
    description: 'Peta interaktif dengan radius lingkaran bubble proporsional terhadap kapasitas penonton venue.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Peta Fasilitas',
    link: '/map'
  },
  {
    number: 19,
    type: 'Stacked Bar Chart',
    title: 'Demografi Kelompok Usia & Gender (Piramida)',
    description: 'Piramida penduduk atlet terbagi bar horizontal putra dan putri berdasarkan kelompok rentang usia.',
    sector: 'Prestasi Olahraga',
    sectorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    category: 'olahraga',
    pageName: 'Statistik Atlet & Pemuda',
    link: '/athletes#chart-19'
  },
  {
    number: 20,
    type: 'Heatmap',
    title: 'Peta Densitas Intensitas Kunjungan Publik',
    description: 'Layer heatmap termal sebaran konsentrasi intensitas utilisasi masyarakat pada sarana olahraga.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Peta Fasilitas',
    link: '/map'
  },
  {
    number: 21,
    type: 'Bullet Chart',
    title: 'Target vs Realisasi Retribusi (Bullet Chart)',
    description: 'Grafik performa bullet chart benchmark realisasi PAD retribusi terhadap target batas minimum & moderat.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Beranda Eksekutif',
    link: '/#chart-21'
  },
  {
    number: 22,
    type: 'Waterfall Chart',
    title: 'Dekomposisi Penurunan Skor Fisik Fasilitas',
    description: 'Waterfall chart membedah faktor depresiasi aset: Nilai Baru (100) -> Penalti Usia -> Defisit Inspeksi -> Insiden.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Detail Fasilitas',
    link: '/venues/1#chart-22'
  },
  {
    number: 23,
    type: 'Line Chart',
    title: 'Tren Perolehan Medali Kontingen Kota Palu',
    description: 'Grafik garis time-series rekam jejak konsistensi medali kontingen Palu di Popda & Porprov Sulteng.',
    sector: 'Prestasi Olahraga',
    sectorClass: 'bg-blue-50 text-blue-700 border border-blue-200',
    category: 'olahraga',
    pageName: 'Beranda Eksekutif',
    link: '/#chart-23'
  },
  {
    number: 24,
    type: 'Bubble Chart',
    title: 'Korelasi 3D: Kunjungan, Retribusi & Kapasitas',
    description: 'Analisis korelasi 3 sumbu: X=Kunjungan (ribu), Y=Retribusi (Juta Rp), Ukuran Bubble=Kapasitas Penonton.',
    sector: 'Optimalisasi Aset',
    sectorClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    category: 'aset',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-24'
  },
  {
    number: 25,
    type: 'Error Bar Chart',
    title: 'Interval Ketidakpastian Proyeksi Medali OLS',
    description: 'Proyeksi poin medali cabor Popda 2026 dilengkapi margin galat toleransi ketidakpastian (Confidence Interval).',
    sector: 'Prediktif OLS',
    sectorClass: 'bg-purple-50 text-purple-700 border border-purple-200',
    category: 'prediksi',
    pageName: 'Prediksi & Wawasan',
    link: '/predictions#chart-25'
  }
]

const filteredCharts = computed(() => {
  return chartsList.filter((item) => {
    const matchesCat = selectedCategory.value === 'all' || item.category === selectedCategory.value
    const q = searchQuery.value.toLowerCase().trim()
    if (!q) return matchesCat

    const matchesQuery =
      item.number.toString().includes(q) ||
      item.type.toLowerCase().includes(q) ||
      item.title.toLowerCase().includes(q) ||
      item.sector.toLowerCase().includes(q) ||
      item.pageName.toLowerCase().includes(q)

    return matchesCat && matchesQuery
  })
})
</script>
