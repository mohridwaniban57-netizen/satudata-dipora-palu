<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-brand-900 text-white p-6 sm:p-10 shadow-lg border border-brand-800/40">
      <img
        src="/images/hero/hero-sports-palu.jpg"
        alt="Sarana & Atlet Olahraga Kota Palu"
        class="absolute inset-0 w-full h-full object-cover object-center opacity-35 mix-blend-luminosity scale-105 pointer-events-none"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-brand-900 via-brand-900/90 to-brand-900/60 pointer-events-none"></div>
      <div class="relative z-10 max-w-3xl space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-accent-400 border border-white/20 backdrop-blur-xs">
          <Activity class="w-3.5 h-3.5" />
          <span>Sistem Informasi Terintegrasi & Predictive Analytics</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
          Satu Data Dispora Kota Palu
        </h1>
        <p class="text-sm sm:text-base text-white/80 leading-relaxed">
          Dashboard analitik prediktif sarana prasarana olahraga, pembinaan potensi atlet muda, proyeksi prestasi medali, dan optimasi penerimaan retribusi daerah Kota Palu.
        </p>
        <div class="pt-3 flex flex-wrap items-center gap-3">
          <router-link to="/map" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent-500 hover:bg-accent-400 text-brand-900 font-bold text-xs sm:text-sm shadow-md transition">
            <MapPin class="w-4 h-4" />
            <span>Jelajahi Peta Fasilitas</span>
          </router-link>
          <router-link to="/predictions" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium text-xs sm:text-sm border border-white/20 transition backdrop-blur-xs">
            <TrendingUp class="w-4 h-4 text-accent-400" />
            <span>Lihat Proyeksi & Prediksi</span>
          </router-link>
        </div>
      </div>
      <div class="absolute -right-12 -bottom-16 w-80 h-80 rounded-full bg-brand-500/10 pointer-events-none blur-2xl"></div>
    </div>

    <!-- Row KPI Cards (Executive Summary) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 items-stretch">
      <KpiCard
        title="Total Sarana"
        :value="store.summary.total_venues"
        subtitle="Unit aset terdaftar"
        :icon="Building2"
        color="brand"
      />

      <KpiCard
        title="Kondisi Baik"
        :value="store.summary.pct_good + '%'"
        :subtitle="store.summary.venues_good + ' unit prima'"
        :icon="CheckCircle2"
        color="good"
      />

      <KpiCard
        title="Perlu Waspada"
        :value="store.summary.pct_watch + '%'"
        :subtitle="store.summary.venues_watch + ' unit monitoring'"
        :icon="AlertTriangle"
        color="watch"
      />

      <KpiCard
        title="Kondisi Kritis"
        :value="store.summary.pct_critical + '%'"
        :subtitle="store.summary.venues_critical + ' unit perbaikan'"
        :icon="XCircle"
        color="critical"
      />

      <KpiCard
        title="Atlet Binaan"
        :value="store.summary.total_athletes"
        subtitle="K-anonymity (k=5)"
        :icon="Users"
        color="accent"
      />

      <KpiCard
        title="Realisasi Retribusi"
        :value="'Rp ' + (store.summary.realisasi_retribusi / 1000000).toFixed(1) + ' Jt'"
        :subtitle="store.summary.pct_retribusi + '% dari target'"
        :icon="Coins"
        color="brand"
      />
    </div>

    <!-- Section 2: Mini Map + Top 5 Prioritas Pemeliharaan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Mini Map (7 cols) -->
      <div id="chart-17" class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4 flex flex-col justify-between scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 17 • Point Map
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <MapPin class="w-4 h-4 text-brand-600" />
              <span>Sebaran Sarana Prasarana Olahraga</span>
            </h3>
            <p class="text-xs text-muted">Pemetaan titik fasilitas dan kondisi aset di 8 kecamatan Kota Palu</p>
          </div>
          <div class="flex items-center gap-2">
            <div class="bg-gray-100 p-0.5 rounded-lg flex text-[11px] font-semibold">
              <button
                @click="setMiniMapMode('pins')"
                class="px-2 py-1 rounded transition"
                :class="miniMapMode === 'pins' ? 'bg-white text-brand-900 shadow-xs' : 'text-muted hover:text-text-main'"
              >
                Pin Titik
              </button>
              <button
                @click="setMiniMapMode('choropleth')"
                class="px-2 py-1 rounded transition"
                :class="miniMapMode === 'choropleth' ? 'bg-white text-brand-900 shadow-xs' : 'text-muted hover:text-text-main'"
              >
                Choropleth
              </button>
            </div>
            <router-link
              to="/map"
              class="text-xs font-semibold text-brand-600 hover:text-brand-900 flex items-center gap-1 group ml-1"
            >
              <span>Lengkap</span>
              <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
            </router-link>
          </div>
        </div>

        <!-- Leaflet Map Container -->
        <div id="mini-map" class="w-full h-80 rounded-xl overflow-hidden border border-gray-100 z-0"></div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-4 text-xs pt-1 border-t border-gray-100">
          <template v-if="miniMapMode === 'pins'">
            <span class="text-muted font-medium">Status Aset:</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-status-good"></span> Baik</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-status-watch"></span> Waspada</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-status-critical"></span> Kritis</span>
          </template>
          <template v-else>
            <span class="text-muted font-medium">Kepadatan Sarana:</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-[#0B3C5D]"></span> Tinggi (&gt;0.8)</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-[#2585C4]"></span> Sedang (0.5-0.8)</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-[#93C5FD]"></span> Rendah (&lt;0.5)</span>
          </template>
        </div>
      </div>

      <!-- Top 5 Prioritas Pemeliharaan (5 cols) -->
      <div class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Wrench class="w-4 h-4 text-rose-500" />
              <span>Prioritas Pemeliharaan Aset</span>
            </h3>
            <p class="text-xs text-muted">Top 5 sarana olahraga dengan skor kondisi terendah</p>
          </div>
          <DummyBadge label="Model OLS" />
        </div>

        <div class="space-y-3">
          <div
            v-for="(venue, index) in store.topPriorityVenues"
            :key="venue.id"
            class="p-2.5 sm:p-3 rounded-xl border border-gray-100 hover:border-brand-200 hover:bg-brand-50/20 transition-all flex items-center justify-between gap-3 group"
          >
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <div class="relative w-12 h-12 rounded-lg overflow-hidden shrink-0 border border-gray-100 bg-gray-50 shadow-2xs">
                <img
                  :src="getVenueThumbnail(venue)"
                  :alt="venue.name"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                  loading="lazy"
                />
                <span class="absolute bottom-0 right-0 w-4 h-4 rounded-tl bg-brand-900/90 text-[10px] font-bold text-white flex items-center justify-center">
                  {{ index + 1 }}
                </span>
              </div>
              <div class="min-w-0 flex-1">
                <router-link :to="'/venues/' + venue.id" class="text-xs sm:text-sm font-semibold text-text-main group-hover:text-brand-600 block truncate" :title="venue.name">
                  {{ venue.name }}
                </router-link>
                <div class="flex items-center gap-2 text-[11px] text-muted mt-0.5 truncate">
                  <span class="truncate">{{ venue.district_name }}</span>
                  <span>•</span>
                  <span class="shrink-0 whitespace-nowrap">Estimasi: {{ venue.next_maintenance || 'Segera' }}</span>
                </div>
              </div>
            </div>

            <StatusBadge :status="venue.status" :score="venue.score" size="sm" class="shrink-0" />
          </div>
        </div>

        <router-link
          to="/predictions"
          class="block text-center text-xs font-semibold text-brand-600 hover:text-brand-900 pt-2"
        >
          Lihat Semua Ranking Prioritas Pemeliharaan →
        </router-link>
      </div>
    </div>

    <!-- Section: Sorotan Pembinaan Atlet Cabor Unggulan -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-3">
        <div>
          <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
            <Award class="w-4 h-4 text-accent-500" />
            <span>Sorotan Pembinaan Atlet Cabang Olahraga Unggulan</span>
          </h3>
          <p class="text-xs text-muted">Dokumentasi pembinaan intensif atlet muda daerah binaan Dispora Kota Palu</p>
        </div>
        <router-link
          to="/athletes-youth"
          class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-900 transition shrink-0"
        >
          <span>Lihat Statistik Lengkap</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Dayung -->
        <div class="relative rounded-xl overflow-hidden border border-gray-100 group shadow-2xs bg-white flex flex-col">
          <div class="relative h-52 sm:h-60 overflow-hidden bg-gray-100">
            <img
              src="/images/athletes/atlet-dayung.jpg"
              alt="Atlet Dayung & Kano Teluk Palu"
              class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/25 to-transparent pointer-events-none"></div>
            <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-900/85 text-accent-400 border border-white/20 backdrop-blur-xs">
              Cabor Unggulan Air
            </span>
            <div class="absolute bottom-3 left-3 right-3 text-white">
              <h4 class="font-bold text-sm sm:text-base leading-tight">Dayung & Kano • Teluk Palu</h4>
              <p class="text-[11px] text-white/80 mt-0.5">Pusat Latihan Pesisir Teluk Palu</p>
            </div>
          </div>
          <div class="p-3.5 text-xs text-muted bg-white flex-1 flex flex-col justify-between space-y-2">
            <p class="line-clamp-2 leading-relaxed">Fasilitas alami Teluk Palu menjadi sentra pembinaan atlet dayung berprestasi nasional.</p>
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] font-medium text-brand-900">
              <span>Target Medali 2026: <b class="font-bold">Emas & Perak</b></span>
              <span class="text-emerald-600 font-bold">12 Atlet Binaan</span>
            </div>
          </div>
        </div>

        <!-- Card 2: Sepak Bola -->
        <div class="relative rounded-xl overflow-hidden border border-gray-100 group shadow-2xs bg-white flex flex-col">
          <div class="relative h-52 sm:h-60 overflow-hidden bg-gray-100">
            <img
              src="/images/athletes/atlet-sepakbola.jpg"
              alt="Atlet Sepak Bola Muda Palu"
              class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/25 to-transparent pointer-events-none"></div>
            <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-900/85 text-accent-400 border border-white/20 backdrop-blur-xs">
              Cabor Tim Populer
            </span>
            <div class="absolute bottom-3 left-3 right-3 text-white">
              <h4 class="font-bold text-sm sm:text-base leading-tight">Sepak Bola • PPLPD Palu</h4>
              <p class="text-[11px] text-white/80 mt-0.5">Stadion Gawalise & Lapangan Vatulemo</p>
            </div>
          </div>
          <div class="p-3.5 text-xs text-muted bg-white flex-1 flex flex-col justify-between space-y-2">
            <p class="line-clamp-2 leading-relaxed">Program pembinaan bibit muda U-17 untuk kompetisi antar-daerah dan Pekan Olahraga Pelajar.</p>
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] font-medium text-brand-900">
              <span>Target Kompetisi: <b class="font-bold">Popda 2026</b></span>
              <span class="text-emerald-600 font-bold">32 Atlet Binaan</span>
            </div>
          </div>
        </div>

        <!-- Card 3: Pencak Silat -->
        <div class="relative rounded-xl overflow-hidden border border-gray-100 group shadow-2xs bg-white flex flex-col">
          <div class="relative h-52 sm:h-60 overflow-hidden bg-gray-100">
            <img
              src="/images/athletes/atlet-silat.jpg"
              alt="Atlet Pencak Silat Kontingen Palu"
              class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/25 to-transparent pointer-events-none"></div>
            <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-900/85 text-accent-400 border border-white/20 backdrop-blur-xs">
              Cabor Beladiri Tradisional
            </span>
            <div class="absolute bottom-3 left-3 right-3 text-white">
              <h4 class="font-bold text-sm sm:text-base leading-tight">Pencak Silat • Siranindi</h4>
              <p class="text-[11px] text-white/80 mt-0.5">GOR Siranindi & Padepokan Silat</p>
            </div>
          </div>
          <div class="p-3.5 text-xs text-muted bg-white flex-1 flex flex-col justify-between space-y-2">
            <p class="line-clamp-2 leading-relaxed">Cabor peraih medali konsisten yang menempatkan pesilat Kota Palu di panggung nasional.</p>
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] font-medium text-brand-900">
              <span>Target Medali 2026: <b class="font-bold">3 Emas</b></span>
              <span class="text-emerald-600 font-bold">18 Atlet Binaan</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 3: Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Chart 23: Line Chart (Tren Medali) -->
      <div id="chart-23" class="bg-surface rounded-2xl border border-gray-100 p-5 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 23 • Line Chart
              </span>
            </div>
            <h3 class="font-bold text-sm sm:text-base text-brand-900 flex items-center gap-2">
              <Trophy class="w-4 h-4 text-accent-500" />
              <span>Tren Perolehan Medali Kontingen Kota Palu</span>
            </h3>
            <p class="text-xs text-muted">Distribusi medali Emas, Perak, Perunggu di Popda & Porprov</p>
          </div>
          <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
            Data Riil
          </span>
        </div>
        <MedalTrendChart :data="store.medalHistory" />
      </div>

      <!-- Chart 11: Donut Chart (Komposisi Jenis Fasilitas) -->
      <div id="chart-11" class="bg-surface rounded-2xl border border-gray-100 p-5 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 11 • Donut Chart
              </span>
            </div>
            <h3 class="font-bold text-sm sm:text-base text-brand-900 flex items-center gap-2">
              <PieChart class="w-4 h-4 text-brand-600" />
              <span>Komposisi Jenis Sarana Olahraga</span>
            </h3>
            <p class="text-xs text-muted">Proporsi sarana prasarana olahraga yang dikelola Dispora</p>
          </div>
        </div>
        <VenueTypeDonutChart :venues="store.venues" />
      </div>
    </div>

    <!-- Section 4: Distribusi Fasilitas & Realisasi Retribusi (Bullet Chart) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Chart 4: Stacked Column Chart -->
      <div id="chart-4" class="bg-surface rounded-2xl border border-gray-100 p-5 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 4 • Stacked Column Chart
              </span>
            </div>
            <h3 class="font-bold text-sm sm:text-base text-brand-900 flex items-center gap-2">
              <BarChart2 class="w-4 h-4 text-brand-600" />
              <span>Distribusi Kondisi Fasilitas per Kecamatan</span>
            </h3>
            <p class="text-xs text-muted">Pemetaan kondisi fisik sarana (Baik, Waspada, Kritis) di 8 kecamatan Kota Palu</p>
          </div>
        </div>
        <DistrictConditionChart :venues="store.venues" :districts="store.districts" />
      </div>

      <!-- Chart 21: Bullet Chart -->
      <div id="chart-21" class="bg-surface rounded-2xl border border-gray-100 p-5 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 21 • Bullet Chart
              </span>
            </div>
            <h3 class="font-bold text-sm sm:text-base text-brand-900 flex items-center gap-2">
              <Coins class="w-4 h-4 text-brand-600" />
              <span>Target vs Realisasi Retribusi (Bullet Chart)</span>
            </h3>
            <p class="text-xs text-muted">Perbandingan realisasi PAD retribusi daerah terhadap target per kelompok sarana</p>
          </div>
          <span class="text-[11px] font-medium text-brand-800 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
            PAD Olahraga
          </span>
        </div>
        <RevenueBulletChart :venues="store.venues" />
      </div>
    </div>

    <!-- Explain Panel -->
    <ExplainPanel
      title="Bagaimana metrik dan skor kondisi fasilitas dihitung?"
      formula="Skor Kondisi = 0.50 × Skor Inspeksi Lapangan + 0.30 × Indeks Masa Pakai Desain + 0.20 × Faktor Insiden Pemeliharaan"
      description="Kondisi dibagi menjadi tiga kategori: Baik (skor 70-100, pemeliharaan rutin preventif), Waspada (skor 45-69, butuh inspeksi berkala dan perbaikan parsial), dan Kritis (skor < 45, membutuhkan renovasi struktural segera)."
      :assumptions="[
        'Data inspeksi bersumber dari laporan berkala UPTD Sarana Prasarana Olahraga.',
        'Masa pakai desain aset diasumsikan berkisar antara 15 hingga 30 tahun sesuai tipe konstruksi.',
        'Data dummy digunakan pada sebagian aset untuk memvalidasi algoritma prediktif (ditandai label khusus).'
      ]"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { usePublicDataStore } from '@/stores/publicData'
