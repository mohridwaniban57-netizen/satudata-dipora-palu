<template>
  <div class="min-h-screen flex flex-col bg-bg text-text-main">
    <!-- Top Announcement Bar (Hybrid Data Notice) -->
    <div class="bg-brand-900 text-white text-xs py-1.5 px-4">
      <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 text-center sm:text-left">
        <div class="flex items-center gap-2 min-w-0">
          <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-accent-500 text-brand-900 font-bold uppercase tracking-wider shrink-0">
            Portal Publik
          </span>
          <span class="truncate font-medium">Satu Data Dinas Pemuda dan Olahraga Pemerintah Kota Palu</span>
        </div>
        <div class="flex items-center gap-3 text-white/80 text-[11px] shrink-0 whitespace-nowrap">
          <span>Data Freshness: {{ formattedLastUpdated }}</span>
        </div>
      </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-surface border-b border-gray-200/80 sticky top-0 z-40 shadow-xs backdrop-blur-md bg-white/95">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo & Brand -->
          <router-link to="/" class="flex items-center gap-3 group">
            <img
              src="/images/logo/logo-palu.png"
              alt="Logo Resmi Dinas Pemuda dan Olahraga Kota Palu"
              class="w-9 sm:w-10 h-auto max-h-12 object-contain drop-shadow-xs group-hover:scale-105 transition-transform"
            />
            <div>
              <span class="font-extrabold text-base tracking-tight text-brand-900 flex items-center gap-1.5 leading-tight">
                SATU DATA <span class="text-brand-600 font-semibold">DISPORA</span>
              </span>
              <p class="text-[10px] font-semibold text-muted tracking-wide mt-0.5">DINAS PEMUDA DAN OLAHRAGA KOTA PALU</p>
            </div>
          </router-link>

          <!-- Desktop Navigation Links -->
          <nav class="hidden lg:flex items-center gap-0.5 xl:gap-1 shrink-0">
            <router-link
              v-for="link in navLinks"
              :key="link.path"
              :to="link.path"
              class="px-2.5 xl:px-3 py-2 rounded-lg text-xs xl:text-sm font-medium transition-colors whitespace-nowrap shrink-0"
              :class="route.path === link.path ? 'bg-brand-50 text-brand-900 font-semibold' : 'text-text-main/80 hover:text-brand-900 hover:bg-gray-100/60'"
            >
              {{ link.label }}
            </router-link>
          </nav>

          <!-- Right Action: Admin Login Button -->
          <div class="hidden sm:flex items-center gap-3 shrink-0">
            <router-link
              v-if="!authStore.isAuthenticated"
              to="/admin/login"
              class="group relative inline-flex items-center gap-2 pl-2 pr-3.5 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-brand-900 via-brand-800 to-brand-900 text-white border border-brand-700/80 hover:border-accent-400/60 shadow-xs hover:shadow-md hover:shadow-brand-900/20 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200"
            >
              <span class="w-5 h-5 rounded-lg bg-accent-400/15 border border-accent-400/30 flex items-center justify-center text-accent-400 group-hover:bg-accent-400 group-hover:text-brand-950 group-hover:border-accent-400 transition-all duration-200 shrink-0">
                <Lock class="w-3 h-3 transition-transform duration-200 group-hover:scale-110" />
              </span>
              <span class="tracking-wide">Masuk Admin</span>
              <ChevronRight class="w-3.5 h-3.5 text-white/40 group-hover:text-accent-400 group-hover:translate-x-0.5 transition-all duration-200" />
            </router-link>

            <router-link
              v-else
              to="/admin"
              class="group relative inline-flex items-center gap-2 pl-2.5 pr-3.5 py-1.5 rounded-xl text-xs font-bold bg-brand-50 hover:bg-brand-100 text-brand-900 border border-brand-200 hover:border-brand-300 shadow-xs hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
            >
              <span class="relative flex h-2 w-2 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
              </span>
              <LayoutDashboard class="w-3.5 h-3.5 text-brand-700 group-hover:text-brand-900 transition-colors" />
              <span>Panel Admin</span>
              <ChevronRight class="w-3.5 h-3.5 text-brand-400 group-hover:translate-x-0.5 transition-transform" />
            </router-link>
          </div>

          <!-- Mobile Hamburger Button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            type="button"
            class="lg:hidden p-2 rounded-lg text-muted hover:text-text-main hover:bg-gray-100 focus:outline-none"
          >
            <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
            <X v-else class="w-6 h-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div v-show="mobileMenuOpen" class="lg:hidden border-t border-gray-100 bg-surface px-4 pt-2 pb-4 space-y-1">
        <router-link
          v-for="link in navLinks"
          :key="link.path"
          :to="link.path"
          @click="mobileMenuOpen = false"
          class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="route.path === link.path ? 'bg-brand-50 text-brand-900 font-semibold' : 'text-text-main/80 hover:bg-gray-50'"
        >
          {{ link.label }}
        </router-link>

        <div class="pt-3 border-t border-gray-100">
          <router-link
            v-if="!authStore.isAuthenticated"
            to="/admin/login"
            @click="mobileMenuOpen = false"
            class="group flex items-center justify-center gap-2.5 w-full py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-brand-900 via-brand-800 to-brand-900 text-white border border-brand-700/80 shadow-sm hover:shadow-md transition-all active:scale-[0.98]"
          >
            <span class="w-5 h-5 rounded-lg bg-accent-400/15 border border-accent-400/30 flex items-center justify-center text-accent-400 group-hover:bg-accent-400 group-hover:text-brand-950 transition-all shrink-0">
              <Lock class="w-3 h-3" />
            </span>
            <span class="tracking-wide">Masuk Admin</span>
            <ChevronRight class="w-3.5 h-3.5 text-white/50 group-hover:text-accent-400 group-hover:translate-x-0.5 transition-all" />
          </router-link>
          <router-link
            v-else
            to="/admin"
            @click="mobileMenuOpen = false"
            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-bold bg-brand-50 text-brand-900 border border-brand-200 shadow-2xs hover:bg-brand-100 transition-all"
          >
            <span class="relative flex h-2 w-2 shrink-0">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <LayoutDashboard class="w-4 h-4 text-brand-600" />
            <span>Ke Panel Admin</span>
          </router-link>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 text-xs text-muted mt-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Col 1: Identity -->
          <div class="md:col-span-2 space-y-3">
            <div class="flex items-center gap-2.5">
              <img
                src="/images/logo/logo-palu.png"
                alt="Logo Resmi Dispora Kota Palu"
                class="w-7 h-auto object-contain drop-shadow-2xs"
              />
              <span class="font-bold text-sm text-brand-900">Satu Data Dispora Kota Palu</span>
            </div>
            <p class="text-xs leading-relaxed max-w-md">
              Pusat data terintegrasi sarana prasarana olahraga, pembinaan atlet, prestasi medali daerah, dan organisasi kepemudaan berbasis analitik prediktif.
            </p>
            <p class="text-[11px] text-gray-500">
              Alamat: Kompleks Kantor Walikota Palu, Jl. Balai Kota No. 1, Kota Palu, Sulawesi Tengah.
            </p>
          </div>

          <!-- Col 2: Navigasi Cepat -->
          <div>
            <h5 class="font-bold text-xs uppercase tracking-wider text-text-main mb-3">Navigasi Utama</h5>
            <ul class="space-y-2">
              <li><router-link to="/" class="hover:text-brand-600 transition">Beranda Eksekutif</router-link></li>
              <li><router-link to="/map" class="hover:text-brand-600 transition">Peta Interaktif Fasilitas</router-link></li>
              <li><router-link to="/athletes" class="hover:text-brand-600 transition">Statistik Atlet & Pemuda</router-link></li>
              <li><router-link to="/predictions" class="hover:text-brand-600 transition">Prediksi & Wawasan Retribusi</router-link></li>
              <li><router-link to="/simulator" class="hover:text-brand-600 transition">Simulator Kebijakan</router-link></li>
            </ul>
          </div>

          <!-- Col 3: Transparansi & Regulasi -->
          <div>
            <h5 class="font-bold text-xs uppercase tracking-wider text-text-main mb-3">Transparansi & Etika</h5>
            <ul class="space-y-2">
              <li><router-link to="/methodology" class="hover:text-brand-600 transition">Metodologi & Data Hybrid</router-link></li>
              <li><router-link to="/privacy" class="hover:text-brand-600 transition">Kebijakan Privasi (UU PDP)</router-link></li>
              <li>
                <span class="inline-flex items-center gap-1 text-[11px] text-dummy">
                  <span class="w-2 h-2 rounded-full bg-dummy"></span>
                  Label Data Simulasi Aktif
                </span>
              </li>
            </ul>
          </div>
        </div>

        <div class="border-t border-gray-100 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px]">
          <p>
            © 2026 Dinas Pemuda dan Olahraga Kota Palu. Hak cipta dilindungi undang-undang.
          </p>
          <p class="text-gray-500 italic text-center md:text-right max-w-xl">
            "Prediksi analitik merupakan estimasi pemodelan statistik (OLS) sebagai instrumen pendukung keputusan (decision support system)."
          </p>
        </div>
      </div>
    </footer>

    <!-- Global Catalog Modal (Chart 1 - 25) -->
    <ChartCatalogModal />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { usePublicDataStore } from '@/stores/publicData'
import { useAuthStore } from '@/stores/auth'
import { Activity, Menu, X, Lock, LayoutDashboard, ChevronRight } from 'lucide-vue-next'
import ChartCatalogModal from '@/components/ui/ChartCatalogModal.vue'

const route = useRoute()
const store = usePublicDataStore()
const authStore = useAuthStore()

const mobileMenuOpen = ref(false)

const navLinks = [
  { path: '/', label: 'Beranda' },
  { path: '/map', label: 'Peta Fasilitas' },
  { path: '/athletes', label: 'Atlet & Pemuda' },
  { path: '/predictions', label: 'Prediksi' },
  { path: '/simulator', label: 'Simulator Kebijakan' },
]

const formattedLastUpdated = computed(() => {
  if (!store.lastUpdated) return 'Hari ini'
  try {
    const d = new Date(store.lastUpdated)
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return 'Hari ini'
  }
})
</script>