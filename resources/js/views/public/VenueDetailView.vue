<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-muted">
      <router-link to="/" class="hover:text-brand-600">Beranda</router-link>
      <span>/</span>
      <router-link to="/map" class="hover:text-brand-600">Peta Fasilitas</router-link>
      <span>/</span>
      <span class="text-text-main font-semibold truncate">{{ venue?.name || 'Detail Venue' }}</span>
    </div>

    <!-- Header Card -->
    <div v-if="venue" class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded border border-brand-200">
            {{ venue.type_name }}
          </span>
          <StatusBadge :status="venue.status" :score="venue.score" size="md" />
          <DummyBadge v-if="venue.is_dummy" label="Data Simulasi" />
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
          {{ venue.name }}
        </h1>
        <p class="text-xs sm:text-sm text-muted flex items-center gap-1.5">
          <MapPin class="w-4 h-4 text-brand-600 shrink-0" />
          <span>{{ venue.address || 'Kecamatan ' + venue.district_name + ', Kota Palu' }}</span>
        </p>
      </div>

      <div class="flex items-center gap-3">
        <router-link
          to="/simulator"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-brand-50 text-brand-900 border border-brand-200 hover:bg-brand-100 transition shadow-xs"
        >
          <Sparkles class="w-3.5 h-3.5 text-accent-500" />
          <span>Simulasi Renovasi</span>
        </router-link>
      </div>
    </div>

    <!-- Row 1: Profil Spesifikasi & Condition Gauge -->
    <div v-if="venue" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Spesifikasi Venue (7 cols) -->
      <div class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
        <!-- Venue Photo Banner -->
        <div class="relative w-full h-48 sm:h-56 rounded-xl overflow-hidden border border-gray-100 shadow-2xs group">
          <img
            :src="getVenuePhoto(venue)"
            :alt="venue.name"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-brand-950/70 via-transparent to-transparent"></div>
          <div class="absolute bottom-3 left-3 text-white">
            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-brand-900/80 backdrop-blur-xs border border-white/20">
              Dokumentasi Visual Fasilitas • Kota Palu
            </span>
          </div>
        </div>

        <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
          <Building2 class="w-4 h-4 text-brand-600" />
          <span>Spesifikasi & Informasi Operasional</span>
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
          <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
            <span class="text-[11px] text-muted block uppercase tracking-wider">Kecamatan</span>
            <span class="font-bold text-sm text-text-main mt-0.5 block">{{ venue.district_name }}</span>
          </div>

          <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
            <span class="text-[11px] text-muted block uppercase tracking-wider">Kapasitas Penonton</span>
            <span class="font-bold text-sm text-text-main mt-0.5 block">
              {{ venue.capacity ? venue.capacity.toLocaleString('id-ID') + ' orang' : '-' }}
            </span>
          </div>

          <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
            <span class="text-[11px] text-muted block uppercase tracking-wider">Luas Area Fisik</span>
            <span class="font-bold text-sm text-text-main mt-0.5 block">
              {{ venue.area_m2 ? venue.area_m2.toLocaleString('id-ID') + ' m²' : '-' }}
            </span>
          </div>

          <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100">
            <span class="text-[11px] text-muted block uppercase tracking-wider">Tahun Dibangun</span>
            <span class="font-bold text-sm text-text-main mt-0.5 block">{{ venue.year_built || '-' }}</span>
          </div>

          <div class="bg-gray-50/70 p-3.5 rounded-xl border border-gray-100 sm:col-span-2">
            <span class="text-[11px] text-muted block uppercase tracking-wider">Instansi Pengelola</span>
            <span class="font-bold text-sm text-text-main mt-0.5 block">{{ venue.manager || 'Dispora Kota Palu' }}</span>
          </div>
        </div>

        <!-- Coordinates & Service Estimate -->
        <div class="p-4 rounded-xl bg-brand-50/50 border border-brand-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
          <div>
            <span class="text-muted block text-[11px]">Koordinat Geografis:</span>
            <span class="font-mono text-brand-900 font-semibold">{{ venue.latitude }}, {{ venue.longitude }}</span>
          </div>
          <div class="sm:text-right">
            <span class="text-muted block text-[11px]">Proyeksi Perbaikan Berikutnya:</span>
            <span class="font-semibold text-brand-900">{{ venue.next_maintenance || 'Segera' }}</span>
          </div>
        </div>
      </div>

      <!-- Condition Score Speedometer (5 cols) -->
      <div class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 flex flex-col justify-between">
        <div>
          <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
            <Gauge class="w-4 h-4 text-brand-600" />
            <span>Skor Kesehatan & Kondisi Fisik</span>
          </h3>
          <p class="text-xs text-muted">Kombinasi hasil audit berkala dan indeks depresiasi aset</p>
        </div>

        <ConditionGaugeChart :score="venue.score" />

        <div class="text-center text-xs text-muted pt-2 border-t border-gray-100">
          Inspeksi terakhir tercatat pada <b>{{ venue.last_inspected || '-' }}</b>
        </div>
      </div>
    </div>

    <!-- Row 2: Dekomposisi Skor (Waterfall) & Kalender Inspeksi (Calendar Heatmap) -->
    <div v-if="venue" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Waterfall Chart (6 cols) - Chart 22: Waterfall Chart -->
      <div id="chart-22" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 22 • Waterfall Chart
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Activity class="w-4 h-4 text-brand-600" />
              <span>Dekomposisi Penurunan Skor (Waterfall Chart)</span>
            </h3>
            <p class="text-xs text-muted">Faktor penentu depresiasi: Kondisi Baru (100) vs Usia, Defisit Inspeksi & Insiden</p>
          </div>
          <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
            Waterfall
          </span>
        </div>
        <ConditionScoreWaterfallChart :venue="venue" />
      </div>

      <!-- Calendar Heatmap (6 cols) - Chart 15: Calendar Heatmap -->
      <div id="chart-15" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 15 • Calendar Heatmap
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Calendar class="w-4 h-4 text-brand-600" />
              <span>Kalender Aktivitas Inspeksi (Calendar Heatmap)</span>
            </h3>
            <p class="text-xs text-muted">Matriks intensitas pemeriksaan lapangan & servis preventif tahun berjalan (2026)</p>
          </div>
          <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
            Calendar
          </span>
        </div>
        <InspectionCalendarHeatmap :year="2026" />
      </div>
    </div>

    <!-- Row 3: Riwayat Inspeksi & Pemeliharaan -->
    <div v-if="venue" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Riwayat Inspeksi -->
      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
          <ClipboardCheck class="w-4 h-4 text-emerald-600" />
          <span>Riwayat Catatan Inspeksi Fisik</span>
        </h3>

        <div v-if="venue.inspections && venue.inspections.length > 0" class="space-y-3">
          <div
            v-for="insp in venue.inspections"
            :key="insp.id"
            class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 space-y-1.5 text-xs"
          >
            <div class="flex items-center justify-between">
              <span class="font-semibold text-brand-900">{{ insp.inspected_on }}</span>
              <StatusBadge :score="insp.score" size="sm" />
            </div>
            <p class="text-muted leading-relaxed">{{ insp.notes }}</p>
          </div>
        </div>
        <div v-else class="text-xs text-muted py-6 text-center">
          Belum ada catatan audit inspeksi berkala.
        </div>
      </div>

      <!-- Riwayat Pemeliharaan & Servis -->
      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
          <Wrench class="w-4 h-4 text-amber-600" />
          <span>Riwayat Intervensi & Biaya Pemeliharaan</span>
        </h3>

        <div v-if="venue.maintenance_records && venue.maintenance_records.length > 0" class="space-y-3">
          <div
            v-for="maint in venue.maintenance_records"
            :key="maint.id"
            class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 space-y-1.5 text-xs"
          >
            <div class="flex items-center justify-between">
              <span class="font-semibold text-text-main">{{ maint.type }}</span>
              <span class="font-bold text-brand-900">Rp {{ Number(maint.cost).toLocaleString('id-ID') }}</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-muted">
              <span>Selesai: {{ maint.done_on }}</span>
              <span v-if="maint.is_incident" class="text-rose-600 font-semibold">Insiden / Darurat</span>
              <span v-else class="text-emerald-600">Rutin Preventif</span>
            </div>
            <p v-if="maint.description" class="text-muted">{{ maint.description }}</p>
          </div>
        </div>
        <div v-else class="text-xs text-muted py-6 text-center">
          Belum ada catatan pemeliharaan terdaftar.
        </div>
      </div>
    </div>

    <!-- Row 3: Monthly Trend Chart - Chart 3: Column Chart -->
    <div id="chart-3" v-if="venue && venue.monthly_stats && venue.monthly_stats.length > 0" class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
              <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
              Chart 3 • Column Chart
            </span>
          </div>
          <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
            <TrendingUp class="w-4 h-4 text-brand-600" />
            <span>Tren Pemanfaatan Kunjungan & Retribusi Bulanan</span>
          </h3>
          <p class="text-xs text-muted">Aktivitas kunjungan masyarakat dan kontribusi PAD retribusi sewa sarana</p>
        </div>
        <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-200">
          Kuadran: {{ venue.quadrant || 'Stabil' }}
        </span>
      </div>

      <VenueMonthlyChart :stats="venue.monthly_stats" />
    </div>

    <!-- Explain Panel -->
    <ExplainPanel
      title="Bagaimana rekomendasi jadwal servis dan skor aset ini ditentukan?"
      formula="T_berikutnya = Tanggal_Inspeksi + Interval_Dasar × (Skor / 100) × Faktor_Penyesuaian_Pemanfaatan"
      description="Jika skor kondisi < 45, fasilitas diklasifikasikan sebagai Kritis dan secara otomatis masuk ke daftar prioritas anggaran renovasi APBD Dispora Kota Palu."
      :assumptions="[
        'Interval pemeliharaan dasar berbeda antar tipe venue (misal kolam renang 30 hari, lapangan terbuka 45 hari, stadion 90 hari).',
        'Fasilitas dengan intensitas utilisasi tinggi mempercepat kebutuhan servis berkala.'
      ]"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { usePublicDataStore } from '@/stores/publicData'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import DummyBadge from '@/components/ui/DummyBadge.vue'
import ExplainPanel from '@/components/ui/ExplainPanel.vue'
import ConditionGaugeChart from '@/components/charts/ConditionGaugeChart.vue'
import ConditionScoreWaterfallChart from '@/components/charts/ConditionScoreWaterfallChart.vue'
import InspectionCalendarHeatmap from '@/components/charts/InspectionCalendarHeatmap.vue'
import VenueMonthlyChart from '@/components/charts/VenueMonthlyChart.vue'
import {
  MapPin,
  Building2,
  Gauge,
  ClipboardCheck,
  Wrench,
  TrendingUp,
  Sparkles,
  Activity,
  Calendar,
} from 'lucide-vue-next'

const route = useRoute()
const store = usePublicDataStore()
const venue = ref(null)

onMounted(async () => {
  const id = route.params.id
  await store.fetchVenues()
  const res = await store.fetchVenue(id)
  venue.value = res || store.venueById(id)
})

const getVenuePhoto = (venue) => {
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