import KpiCard from '@/components/ui/KpiCard.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import DummyBadge from '@/components/ui/DummyBadge.vue'
import ExplainPanel from '@/components/ui/ExplainPanel.vue'
import MedalTrendChart from '@/components/charts/MedalTrendChart.vue'
import VenueTypeDonutChart from '@/components/charts/VenueTypeDonutChart.vue'
import DistrictConditionChart from '@/components/charts/DistrictConditionChart.vue'
import RevenueBulletChart from '@/components/charts/RevenueBulletChart.vue'
import { paluDistrictsGeoJSON } from '@/data/paluDistrictsGeoJSON'
import L from 'leaflet'
import {
  Activity,
  Building2,
  CheckCircle2,
  AlertTriangle,
  XCircle,
  Users,
  Coins,
  MapPin,
  TrendingUp,
  Wrench,
  Trophy,
  PieChart,
  BarChart2,
  ArrowRight,
  Award,
  ShieldCheck,
} from 'lucide-vue-next'

const store = usePublicDataStore()
const miniMapMode = ref('pins')
let miniMap = null
let miniPinsLayer = null
let miniChoroplethLayer = null

onMounted(async () => {
  await store.fetchSummary()
  await store.fetchVenues()
  await store.fetchMedalStats()

  // Initialize Leaflet Mini Map
  await nextTick()
  initMiniMap()
})

