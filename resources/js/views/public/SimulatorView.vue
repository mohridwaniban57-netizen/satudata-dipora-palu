<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header Banner -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm space-y-3">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-accent-600 border border-amber-200">
            <Sliders class="w-3.5 h-3.5" />
            <span>Policy What-If Simulation Sandbox</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight mt-1">
            Simulator Kebijakan & Skenario Anggaran
          </h1>
          <p class="text-xs sm:text-sm text-muted max-w-2xl">
            Simulasikan dampak penyesuaian alokasi anggaran pembinaan cabor terhadap perolehan medali, atau dampak renovasi sarana olahraga terhadap masa pakai aset.
          </p>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
          <Info class="w-3.5 h-3.5" />
          Estimasi Berbasis Asumsi
        </span>
      </div>

      <!-- Mode Switcher -->
      <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
        <button
          @click="simulatorMode = 'budget'"
          type="button"
          class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition"
          :class="simulatorMode === 'budget' ? 'bg-brand-900 text-white shadow-sm' : 'bg-gray-100 text-text-main hover:bg-gray-200'"
        >
          Skenario 1: Anggaran Pembinaan Cabor
        </button>

        <button
          @click="simulatorMode = 'renovation'"
          type="button"
          class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition"
          :class="simulatorMode === 'renovation' ? 'bg-brand-900 text-white shadow-sm' : 'bg-gray-100 text-text-main hover:bg-gray-200'"
        >
          Skenario 2: Renovasi Sarana Olahraga
        </button>
      </div>
    </div>

    <!-- SKENARIO 1: ANGGARAN PEMBINAAN CABOR -->
    <div v-if="simulatorMode === 'budget'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Input Controls (5 cols) -->
      <div class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
        <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
          <Coins class="w-4 h-4 text-brand-600" />
          <span>Parameter Anggaran Pembinaan</span>
        </h3>

        <!-- Cabor Selector -->
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5">Pilih Cabang Olahraga</label>
          <select
            v-model="budgetForm.sportId"
            @change="runBudgetSim"
            class="w-full text-xs sm:text-sm rounded-lg border border-gray-200 px-3 py-2 bg-white text-text-main focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
          >
            <option v-for="s in store.sports" :key="s.id" :value="s.id">
              {{ s.name }}
            </option>
          </select>
        </div>

        <!-- Slider Change Pct -->
        <div class="space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-text-main">Penyesuaian Anggaran (%):</span>
            <span
              class="font-mono font-bold px-2 py-0.5 rounded text-xs"
              :class="budgetForm.changePct >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
            >
              {{ budgetForm.changePct >= 0 ? '+' : '' }}{{ budgetForm.changePct }}%
            </span>
          </div>

          <input
            v-model.number="budgetForm.changePct"
            @input="runBudgetSim"
            type="range"
            min="-50"
            max="100"
            step="5"
            class="w-full accent-brand-600 cursor-pointer"
          />

          <div class="flex justify-between text-[10px] text-muted font-mono">
            <span>-50% (Penghematan)</span>
            <span>0% (Tetap)</span>
            <span>+100% (Lipat Dua)</span>
          </div>
        </div>

        <div class="pt-2 flex items-center gap-3">
          <button
            @click="resetBudget"
            type="button"
            class="text-xs text-brand-600 hover:text-brand-900 font-semibold"
          >
            Reset ke Baseline (0%)
          </button>
        </div>

        <div class="p-3 bg-brand-50/50 rounded-xl border border-brand-100 text-xs text-muted leading-relaxed">
          <p>
            <b>Asumsi Model OLS:</b> Elastisitas anggaran diperkirakan sebesar 0.45, di mana kenaikan 10% alokasi dana pemusatan latihan (pelatda) diperkirakan meningkatkan probabilitas capaian medali sebesar 0.45 poin.
          </p>
        </div>
      </div>

      <!-- Hasil Simulasi (7 cols) -->
      <div class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <h3 class="font-bold text-base text-brand-900">Hasil Proyeksi Dampak Performa</h3>
            <span class="text-xs text-muted font-medium">Cabor: {{ budgetResult?.sport_name }}</span>
          </div>

          <!-- KPI Result Cards -->
          <div class="grid grid-cols-3 gap-3 my-4">
            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-center">
              <span class="text-[10px] uppercase tracking-wider text-muted block">Baseline</span>
              <span class="text-xl sm:text-2xl font-bold text-text-main mt-0.5 block">
                {{ budgetResult?.baseline_points }} pts
              </span>
            </div>

            <div class="p-3 rounded-xl bg-brand-50 border border-brand-200 text-center">
              <span class="text-[10px] uppercase tracking-wider text-brand-900 font-bold block">Simulasi</span>
              <span class="text-xl sm:text-2xl font-bold text-brand-900 mt-0.5 block">
                {{ budgetResult?.simulated_points }} pts
              </span>
            </div>

            <div
              class="p-3 rounded-xl border text-center"
              :class="budgetResult?.delta_points >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'"
            >
              <span class="text-[10px] uppercase tracking-wider block font-bold">Delta Selisih</span>
              <span class="text-xl sm:text-2xl font-bold mt-0.5 block">
                {{ budgetResult?.delta_points >= 0 ? '+' : '' }}{{ budgetResult?.delta_points }} pts
              </span>
            </div>
          </div>

          <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 space-y-2 text-xs">
            <h4 class="font-semibold text-text-main">Interpretasi Kebijakan:</h4>
            <p v-if="budgetForm.changePct > 0" class="text-muted leading-relaxed">
              Dengan penambahan anggaran sebesar <b>+{{ budgetForm.changePct }}%</b>, estimasi perolehan poin medali untuk cabor <b>{{ budgetResult?.sport_name }}</b> meningkat menjadi <b>{{ budgetResult?.simulated_points }} poin</b>. Program yang disarankan: penambahan intensitas uji tanding (try out) antardaerah dan suplemen atlet.
            </p>
            <p v-else-if="budgetForm.changePct < 0" class="text-muted leading-relaxed">
              Pemotongan anggaran sebesar <b>{{ budgetForm.changePct }}%</b> diperkirakan menurunkan kapasitas pemusatan latihan sebesar <b>{{ budgetResult?.delta_points }} poin</b>. Diperlukan prioritas atlet unggulan ring 1.
            </p>
            <p v-else class="text-muted leading-relaxed">
              Anggaran berjalan mempertahankan performa eksisting sesuai baseline proyeksi Popda/Porprov.
            </p>
          </div>
        </div>

        <ExplainPanel
          title="Bagaimana angka simulasi ini dihitung?"
          formula="Poin_Simulasi = Poin_Baseline + (Persen_Perubahan / 10) × Elastisitas_Model"
          description="Model mengasumsikan elastisitas responsif linier berskala moderat dengan koefisien 0.45. Output simulasi tidak menjamin medali secara deterministik, melainkan sebagai komparasi skenario alokasi belanja daerah."
        />
      </div>
    </div>

    <!-- SKENARIO 2: RENOVASI SARANA OLAHRAGA -->
    <div v-if="simulatorMode === 'renovation'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Input Controls (5 cols) -->
      <div class="lg:col-span-5 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
        <h3 class="font-bold text-base text-brand-900 flex items-center gap-2">
          <Wrench class="w-4 h-4 text-brand-600" />
          <span>Parameter Intervensi Renovasi Venue</span>
        </h3>

        <!-- Venue Selector -->
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5">Pilih Sarana Olahraga</label>
          <select
            v-model="renoForm.venueId"
            @change="runRenoSim"
            class="w-full text-xs sm:text-sm rounded-lg border border-gray-200 px-3 py-2 bg-white text-text-main focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
          >
            <option v-for="v in store.venues" :key="v.id" :value="v.id">
              {{ v.name }} (Skor Saat Ini: {{ v.score }})
            </option>
          </select>
        </div>

        <!-- Slider Target Score -->
        <div class="space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-text-main">Target Skor Pasca Perbaikan:</span>
            <span class="font-mono font-bold px-2 py-0.5 rounded bg-brand-50 text-brand-900 text-xs">
              {{ renoForm.targetScore }} / 100
            </span>
          </div>

          <input
            v-model.number="renoForm.targetScore"
            @input="runRenoSim"
            type="range"
            min="40"
            max="100"
            step="1"
            class="w-full accent-brand-600 cursor-pointer"
          />

          <div class="flex justify-between text-[10px] text-muted font-mono">
            <span>40 (Minimal)</span>
            <span>70 (Standar Baik)</span>
            <span>100 (Sempurna)</span>
          </div>
        </div>

        <div class="p-3 bg-brand-50/50 rounded-xl border border-brand-100 text-xs text-muted leading-relaxed">
          <p>
            <b>Model Depresiasi Aset:</b> Peningkatan mutu kondisi sarana menunda masa degradasi fisik dan memperpanjang estimasi tanggal pemeliharaan besar berikutnya.
          </p>
        </div>
      </div>

      <!-- Hasil Renovasi (7 cols) -->
      <div class="lg:col-span-7 bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <h3 class="font-bold text-base text-brand-900">Hasil Simulasi Kondisi & Masa Pakai</h3>
            <span class="text-xs text-muted font-medium">{{ renoResult?.venue_name }}</span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 my-4">
            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-center">
              <span class="text-[10px] uppercase tracking-wider text-muted block">Skor Lama vs Baru</span>
              <span class="text-lg sm:text-xl font-bold text-brand-900 mt-0.5 block">
                {{ renoResult?.baseline_score }} → {{ renoResult?.simulated_score }}
              </span>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-center">
              <span class="text-[10px] uppercase tracking-wider text-muted block">Perpanjangan Usia</span>
              <span class="text-lg sm:text-xl font-bold text-emerald-600 mt-0.5 block">
                +{{ renoResult?.life_extension_years }} Tahun
              </span>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-center col-span-2 sm:col-span-1">
              <span class="text-[10px] uppercase tracking-wider text-muted block">Status Baru</span>
              <div class="mt-1 flex justify-center">
                <StatusBadge :status="renoResult?.new_status" :score="renoResult?.simulated_score" size="sm" />
              </div>
            </div>
          </div>

          <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 space-y-2 text-xs">
            <h4 class="font-semibold text-text-main">Jadwal Pemeliharaan Pasca Intervensi:</h4>
            <p class="text-muted leading-relaxed">
              Dengan perbaikan fasilitas ke skor <b>{{ renoResult?.simulated_score }}</b>, perkiraan kebutuhan inspeksi dan pemeliharaan berat berikutnya mundur hingga tanggal <b>{{ renoResult?.estimated_next_maintenance }}</b>. Aset kembali layak menjadi arena pertandingan resmi.
            </p>
          </div>
        </div>

        <ExplainPanel
          title="Bagaimana rumus perpanjangan masa pakai dihitung?"
          formula="Usia_Perpanjangan = (Skor_Baru - Skor_Lama) × 0.08 Tahun"
          description="Kalkulasi ini mengacu pada standar depresiasi aset teknik sipil sarana prasarana olahraga di lingkungan dinas teknis pemerintah daerah."
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { usePublicDataStore } from '@/stores/publicData'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import ExplainPanel from '@/components/ui/ExplainPanel.vue'
import { Sliders, Coins, Wrench, Info } from 'lucide-vue-next'

const store = usePublicDataStore()

const simulatorMode = ref('budget')

const budgetForm = reactive({
  sportId: 2, // Default: Atletik
  changePct: 20, // +20%
})
const budgetResult = ref(null)

const renoForm = reactive({
  venueId: 4, // Default: Kolam Renang Madamba
  targetScore: 85,
})
const renoResult = ref(null)

onMounted(async () => {
  await store.fetchVenues()
  runBudgetSim()
  runRenoSim()
})

const runBudgetSim = async () => {
  budgetResult.value = await store.simulateBudget({
    sportId: budgetForm.sportId,
    changePct: budgetForm.changePct,
  })
}

const resetBudget = () => {
  budgetForm.changePct = 0
  runBudgetSim()
}

const runRenoSim = async () => {
  renoResult.value = await store.simulateRenovation({
    venueId: renoForm.venueId,
    targetScore: renoForm.targetScore,
  })
}
</script>