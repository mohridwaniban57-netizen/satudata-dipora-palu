<template>
  <div class="min-h-screen flex bg-bg text-text-main font-sans">
    <!-- Sidebar Desktop -->
    <aside class="w-64 bg-brand-900 text-white shrink-0 hidden md:flex flex-col justify-between shadow-xl z-20">
      <div>
        <!-- Logo -->
        <div class="h-16 px-5 border-b border-brand-800 flex items-center gap-3">
          <img
            src="/images/logo/logo-palu.png"
            alt="Logo Dispora Palu"
            class="w-8 h-auto object-contain drop-shadow-xs"
          />
          <div>
            <h1 class="text-sm font-bold tracking-tight leading-tight">Satu Data Dispora</h1>
            <p class="text-[10px] text-brand-100/70 uppercase tracking-wider mt-0.5">Kota Palu • Panel Admin</p>
          </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-3 space-y-1">
          <router-link
            v-for="item in adminNavItems"
            :key="item.path"
            :to="item.path"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs sm:text-sm font-medium transition-colors"
            :class="route.path === item.path ? 'bg-brand-600 text-white shadow-sm' : 'text-brand-100 hover:bg-brand-800/80 hover:text-white'"
          >
            <component :is="item.icon" class="w-4 h-4 shrink-0 text-brand-100" />
            <span>{{ item.label }}</span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom Sidebar Actions -->
      <div class="p-4 border-t border-brand-800 space-y-2">
        <router-link
          to="/"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-brand-100 hover:bg-brand-800 transition"
        >
          <ExternalLink class="w-4 h-4 text-accent-400" />
          <span>Ke Portal Publik</span>
        </router-link>

        <button
          @click="handleLogout"
          type="button"
          class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-rose-200 hover:bg-rose-900/40 hover:text-rose-100 transition"
        >
          <LogOut class="w-4 h-4 text-rose-400" />
          <span>Keluar (Logout)</span>
        </button>
      </div>
    </aside>

    <!-- Main Admin Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Admin Topbar -->
      <header class="h-16 bg-surface border-b border-gray-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center gap-3">
          <button
            @click="mobileSidebarOpen = !mobileSidebarOpen"
            type="button"
            class="md:hidden p-2 rounded-lg text-muted hover:bg-gray-100"
          >
            <Menu class="w-5 h-5" />
          </button>
          <div>
            <h2 class="text-sm sm:text-base font-bold text-brand-900">{{ currentTitle }}</h2>
            <p class="text-[10px] text-muted hidden sm:block">Kelola master data, validasi draft & monitoring log audit</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right hidden sm:block">
            <span class="text-xs font-semibold text-text-main block">{{ authStore.adminName }}</span>
            <span class="text-[10px] text-muted">{{ authStore.userEmail }}</span>
          </div>
          <div class="w-9 h-9 rounded-full bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-900 font-bold text-xs">
            AD
          </div>
        </div>
      </header>

      <!-- Mobile Sidebar Drawer -->
      <div
        v-if="mobileSidebarOpen"
        class="fixed inset-0 z-50 md:hidden flex"
      >
        <div class="fixed inset-0 bg-black/50" @click="mobileSidebarOpen = false"></div>
        <div class="relative w-64 bg-brand-900 text-white flex flex-col justify-between z-10 p-4">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-brand-800 mb-4">
              <span class="font-bold text-sm">Menu Admin</span>
              <button @click="mobileSidebarOpen = false" class="p-1 rounded text-white/70">
                <X class="w-5 h-5" />
              </button>
            </div>
            <nav class="space-y-1">
              <router-link
                v-for="item in adminNavItems"
                :key="item.path"
                :to="item.path"
                @click="mobileSidebarOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium"
                :class="route.path === item.path ? 'bg-brand-600 text-white' : 'text-brand-100 hover:bg-brand-800'"
              >
                <component :is="item.icon" class="w-4 h-4 shrink-0" />
                <span>{{ item.label }}</span>
              </router-link>
            </nav>
          </div>

          <div class="pt-4 border-t border-brand-800 space-y-2">
            <router-link to="/" class="block text-xs text-brand-100 py-1.5">Portal Publik</router-link>
            <button @click="handleLogout" class="text-xs text-rose-300 py-1.5 block">Keluar</button>
          </div>
        </div>
      </div>

      <!-- Main Body -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  LayoutDashboard,
  Building2,
  Users,
  Trophy,
  Users2,
  Coins,
  UploadCloud,
  History,
  ExternalLink,
  LogOut,
  ShieldCheck,
  Menu,
  X,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const mobileSidebarOpen = ref(false)
const adminNavItems = [
  { path: '/admin', label: 'Dashboard Admin', icon: LayoutDashboard },
  { path: '/admin/venues', label: 'Sarana Prasarana', icon: Building2 },
  { path: '/admin/athletes', label: 'Atlet & Prestasi', icon: Users },
  { path: '/admin/medals', label: 'Riwayat Medali', icon: Trophy },
  { path: '/admin/youth-orgs', label: 'Organisasi Pemuda', icon: Users2 },
  { path: '/admin/budgets', label: 'Anggaran Pembinaan', icon: Coins },
  { path: '/admin/import', label: 'Impor Data CSV', icon: UploadCloud },
  { path: '/admin/audit-logs', label: 'Log Audit Sistem', icon: History },
]

const currentTitle = computed(() => {
  const current = adminNavItems.find((i) => i.path === route.path)
  return current ? current.label : (route.meta?.title || 'Panel Admin')
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/admin/login')
}
</script>