const initMiniMap = () => {
  const container = document.getElementById('mini-map')
  if (!container || container._leaflet_id) return

  // Palu coordinates: [-0.885, 119.87]
  miniMap = L.map('mini-map', {
    center: [-0.885, 119.87],
    zoom: 12,
    zoomControl: false,
    scrollWheelZoom: false,
  })

  L.control.zoom({ position: 'topright' }).addTo(miniMap)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(miniMap)

  miniPinsLayer = L.featureGroup().addTo(miniMap)
  renderMiniPins()
}

const renderMiniPins = () => {
  if (!miniPinsLayer) return
  miniPinsLayer.clearLayers()

  store.venues.forEach((v) => {
    if (!v.latitude || !v.longitude) return

    let color = '#2E9E5B'
    if (v.status === 'watch') color = '#E0A100'
    if (v.status === 'critical') color = '#D64545'

    const markerHtml = `
      <div style="background-color: ${color}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
    `

    const icon = L.divIcon({
      html: markerHtml,
      className: 'custom-pin',
      iconSize: [14, 14],
      iconAnchor: [7, 7],
    })

    const marker = L.marker([v.latitude, v.longitude], { icon })
    marker.bindPopup(`
      <div class="p-3 text-xs space-y-1">
        <p class="font-bold text-sm text-brand-900">${v.name}</p>
        <p class="text-gray-500">${v.district_name} • ${v.type_name}</p>
        <p class="font-semibold" style="color: ${color}">Skor Kondisi: ${v.score} / 100</p>
      </div>
    `)
    marker.addTo(miniPinsLayer)
  })
}

