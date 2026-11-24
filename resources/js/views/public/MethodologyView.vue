<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm space-y-3">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-900 border border-brand-200">
        <FileCode class="w-3.5 h-3.5 text-brand-600" />
        <span>Dokumentasi Teknis & Metodologi</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
        Metodologi Analitik & Transparansi Data Hybrid
      </h1>
      <p class="text-xs sm:text-sm text-muted leading-relaxed">
        Penjelasan terbuka mengenai model matematika, algoritma estimasi statistik Ordinary Least Squares (OLS), formula penilaian aset sarana prasarana, serta pemisahan data riil dan data simulasi di Portal Satu Data Dispora Kota Palu.
      </p>
    </div>

    <!-- Section 1: Data Hybrid (Riil vs Simulasi) -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <h2 class="text-lg font-bold text-brand-900 flex items-center gap-2">
        <Layers class="w-5 h-5 text-brand-600" />
        <span>1. Arsitektur Data Hybrid (Riil vs Simulasi)</span>
      </h2>
      <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
        Untuk memenuhi prinsip keterbukaan informasi publik dan kejujuran data (honesty in analytics), portal ini secara eksplisit mengklasifikasikan setiap dataset menjadi dua kategori:
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50 space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold text-xs uppercase tracking-wider text-emerald-800">Data Riil (Terverifikasi)</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">Riil</span>
          </div>
          <p class="text-xs text-emerald-950/80 leading-relaxed">
            Data primer bersumber langsung dari dokumen resmi Dispora Kota Palu, meliputi: data perolehan medali Popda Sulteng 2024 & Porprov IX 2022, daftar sarana olahraga utama pemerintah daerah (Stadion Gawalise, GOR Siranindi, Lapangan Vatulemo, Tenis Kartini), dan batas wilayah administrasi 8 kecamatan.
          </p>
        </div>

        <div class="p-4 rounded-xl border border-dummy-border bg-dummy-light/60 space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold text-xs uppercase tracking-wider text-dummy">Data Simulasi (Synthetic Dummy)</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-dummy/15 text-dummy border border-dummy-border">Simulasi</span>
          </div>
          <p class="text-xs text-text-main/80 leading-relaxed">
            Data sintetis dibuat dengan pola distribusi realistis untuk melatih dan menguji model machine learning sebelum seluruh sarana memasang sensor/e-ticketing IoT. Ditandai dengan badge ungu <b>"Data Dummy"</b> di seluruh tabel dan grafik.
          </p>
        </div>
      </div>
    </div>

    <!-- Section 2: Model OLS Proyeksi Medali -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <h2 class="text-lg font-bold text-brand-900 flex items-center gap-2">
        <TrendingUp class="w-5 h-5 text-brand-600" />
        <span>2. Model Proyeksi Medali (Ordinary Least Squares)</span>
      </h2>
      <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
        Proyeksi medali menggunakan regresi linear univariat dan multivariat yang mengevaluasi tren poin historis. Pembobotan medali mengacu pada standar Pekan Olahraga Pelajar Daerah:
      </p>

      <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 font-mono text-xs space-y-2">
        <p class="font-bold text-brand-900">Total Poin = 3 × Emas + 2 × Perak + 1 × Perunggu</p>
        <p class="text-muted">ŷ = β₀ + β₁x₁ + β₂x₂ + ε</p>
        <p class="text-muted">Dimana: ŷ = estimasi poin event selanjutnya, x₁ = poin historis rata-rata, x₂ = logaritma alokasi anggaran binaan.</p>
      </div>

      <div class="space-y-2 text-xs text-text-main/80">
        <p><b>Confidence Interval (Tingkat Keyakinan):</b></p>
        <ul class="list-disc list-inside space-y-1 text-muted pl-2">
          <li><b>Tinggi:</b> Koefisien determinasi R² ≥ 0.80 dengan galat standar residual rendah.</li>
          <li><b>Sedang:</b> Koefisien determinasi 0.60 ≤ R² &lt; 0.80.</li>
          <li><b>Rendah:</b> Koefisien determinasi R² &lt; 0.60 (sering terjadi pada cabang olahraga baru atau riwayat data &lt; 3 event).</li>
        </ul>
      </div>
    </div>

    <!-- Section 3: Formula Penilaian Kondisi Fisik Fasilitas -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <h2 class="text-lg font-bold text-brand-900 flex items-center gap-2">
        <Wrench class="w-5 h-5 text-brand-600" />
        <span>3. Formula Penilaian Skor Kondisi Sarana (0–100)</span>
      </h2>
      <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
        Kondisi fisik sarana prasarana dinilai dengan formula terbobot:
      </p>

      <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 font-mono text-xs space-y-2">
        <p class="font-bold text-brand-900">
          Skor_Kondisi = 0.50 × Skor_Inspeksi + 0.30 × (1 - (Usia / Masa_Pakai)) × 100 - 0.20 × Penalti_Insiden
        </p>
      </div>

      <div class="grid grid-cols-3 gap-3 text-xs pt-1">
        <div class="p-3 rounded-lg border border-emerald-200 bg-emerald-50/40 text-center">
          <span class="font-bold text-emerald-800 block">Skor 70 - 100</span>
          <span class="text-emerald-700 text-[11px]">Kondisi Baik (Preventif)</span>
        </div>
        <div class="p-3 rounded-lg border border-amber-200 bg-amber-50/40 text-center">
          <span class="font-bold text-amber-800 block">Skor 45 - 69</span>
          <span class="text-amber-700 text-[11px]">Waspada (Monitoring)</span>
        </div>
        <div class="p-3 rounded-lg border border-rose-200 bg-rose-50/40 text-center">
          <span class="font-bold text-rose-800 block">Skor &lt; 45</span>
          <span class="text-rose-700 text-[11px]">Kritis (Renovasi Berat)</span>
        </div>
      </div>
    </div>

    <!-- Section 4: Matriks Kuadran Retribusi -->
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
      <h2 class="text-lg font-bold text-brand-900 flex items-center gap-2">
        <Coins class="w-5 h-5 text-brand-600" />
        <span>4. Matriks 2×2 Pemanfaatan & Retribusi</span>
      </h2>
      <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
        Matriks mengklasifikasikan aset olahraga ke dalam 4 kuadran menggunakan titik potong median tahunan untuk membantu perumusan kebijakan tarif dan pengetatan kebocoran pendapatan asli daerah.
      </p>
    </div>
  </div>
</template>

<script setup>
import { FileCode, Layers, TrendingUp, Wrench, Coins } from 'lucide-vue-next'
</script>