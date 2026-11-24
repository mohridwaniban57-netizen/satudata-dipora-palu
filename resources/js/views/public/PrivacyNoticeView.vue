<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="bg-surface rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm space-y-3">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
        <ShieldCheck class="w-3.5 h-3.5 text-emerald-600" />
        <span>Kepatuhan Regulasi UU No. 27 Tahun 2022</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-900 tracking-tight">
        Pernyataan Pelindungan Data Pribadi (UU PDP)
      </h1>
      <p class="text-xs sm:text-sm text-muted leading-relaxed">
        Komitmen Dinas Pemuda dan Olahraga Kota Palu dalam menjamin hak privasi atlet anak, remaja, dan pemuda di seluruh sistem informasi dan portal Satu Data Dispora.
      </p>
    </div>

    <!-- Poin-poin Pelindungan Data -->
    <div class="space-y-6">
      <!-- 1. Pseudonimisasi -->
      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
        <h2 class="text-base font-bold text-brand-900 flex items-center gap-2">
          <Key class="w-4 h-4 text-brand-600" />
          <span>1. Pseudonimisasi & Enkripsi Berlapis</span>
        </h2>
        <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
          Setiap atlet yang terdaftar di sistem diberikan kode pseudonim acak unik (misal: <code>ATL-0042</code>). Nama asli atlet dienkripsi pada tingkat database menggunakan algoritma <b>AES-256-CBC</b> dengan salt dinamis. Kunci enkripsi tersimpan secara terisolasi pada environment server (tidak pernah dikirim ke browser publik).
        </p>
      </div>

      <!-- 2. K-Anonymity & Masking -->
      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
        <h2 class="text-base font-bold text-brand-900 flex items-center gap-2">
          <EyeOff class="w-4 h-4 text-brand-600" />
          <span>2. Penerapan K-Anonymity (Ambang Batas k = 5)</span>
        </h2>
        <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
          Portal Satu Data secara otomatis menerapkan prinsip <i>k-anonymity</i> pada seluruh agregat statistik publik. Jika sebuah kombinasi atribut (misal: cabor tertentu di kecamatan tertentu) menghasilkan frekuensi kurang dari 5 orang, angka tersebut secara otomatis disamarkan (<i>small cell suppression</i>) menjadi label <b><code>&lt; 5</code></b> untuk mencegah upaya re-identifikasi individu.
        </p>
      </div>

      <!-- 3. Larangan Penyimpanan Data Sensitif -->
      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
        <h2 class="text-base font-bold text-brand-900 flex items-center gap-2">
          <Lock class="w-4 h-4 text-brand-600" />
          <span>3. Penghapusan Atribut Non-Esensial (Data Minimization)</span>
        </h2>
        <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
          Sesuai prinsip minimalisasi data, sistem Dispora Palu <b>tidak mempublikasikan</b> Nomor Induk Kependudukan (NIK), alamat tempat tinggal pribadi, nomor telepon genggam, maupun riwayat medis atlet pada portal publik. Hanya data prestasi dan afiliasi cabor yang diproses untuk evaluasi pembinaan olahraga daerah.
        </p>
      </div>

      <!-- 4. Perlindungan Data Khusus Anak -->
      <div class="bg-surface rounded-2xl border border-gray-100 p-6 shadow-sm space-y-3">
        <h2 class="text-base font-bold text-brand-900 flex items-center gap-2">
          <ShieldAlert class="w-4 h-4 text-amber-600" />
          <span>4. Pelindungan Khusus Atlet Usia Dini / Anak</span>
        </h2>
        <p class="text-xs sm:text-sm text-text-main/80 leading-relaxed">
          Mengacu pada Pasal 25 UU PDP mengenai pelindungan data anak di bawah 18 tahun, pendaftaran atlet pelajar (Popda/PPLPD) wajib menyertakan surat persetujuan (consent) tertulis dari orang tua atau wali resmi.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ShieldCheck, Key, EyeOff, Lock, ShieldAlert } from 'lucide-vue-next'
</script>