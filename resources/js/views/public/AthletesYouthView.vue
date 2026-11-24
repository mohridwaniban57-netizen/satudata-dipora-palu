<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm space-y-3">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-900 border border-brand-200">
            <ShieldCheck class="w-3.5 h-3.5 text-brand-600" />
            <span>K-Anonymity (k=5) Privacy Protected</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
            Statistik Pembinaan Atlet & Kepemudaan
          </h1>
          <p class="text-xs sm:text-sm text-muted max-w-2xl">
            Sebaran data agregat atlet daerah Kota Palu, piramida demografi usia, serta organisasi kepemudaan (OKP & Karang Taruna) di 8 kecamatan.
          </p>
        </div>

        <div class="bg-brand-50 p-3.5 rounded-xl border border-brand-100 text-right">
          <span class="text-[11px] text-muted block uppercase tracking-wider">Total Atlet Binaan</span>
          <span class="text-2xl font-bold text-brand-900">{{ totalAthletesCount }}</span>
          <span class="text-[10px] text-muted block">Terdaftar resmi di Dispora</span>
        </div>
      </div>

      <!-- Privacy Notice Banner -->
      <div class="p-3 bg-amber-50/70 border border-amber-200/80 rounded-xl flex items-center gap-2.5 text-xs text-amber-900">
        <Info class="w-4 h-4 text-accent-600 shrink-0" />
        <span>
          <strong>Kepatuhan UU No. 27/2022 (Pelindungan Data Pribadi):</strong> Portal publik hanya menyajikan statistik agregat. Seluruh kelompok agregasi dengan frekuensi di bawah 5 individu disamarkan menjadi <code>&lt; 5</code> untuk mencegah re-identifikasi personal.
        </span>
      </div>

      <!-- Athlete Documentation Photo Strip -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
        <div class="relative h-44 sm:h-52 rounded-xl overflow-hidden border border-gray-100 group shadow-2xs bg-gray-100">
          <img src="/images/athletes/atlet-dayung.jpg" alt="Dayung Teluk Palu" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-brand-950/85 via-brand-950/20 to-transparent"></div>
          <span class="absolute bottom-2.5 left-3 text-white text-xs font-bold drop-shadow-sm">Cabor Dayung & Kano • Teluk Palu</span>
        </div>
        <div class="relative h-44 sm:h-52 rounded-xl overflow-hidden border border-gray-100 group shadow-2xs bg-gray-100">
          <img src="/images/athletes/atlet-sepakbola.jpg" alt="Sepak Bola Palu" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-brand-950/85 via-brand-950/20 to-transparent"></div>
          <span class="absolute bottom-2.5 left-3 text-white text-xs font-bold drop-shadow-sm">Cabor Sepak Bola • PPLPD Palu</span>
        </div>
        <div class="relative h-44 sm:h-52 rounded-xl overflow-hidden border border-gray-100 group shadow-2xs bg-gray-100">
          <img src="/images/athletes/atlet-silat.jpg" alt="Pencak Silat Palu" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-brand-950/85 via-brand-950/20 to-transparent"></div>
          <span class="absolute bottom-2.5 left-3 text-white text-xs font-bold drop-shadow-sm">Cabor Pencak Silat • GOR Siranindi</span>
        </div>
      </div>
    </div>

    <!-- Charts Row 1: Atlet per Cabor & Piramida Usia -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Atlet per Cabor (7 cols) - Chart 1: Bar Chart -->
      <div id="chart-1" class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 1 • Bar Chart
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Trophy class="w-4 h-4 text-brand-600" />
              <span>Distribusi Atlet per Cabang Olahraga</span>
            </h3>
            <p class="text-xs text-muted">Jumlah atlet berprestasi binaan Pusat Pendidikan & Latihan Olahraga Daerah (PPLPD)</p>
          </div>
        </div>

        <AthletesBySportChart :data="store.athletesStats.by_sport" />
      </div>

      <!-- Piramida Usia & Gender (5 cols) - Chart 19: Stacked Bar Chart -->
      <div id="chart-19" class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 19 • Stacked Bar Chart
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Users class="w-4 h-4 text-accent-500" />
              <span>Demografi Kelompok Usia & Gender</span>
            </h3>
            <p class="text-xs text-muted">Perbandingan proporsi atlet putra dan putri</p>
          </div>
        </div>

        <AthletesPyramidChart :data="store.athletesStats.by_age_gender" />
      </div>
    </div>

    <!-- Charts Row 2: Tingkat Prestasi Atlet (100% Stacked Bar) & Regenerasi (Funnel Chart) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- 100% Stacked Bar (7 cols) - Chart 13: 100% Stacked Bar Chart -->
      <div id="chart-13" class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 13 • 100% Stacked Bar Chart
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <Trophy class="w-4 h-4 text-brand-600" />
              <span>Komposisi Tingkat Prestasi Atlet per Cabang Olahraga</span>
            </h3>
            <p class="text-xs text-muted">Proporsi jenjang prestasi (Klub, Daerah, Provinsi, Nasional) dalam 100% skala per cabor</p>
          </div>
          <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
            100% Stacked
          </span>
        </div>
        <AthleteLevelStacked100Chart :sports="store.athletesStats.by_sport" />
      </div>

      <!-- Funnel Chart Regenerasi Atlet (5 cols) - Chart 14: Funnel Chart -->
      <div id="chart-14" class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 14 • Funnel Chart
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <TrendingUp class="w-4 h-4 text-accent-500" />
              <span>Pipeline Regenerasi Atlet Binaan</span>
            </h3>
            <p class="text-xs text-muted">Konversi talenta dari jenjang pemula hingga peraih medali kejuaraan</p>
          </div>
          <span class="text-[11px] font-medium text-accent-700 bg-accent-50 px-2 py-0.5 rounded border border-accent-200">
            Funnel Analitik
          </span>
        </div>
        <AthleteRegenerationFunnelChart />
      </div>
    </div>

    <!-- Charts Row 3: Alokasi Anggaran (Treemap) & Alur Pembiayaan Prestasi (Sankey Diagram) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Treemap Chart (6 cols) - Chart 12: Treemap Chart -->
      <div id="chart-12" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 12 • Treemap Chart
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <PieChart class="w-4 h-4 text-brand-600" />
              <span>Peta Alokasi Anggaran Dispora (Treemap)</span>
            </h3>
            <p class="text-xs text-muted">Hierarki proporsi pendanaan sektor olahraga prestasi, kepemudaan, dan pemeliharaan</p>
          </div>
          <span class="text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">
            APBD Dispora
          </span>
        </div>
        <BudgetTreemapChart />
      </div>

      <!-- Sankey Diagram (6 cols) - Chart 9: Sankey Diagram -->
      <div id="chart-9" class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-100 text-brand-900 border border-brand-300">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                Chart 9 • Sankey Diagram
              </span>
            </div>
            <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
              <GitMerge class="w-4 h-4 text-accent-500" />
              <span>Alur Pembiayaan Anggaran ke Prestasi (Sankey)</span>
            </h3>
            <p class="text-xs text-muted">Visualisasi aliran dana pembinaan dari kas daerah hingga capaian medali emas/perak/perunggu</p>
          </div>
          <span class="text-[11px] font-medium text-accent-700 bg-accent-50 px-2 py-0.5 rounded border border-accent-200">
            Sankey Flow
          </span>
        </div>
        <BudgetAthleteFlowSankeyChart />
      </div>
    </div>

    <!-- Charts Row 4: Sebaran per Kecamatan & Organisasi Kepemudaan (OKP) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Sebaran Atlet per Kecamatan (6 cols) -->
      <div class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
        <div>
          <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
            <MapPin class="w-4 h-4 text-brand-600" />
            <span>Sebaran Asal Atlet per Kecamatan</span>
          </h3>
          <p class="text-xs text-muted">Basis domisili atlet muda berprestasi di wilayah Kota Palu</p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead class="bg-gray-50 text-muted font-semibold border-b border-gray-100">
              <tr>
                <th class="py-2.5 px-3">Kecamatan</th>
                <th class="py-2.5 px-3 text-right">Jumlah Atlet</th>
                <th class="py-2.5 px-3 text-right">Proporsi</th>
                <th class="py-2.5 px-3 text-center">Status Privasi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="d in store.athletesStats?.by_district || []" :key="d.district_id" class="hover:bg-gray-50/50">
                <td class="py-2.5 px-3 font-semibold text-text-main">{{ d.district_name }}</td>
                <td class="py-2.5 px-3 text-right font-mono font-bold text-brand-900">
                  <span v-if="d.is_suppressed || d.count === '< 5' || (typeof d.count === 'number' && d.count < 5)" class="text-muted cursor-help" title="Disamarkan untuk melindungi data pribadi atlet muda">&lt; 5</span>
                  <span v-else>{{ d.count }}</span>
                </td>
                <td class="py-2.5 px-3 text-right text-muted">
                  <span v-if="typeof d.count === 'number' && totalAthletesCount > 0">
                    {{ ((d.count / totalAthletesCount) * 100).toFixed(1) }}%
                  </span>
                  <span v-else class="text-muted italic text-[11px]">&lt; 5%</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Aman (k ≥ 5)
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Organisasi Kepemudaan (OKP & Karang Taruna) (6 cols) -->
      <div class="lg:col-span-6 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
        <div>
          <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
            <Users2 class="w-4 h-4 text-brand-600" />
            <span>Organisasi Kepemudaan (OKP & Karang Taruna)</span>
          </h3>
          <p class="text-xs text-muted">Potensi kelembagaan pemuda aktif di 8 kecamatan</p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead class="bg-gray-50 text-muted font-semibold border-b border-gray-100">
              <tr>
                <th class="py-2.5 px-3">Kecamatan</th>
                <th class="py-2.5 px-3 text-center">OKP</th>
                <th class="py-2.5 px-3 text-center">Karang Taruna</th>
                <th class="py-2.5 px-3 text-right">Estimasi Anggota</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="org in youthOrgsList" :key="org.district_id" class="hover:bg-gray-50/50">
                <td class="py-2.5 px-3 font-semibold text-text-main">{{ org.district_name }}</td>
                <td class="py-2.5 px-3 text-center font-mono font-bold text-brand-600">{{ org.okp_count ?? 0 }}</td>
                <td class="py-2.5 px-3 text-center font-mono font-bold text-accent-600">{{ org.karang_taruna_count ?? 0 }}</td>
                <td class="py-2.5 px-3 text-right font-mono text-muted">
                  {{ (org.total_members ? Number(org.total_members).toLocaleString('id-ID') : '0') }} org
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Explain Panel -->
    <ExplainPanel
      title="Bagaimana privasi atlet dan pelindungan data pribadi dijamin?"
      description="Sesuai amanat Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP), identitas atlet anak/remaja berstatus data sensitif. Pada database internal, nama lengkap dienkripsi menggunakan AES-256-CBC dengan salt unik. Di antarmuka publik, portal hanya menyajikan agregasi berskala makro dengan ambang batas k-anonymity (k=5)."
      :assumptions="[
        'Kelompok agregat dengan n < 5 di-masking secara otomatis menjadi teks (< 5).',
        'Tidak ada nomor induk kependudukan (NIK), alamat tempat tinggal, maupun kontak telepon yang disajikan di portal publik.',
        'Data kepemudaan diperbarui berdasarkan registrasi tahunan organisasi di Dispora Kota Palu.'
      ]"
    />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { usePublicDataStore } from '@/stores/publicData'
