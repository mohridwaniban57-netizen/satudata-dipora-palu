<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm space-y-3">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-accent-50 text-accent-700 border border-accent-200">
            <Sparkles class="w-3.5 h-3.5" />
            <span>Prescriptive & Predictive Analytics Engine</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight mt-1">
            Prediksi & Wawasan Kebijakan Dispora
          </h1>
          <p class="text-xs sm:text-sm text-muted max-w-2xl">
            Model regresi linear OLS terkalibrasi untuk memproyeksikan performa medali, prioritas pemeliharaan aset berisiko, serta matriks efisiensi penerimaan retribusi.
          </p>
        </div>

        <DummyBadge label="Model Prediksi Aktif" />
      </div>

      <!-- Tab Navigation -->
      <div class="flex items-center gap-2 border-b border-gray-200 pt-4 overflow-x-auto">
        <button
          @click="activeTab = 'medals'"
          class="pb-3 px-4 text-xs sm:text-sm font-semibold border-b-2 transition whitespace-nowrap flex items-center gap-2"
          :class="activeTab === 'medals' ? 'border-brand-600 text-brand-900' : 'border-transparent text-muted hover:text-text-main'"
        >
          <Trophy class="w-4 h-4" />
          <span>Tab 1: Proyeksi Medali Cabor</span>
        </button>

        <button
          @click="activeTab = 'maintenance'"
          class="pb-3 px-4 text-xs sm:text-sm font-semibold border-b-2 transition whitespace-nowrap flex items-center gap-2"
          :class="activeTab === 'maintenance' ? 'border-brand-600 text-brand-900' : 'border-transparent text-muted hover:text-text-main'"
        >
          <Wrench class="w-4 h-4" />
          <span>Tab 2: Prioritas Pemeliharaan Fasilitas</span>
        </button>

        <button
          @click="activeTab = 'levy'"
          class="pb-3 px-4 text-xs sm:text-sm font-semibold border-b-2 transition whitespace-nowrap flex items-center gap-2"
          :class="activeTab === 'levy' ? 'border-brand-600 text-brand-900' : 'border-transparent text-muted hover:text-text-main'"
        >
          <Coins class="w-4 h-4" />
          <span>Tab 3: Insight Optimalisasi Retribusi</span>
        </button>
      </div>
    </div>

    <!-- TAB 1: PROYEKSI MEDALI -->
    <div v-if="activeTab === 'medals'" class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Chart (6 cols) - Chart 2: Grouped Bar Chart -->
        <div id="chart-2" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 2 • Grouped Bar Chart
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Perbandingan: Poin Riil vs Proyeksi</h3>
              <p class="text-xs text-muted">Komparasi poin riil Popda 2024 vs proyeksi model OLS per cabor</p>
            </div>
            <span class="text-xs text-muted">Bobot: E=3, P=2, B=1</span>
          </div>
          <MedalProjectionChart :projections="store.medalProjections" />
        </div>

        <!-- Tabel Proyeksi (6 cols) -->
        <div class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
          <h3 class="font-bold text-base text-brand-900">Tabel Estimasi Performa Cabang Olahraga</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
              <thead class="bg-gray-50 text-muted font-semibold border-b border-gray-100">
                <tr>
                  <th class="py-2.5 px-3">Cabang Olahraga</th>
                  <th class="py-2.5 px-3 text-center">Terakhir</th>
                  <th class="py-2.5 px-3 text-center">Proyeksi</th>
                  <th class="py-2.5 px-3 text-center">Rentang (Min-Max)</th>
                  <th class="py-2.5 px-3 text-center">Keyakinan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="p in store.medalProjections" :key="p.sport_id" class="hover:bg-gray-50/50">
                  <td class="py-2.5 px-3 font-semibold text-text-main">{{ p.sport_name }}</td>
                  <td class="py-2.5 px-3 text-center font-mono text-muted">{{ p.last_points }}</td>
                  <td class="py-2.5 px-3 text-center font-mono font-bold text-brand-900">{{ p.projected_points }}</td>
                  <td class="py-2.5 px-3 text-center font-mono text-xs text-muted">{{ p.min_points }} - {{ p.max_points }}</td>
                  <td class="py-2.5 px-3 text-center">
                    <ConfidenceTag :level="p.confidence" :r-squared="p.r_squared" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tab 1 Row 2: Error Bar Chart (#25) & Radar Chart (#6) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Error Bar Chart (6 cols) - Chart 25: Error Bar Chart -->
        <div id="chart-25" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 25 • Error Bar Chart
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Interval Ketidakpastian Proyeksi (Error Bar)</h3>
              <p class="text-xs text-muted">Rentang toleransi galat regresi (min-max) estimasi perolehan poin medali cabor</p>
            </div>
            <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
              Error Bar
            </span>
          </div>
          <MedalErrorBarChart :projections="store.medalProjections" />
        </div>

        <!-- Radar Chart (6 cols) - Chart 6: Radar Chart -->
        <div id="chart-6" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 6 • Radar Chart
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Multi-Dimensi Performa Cabor Unggulan (Radar)</h3>
              <p class="text-xs text-muted">Pemetaan 6 dimensi (Rasio Emas, Kuantitas Atlet, Anggaran, Putri, Konsistensi, Sarana)</p>
            </div>
            <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
              Radar Chart
            </span>
          </div>
          <SportPerformanceRadarChart />
        </div>
      </div>

      <ExplainPanel
        title="Bagaimana proyeksi medali ini dihitung?"
        formula="Poin_Proyeksi = α + β_1 × Riwayat_Poin_Historis + β_2 × Anggaran_Binaan + ε"
        description="Poin dihitung dengan skema bobot: Emas = 3 poin, Perak = 2 poin, Perunggu = 1 poin. Model OLS mengevaluasi tren konsistensi perolehan medali cabor dalam 3 event sebelumnya (Popda & Porprov) dan elastisitas dukungan dana pembinaan."
        :assumptions="[
          'Rentang estimasi (confidence interval 95%) dihitung berdasarkan standar deviasi galat residual model.',
          'Cabor dengan tingkat keyakinan Tinggi memiliki R² > 0.80.',
          'Perubahan regulasi kelas tanding atau mutasi atlet belum diperhitungkan dalam baseline murni.'
        ]"
      />
    </div>

    <!-- TAB 2: PRIORITAS PEMELIHARAAN -->
    <div v-if="activeTab === 'maintenance'" class="space-y-6">
      <!-- Tab 2 Row 1: Histogram (#7) & Lollipop Chart (#5) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Histogram (6 cols) - Chart 7: Histogram -->
        <div id="chart-7" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 7 • Histogram
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Distribusi Frekuensi Skor Sarana (Histogram)</h3>
              <p class="text-xs text-muted">Sebaran jumlah fasilitas berdasarkan rentang skor kelayakan fisik</p>
            </div>
            <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
              Histogram
            </span>
          </div>
          <VenueScoreHistogramChart :venues="store.venues" />
        </div>

        <!-- Lollipop Chart (6 cols) - Chart 5: Lollipop Chart -->
        <div id="chart-5" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 5 • Lollipop Chart
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Top 10 Kapasitas Fasilitas (Lollipop Chart)</h3>
              <p class="text-xs text-muted">Perbandingan kapasitas daya tampung penonton sarana olahraga terbesar</p>
            </div>
            <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
              Lollipop Chart
            </span>
          </div>
          <VenueCapacityLollipopChart :venues="store.venues" />
        </div>
      </div>

      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Wrench class="w-4 h-4 text-brand-600" />
              <span>Daftar Rangking Risiko Kerusakan Aset Olahraga</span>
            </h3>
            <p class="text-xs text-muted">Diurutkan dari fasilitas dengan urgensi intervensi anggaran perbaikan tertinggi</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead class="bg-gray-50 text-muted font-semibold border-b border-gray-100">
              <tr>
                <th class="py-3 px-3">Peringkat</th>
                <th class="py-3 px-3">Nama Sarana Olahraga</th>
                <th class="py-3 px-3">Kecamatan</th>
                <th class="py-3 px-3">Tipe</th>
                <th class="py-3 px-3 text-center">Skor Kondisi</th>
                <th class="py-3 px-3 text-center">Status</th>
                <th class="py-3 px-3">Estimasi Tanggal Servis</th>
                <th class="py-3 px-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr
                v-for="(venue, index) in sortedMaintenanceVenues"
                :key="venue.id"
                class="hover:bg-gray-50/50"
              >
                <td class="py-3 px-3 font-bold text-muted">{{ index + 1 }}</td>
                <td class="py-3 px-3 font-bold text-text-main">{{ venue.name }}</td>
                <td class="py-3 px-3 text-muted">{{ venue.district_name }}</td>
                <td class="py-3 px-3 text-muted">{{ venue.type_name }}</td>
                <td class="py-3 px-3 text-center font-mono font-bold">{{ venue.score }} / 100</td>
                <td class="py-3 px-3 text-center">
                  <StatusBadge :status="venue.status" :score="venue.score" size="sm" />
                </td>
                <td class="py-3 px-3 text-muted font-mono">{{ venue.next_maintenance || 'Segera' }}</td>
                <td class="py-3 px-3 text-right">
                  <router-link
                    :to="'/venues/' + venue.id"
                    class="text-brand-600 hover:text-brand-900 font-semibold"
                  >
                    Profil →
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <ExplainPanel
        title="Bagaimana urutan prioritas pemeliharaan ini diranking?"
        formula="Indeks_Risiko = (100 - Skor_Kondisi) × Faktor_Pemanfaatan_Masyarakat"
        description="Fasilitas dengan skor kondisi rendah yang memiliki intensitas kunjungan publik tinggi memperoleh bobot risiko kerusakan terbesar karena menyangkut keselamatan atlet dan masyarakat pengguna."
        :assumptions="[
          'Skor < 45 menandakan kondisi kritis dan butuh renovasi fisik struktural.',
          'Estimasi tanggal pemeliharaan dihitung berdasarkan laju penurunan mutu fasilitas (degradation rate) per bulan.'
        ]"
      />
    </div>

    <!-- TAB 3: INSIGHT RETRIBUSI (4 KUADRAN) -->
    <div v-if="activeTab === 'levy'" class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Scatter Plot Matrix (7 cols) - Chart 8: Scatter Plot -->
        <div id="chart-8" class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 8 • Scatter Plot
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Matriks Kuadran Utilisasi vs Realisasi Retribusi</h3>
              <p class="text-xs text-muted">Pemetaan 4 kuadran pemanfaatan publik (X) vs realisasi pendapatan retribusi (Y)</p>
            </div>
            <span class="text-xs text-muted">Analisis PAD</span>
          </div>

          <LevyQuadrantChart :venues="store.venues" />
        </div>

        <!-- Rekomendasi Aksi Konkret (5 cols) -->
        <div class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
          <h3 class="font-bold text-base text-brand-900">Pedoman Kebijakan 4 Kuadran</h3>

          <div class="space-y-3 text-xs">
            <!-- Kuadran 1 -->
            <div class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 space-y-1">
              <span class="font-bold text-emerald-800 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-status-good"></span>
                Kuadran I: Bintang (Kunjungan Tinggi, Retribusi Tinggi)
              </span>
              <p class="text-emerald-900/80 leading-relaxed">
                Contoh: <b>GOR Siranindi, Stadion Gawalise</b>. Rekomendasi: Pertahankan standar perawatan preventif dan terapkan sistem reservasi digital mandiri.
              </p>
            </div>

            <!-- Kuadran 2 -->
            <div class="p-3 rounded-xl border border-rose-200 bg-rose-50/50 space-y-1">
              <span class="font-bold text-rose-800 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-status-critical"></span>
                Kuadran II: Berpotensi Bocor (Kunjungan Tinggi, Retribusi Rendah)
              </span>
              <p class="text-rose-900/80 leading-relaxed">
                Contoh: <b>Kolam Renang Madamba</b>. Rekomendasi: Perketat sistem ticketing/e-retribusi, audit rekonsiliasi karcis masuk, dan cegah kebocoran penerimaan kas daerah.
              </p>
            </div>

            <!-- Kuadran 3 -->
            <div class="p-3 rounded-xl border border-blue-200 bg-blue-50/50 space-y-1">
              <span class="font-bold text-blue-800 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-600"></span>
                Kuadran III: Niche / Fasilitas Tertentu (Kunjungan Rendah, Retribusi Tinggi)
              </span>
              <p class="text-blue-900/80 leading-relaxed">
                Contoh: <b>Sirkuit Panggona</b>. Rekomendasi: Tarif sewa premium untuk kejuaraan profesional dan event khusus akhir pekan.
              </p>
            </div>

            <!-- Kuadran 4 -->
            <div class="p-3 rounded-xl border border-amber-200 bg-amber-50/50 space-y-1">
              <span class="font-bold text-amber-800 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-status-watch"></span>
                Kuadran IV: Kurang Pemanfaatan (Kunjungan Rendah, Retribusi Rendah)
              </span>
              <p class="text-amber-900/80 leading-relaxed">
                Contoh: <b>Lapangan Sepak Bola Pantoloan, GOR Madani Mamboro</b>. Rekomendasi: Program aktivasi turnamen antardesa/kelurahan atau kolaborasi bersama Karang Taruna setempat.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 3 Row 2: 3D Bubble Chart (#24) & Cumulative Area Chart (#10) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Bubble Chart 3D (6 cols) - Chart 24: Bubble Chart -->
        <div id="chart-24" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 24 • Bubble Chart
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Korelasi 3D: Kunjungan, Retribusi & Kapasitas</h3>
              <p class="text-xs text-muted">Bubble Chart: X=Kunjungan (ribu), Y=Retribusi (Jt Rp), Ukuran=Kapasitas</p>
            </div>
            <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
              Bubble Chart
            </span>
          </div>
          <Venue3DBubbleChart :venues="store.venues" />
        </div>

        <!-- Cumulative Area Chart (6 cols) - Chart 10: Area Chart -->
        <div id="chart-10" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                  Chart 10 • Area Chart
                </span>
              </div>
              <h3 class="font-bold text-base text-brand-900">Tren Akumulasi Retribusi Bulanan vs Target</h3>
              <p class="text-xs text-muted">Area Chart: Laju pencapaian akumulatif penerimaan PAD sarana olahraga</p>
            </div>
            <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
              Area Chart
            </span>
          </div>
          <LevyCumulativeAreaChart />
        </div>
      </div>

      <ExplainPanel
        title="Bagaimana matriks retribusi ini dianalisis?"
        description="Analisis matriks membagi seluruh sarana prasarana olahraga ke dalam empat kuadran berdasarkan titik median kunjungan per tahun (15.000 kunjungan) dan median retribusi sewa per tahun (Rp 40.000.000). Hal ini membantu Kepala Dinas mengambil keputusan tata kelola aset yang efisien."
        :assumptions="[
          'Retribusi mencakup biaya sewa lapangan resmi sesuai Perda Retribusi Daerah Kota Palu.',
          'Data kunjungan bersumber dari rekapitulasi buku tamu dan e-ticketing fasilitas.'
        ]"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePublicDataStore } from '@/stores/publicData'
