<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-900 via-brand-800 to-brand-900 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 sm:p-8 space-y-6">
      <!-- Brand Logo -->
      <div class="text-center space-y-2">
        <img
          src="/images/logo/logo-palu.png"
          alt="Logo Resmi Dispora Kota Palu"
          class="w-16 h-auto mx-auto object-contain drop-shadow-sm mb-1"
        />
        <h1 class="text-xl font-bold text-brand-900">Masuk Panel Admin</h1>
        <p class="text-xs text-muted">Dinas Pemuda dan Olahraga Pemerintah Kota Palu</p>
      </div>

      <!-- Expired alert -->
      <div v-if="isExpired" class="p-3 bg-amber-50 text-amber-800 rounded-xl text-xs border border-amber-200">
        Sesi Anda telah berakhir. Silakan login kembali.
      </div>

      <!-- Error alert -->
      <div v-if="authStore.error" class="p-3 bg-rose-50 text-rose-800 rounded-xl text-xs border border-rose-200">
        {{ authStore.error }}
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-text-main mb-1">Alamat Email Resmi</label>
          <div class="relative">
            <Mail class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input
              v-model="email"
              type="email"
              required
              class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              placeholder="admin@dispora-palu.go.id"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-text-main mb-1">Kata Sandi (Password)</label>
          <div class="relative">
            <Lock class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full pl-9 pr-9 py-2 text-xs sm:text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-text-main"
            >
              <Eye v-if="!showPassword" class="w-4 h-4" />
              <EyeOff v-else class="w-4 h-4" />
            </button>
          </div>
        </div>

        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-900 hover:bg-brand-800 focus:ring-4 focus:ring-brand-500/20 transition shadow-md disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <Loader2 v-if="authStore.loading" class="w-4 h-4 animate-spin" />
          <span>{{ authStore.loading ? 'Memverifikasi...' : 'Masuk ke Sistem' }}</span>
        </button>
      </form>

      <div class="text-center pt-2">
        <router-link to="/" class="text-xs text-brand-600 hover:text-brand-900 font-medium">
          ← Kembali ke Portal Publik
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ShieldCheck, Mail, Lock, Eye, EyeOff, Loader2 } from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const isExpired = ref(false)

onMounted(() => {
  if (route.query.expired) {
    isExpired.value = true
  }
})

const handleLogin = async () => {
  const res = await authStore.login(email.value, password.value)
  if (res.success) {
    router.push('/admin')
  }
}
</script>