const renderMiniChoropleth = () => {
  if (!miniChoroplethLayer) {
    miniChoroplethLayer = L.geoJSON(paluDistrictsGeoJSON, {
      style: (feature) => {
        const dName = feature.properties.name
        const count = store.venues.filter((v) => v.district_name === dName).length
        const pop = feature.properties.population || 50000
        const density = count / (pop / 10000)

        let fillColor = '#93C5FD'
        if (density >= 0.8) fillColor = '#0B3C5D'
        else if (density >= 0.5) fillColor = '#2585C4'

        return {
          fillColor,
          weight: 1.5,
          opacity: 1,
          color: '#FFFFFF',
          dashArray: '2',
          fillOpacity: 0.65,
        }
      },
      onEachFeature: (feature, layer) => {
        const dName = feature.properties.name
        const count = store.venues.filter((v) => v.district_name === dName).length
        layer.bindPopup(`
          <div class="p-2 text-xs">
            <p class="font-bold text-brand-900">Kecamatan ${dName}</p>
            <p class="text-muted">Total Fasilitas: <b>${count}</b> unit</p>
          </div>
        `)
      },
    })
  }
}

const setMiniMapMode = (mode) => {
  miniMapMode.value = mode
  if (!miniMap) return

  if (mode === 'pins') {
    if (miniChoroplethLayer && miniMap.hasLayer(miniChoroplethLayer)) {
      miniMap.removeLayer(miniChoroplethLayer)
    }
    if (miniPinsLayer && !miniMap.hasLayer(miniPinsLayer)) {
      miniMap.addLayer(miniPinsLayer)
    }
  } else if (mode === 'choropleth') {
    if (miniPinsLayer && miniMap.hasLayer(miniPinsLayer)) {
      miniMap.removeLayer(miniPinsLayer)
    }
    renderMiniChoropleth()
    if (miniChoroplethLayer && !miniMap.hasLayer(miniChoroplethLayer)) {
      miniMap.addLayer(miniChoroplethLayer)
    }
  }
}

const getVenueThumbnail = (venue) => {
  const name = (venue?.name || '').toLowerCase()
  if (name.includes('gawalise')) return '/images/venues/stadion-gawalise.jpg'
  if (name.includes('siranindi') || name.includes('gor') || name.includes('bumi kaktus')) return '/images/venues/gor-siranindi.jpg'
  if (name.includes('vatulemo') || name.includes('lapangan')) return '/images/venues/lapangan-vatulemo.jpg'
  if (name.includes('dayung') || name.includes('layar') || name.includes('teluk')) return '/images/venues/dayung-teluk-palu.jpg'
  if (venue?.venue_type_id === 1) return '/images/venues/stadion-gawalise.jpg'
  if (venue?.venue_type_id === 2 || venue?.venue_type_id === 5) return '/images/venues/gor-siranindi.jpg'
  if (venue?.venue_type_id === 3 || venue?.venue_type_id === 7) return '/images/venues/lapangan-vatulemo.jpg'
  return '/images/venues/stadion-gawalise.jpg'
}
</script>