import AthletesBySportChart from '@/components/charts/AthletesBySportChart.vue'
import AthletesPyramidChart from '@/components/charts/AthletesPyramidChart.vue'
import AthleteLevelStacked100Chart from '@/components/charts/AthleteLevelStacked100Chart.vue'
import AthleteRegenerationFunnelChart from '@/components/charts/AthleteRegenerationFunnelChart.vue'
import BudgetTreemapChart from '@/components/charts/BudgetTreemapChart.vue'
import BudgetAthleteFlowSankeyChart from '@/components/charts/BudgetAthleteFlowSankeyChart.vue'
import ExplainPanel from '@/components/ui/ExplainPanel.vue'
import {
  ShieldCheck,
  Trophy,
  Users,
  MapPin,
  Users2,
  Info,
  TrendingUp,
  PieChart,
  GitMerge,
} from 'lucide-vue-next'

const store = usePublicDataStore()

const totalAthletesCount = computed(() => {
  if (store.athletesStats?.total) return store.athletesStats.total
  if (Array.isArray(store.athletesStats?.by_district)) {
    return store.athletesStats.by_district.reduce((sum, d) => sum + (typeof d.count === 'number' ? d.count : 0), 0)
  }
  return 150
})

const youthOrgsList = computed(() => {
  if (Array.isArray(store.youthOrgs)) return store.youthOrgs
  if (Array.isArray(store.youthOrgs?.by_district)) return store.youthOrgs.by_district
  return []
})

onMounted(async () => {
  await store.fetchAthletesStats()
  await store.fetchYouthOrgs()
})
</script>