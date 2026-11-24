import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import PublicLayout from '@/layouts/PublicLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

// Public Views
import HomeView from '@/views/public/HomeView.vue'
import FacilityMapView from '@/views/public/FacilityMapView.vue'
import VenueDetailView from '@/views/public/VenueDetailView.vue'
import AthletesYouthView from '@/views/public/AthletesYouthView.vue'
import PredictionsView from '@/views/public/PredictionsView.vue'
import SimulatorView from '@/views/public/SimulatorView.vue'
import MethodologyView from '@/views/public/MethodologyView.vue'
import PrivacyNoticeView from '@/views/public/PrivacyNoticeView.vue'

// Admin Views
import LoginView from '@/views/admin/LoginView.vue'
import AdminDashboardView from '@/views/admin/AdminDashboardView.vue'
import VenueCrudView from '@/views/admin/VenueCrudView.vue'
import AthleteCrudView from '@/views/admin/AthleteCrudView.vue'
import MedalCrudView from '@/views/admin/MedalCrudView.vue'
import YouthOrgCrudView from '@/views/admin/YouthOrgCrudView.vue'
import BudgetCrudView from '@/views/admin/BudgetCrudView.vue'
import CsvImportView from '@/views/admin/CsvImportView.vue'
import AuditLogView from '@/views/admin/AuditLogView.vue'

const routes = [
  // Public Portal (no login required)
  {
    path: '/',
    component: PublicLayout,
    children: [
      {
        path: '',
        name: 'home',
        component: HomeView,
        meta: { title: 'Beranda Eksekutif' },
      },
      {
        path: 'map',
        name: 'facility-map',
        component: FacilityMapView,
        meta: { title: 'Peta Interaktif Fasilitas Olahraga' },
      },
      {
        path: 'venues/:id',
        name: 'venue-detail',
        component: VenueDetailView,
        meta: { title: 'Detail Fasilitas' },
      },
      {
        path: 'athletes',
        name: 'athletes-youth',
        component: AthletesYouthView,
        meta: { title: 'Statistik Atlet & Kepemudaan' },
      },
      {
        path: 'predictions',
        name: 'predictions',
        component: PredictionsView,
        meta: { title: 'Prediksi & Wawasan Kebijakan' },
      },
      {
        path: 'simulator',
        name: 'simulator',
        component: SimulatorView,
        meta: { title: 'Simulator Skenario Kebijakan' },
      },
      {
        path: 'methodology',
        name: 'methodology',
        component: MethodologyView,
        meta: { title: 'Metodologi & Data Hybrid' },
      },
      {
        path: 'privacy',
        name: 'privacy',
        component: PrivacyNoticeView,
        meta: { title: 'Pernyataan Privasi (UU PDP)' },
      },
    ],
  },

  // Admin Login (Standalone)
  {
    path: '/admin/login',
    name: 'admin-login',
    component: LoginView,
    meta: { title: 'Masuk Admin Dispora' },
  },

  // Admin Panel (Protected by Auth Guard)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'admin-dashboard',
        component: AdminDashboardView,
        meta: { title: 'Dashboard Administrasi' },
      },
      {
        path: 'venues',
        name: 'admin-venues',
        component: VenueCrudView,
        meta: { title: 'Kelola Sarana Prasarana' },
      },
      {
        path: 'athletes',
        name: 'admin-athletes',
        component: AthleteCrudView,
        meta: { title: 'Kelola Atlet & Prestasi' },
      },
      {
        path: 'medals',
        name: 'admin-medals',
        component: MedalCrudView,
        meta: { title: 'Kelola Riwayat Medali' },
      },
      {
        path: 'youth-orgs',
        name: 'admin-youth-orgs',
        component: YouthOrgCrudView,
        meta: { title: 'Kelola Organisasi Pemuda' },
      },
      {
        path: 'budgets',
        name: 'admin-budgets',
        component: BudgetCrudView,
        meta: { title: 'Kelola Anggaran Pembinaan' },
      },
      {
        path: 'import',
        name: 'admin-import',
        component: CsvImportView,
        meta: { title: 'Impor Data CSV' },
      },
      {
        path: 'audit-logs',
        name: 'admin-audit-logs',
        component: AuditLogView,
        meta: { title: 'Log Audit Perubahan Data' },
      },
    ],
  },

  // Fallback redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0 }
  },
})

// Navigation Guard for Admin Routes
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.title) {
    document.title = `${to.meta.title} — Satu Data Dispora Palu`
  }

  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!authStore.isAuthenticated) {
      next({
        path: '/admin/login',
        query: { redirect: to.fullPath },
      })
    } else {
      next()
    }
  } else if (to.path === '/admin/login' && authStore.isAuthenticated) {
    next('/admin')
  } else {
    next()
  }
})

export default router