import ConfidenceTag from '@/components/ui/ConfidenceTag.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import DummyBadge from '@/components/ui/DummyBadge.vue'
import ExplainPanel from '@/components/ui/ExplainPanel.vue'
import MedalProjectionChart from '@/components/charts/MedalProjectionChart.vue'
import MedalErrorBarChart from '@/components/charts/MedalErrorBarChart.vue'
import SportPerformanceRadarChart from '@/components/charts/SportPerformanceRadarChart.vue'
import VenueScoreHistogramChart from '@/components/charts/VenueScoreHistogramChart.vue'
import VenueCapacityLollipopChart from '@/components/charts/VenueCapacityLollipopChart.vue'
import LevyQuadrantChart from '@/components/charts/LevyQuadrantChart.vue'
import Venue3DBubbleChart from '@/components/charts/Venue3DBubbleChart.vue'
import LevyCumulativeAreaChart from '@/components/charts/LevyCumulativeAreaChart.vue'
import { Sparkles, Trophy, Wrench, Coins } from 'lucide-vue-next'

const store = usePublicDataStore()
const activeTab = ref('medals')

onMounted(async () => {
  await store.fetchPredictions()
  await store.fetchVenues()
})

const sortedMaintenanceVenues = computed(() => {
  return [...store.venues].sort((a, b) => a.score - b.score)
})
</script>