import { defineStore } from 'pinia'
import api from '@/services/api'
import {
  mockSummary,
  mockVenues,
  mockHeatmap,
  mockMedalHistory,
  mockMedalProjections,
  mockAthletesStats,
  mockYouthOrgs,
  districts,
  venueTypes,
  sports,
} from '@/data/mockData'

export const usePublicDataStore = defineStore('publicData', {
  state: () => ({
    summary: { ...mockSummary },
    venues: [...mockVenues],
    selectedVenue: null,
    heatmap: [...mockHeatmap],
    medalHistory: [...mockMedalHistory],
    medalProjections: [...mockMedalProjections],
    athletesStats: { ...mockAthletesStats },
    youthOrgs: [...mockYouthOrgs],
    districts: [...districts],
    venueTypes: [...venueTypes],
    sports: [...sports],
    loading: false,
    lastUpdated: mockSummary.last_updated,
    dummyRatio: mockSummary.dummy_ratio,
  }),

  getters: {
    venuesGood: (state) => state.venues.filter((v) => v.status === 'good'),
    venuesWatch: (state) => state.venues.filter((v) => v.status === 'watch'),
    venuesCritical: (state) => state.venues.filter((v) => v.status === 'critical'),
    topPriorityVenues: (state) => {
      // sort by score ascending (lowest score = highest risk/priority)
      return [...state.venues].sort((a, b) => a.score - b.score).slice(0, 5)
    },
    venueById: (state) => (id) => state.venues.find((v) => Number(v.id) === Number(id)),
  },

  actions: {
    async fetchSummary() {
      try {
        const res = await api.get('/public/summary')
        if (res.data?.data) {
          const raw = res.data.data
          const cond = raw.condition_distribution || {}
          this.summary = {
            ...this.summary,
            ...raw,
            total_venues: raw.total_venues ?? this.summary.total_venues,
            total_athletes: raw.total_athletes ?? this.summary.total_athletes,
            venues_good: cond.good_count ?? raw.venues_good ?? 7,
            pct_good: cond.good_pct ?? raw.pct_good ?? 28,
            venues_watch: cond.watch_count ?? raw.venues_watch ?? 18,
            pct_watch: cond.watch_pct ?? raw.pct_watch ?? 72,
            venues_critical: cond.critical_count ?? raw.venues_critical ?? 0,
            pct_critical: cond.critical_pct ?? raw.pct_critical ?? 0,
            realisasi_retribusi: raw.total_levy_12m || raw.realisasi_retribusi || 54000000,
            pct_retribusi: raw.pct_retribusi || 72,
          }
          this.lastUpdated = res.data.meta?.last_updated || new Date().toISOString()
          this.dummyRatio = res.data.meta?.dummy_ratio ?? 0.65
        }
      } catch (err) {
        console.info('Menggunakan data summary lokal (fallback):', err.message)
      }
    },

    async fetchVenues(params = {}) {
      this.loading = true
      try {
        const res = await api.get('/public/venues', { params })
        if (res.data?.data && Array.isArray(res.data.data) && res.data.data.length > 0) {
          this.venues = res.data.data.map((v) => {
            const statusLower = (v.status || v.condition_status || 'good').toLowerCase()
            return {
              ...v,
              status: statusLower,
              condition_status: statusLower,
              score: v.score ?? v.condition_score ?? 70,
              condition_score: v.score ?? v.condition_score ?? 70,
              district_name: v.district_name || v.district || '',
              district: v.district || v.district_name || '',
              type_name: v.type_name || v.type || '',
              type: v.type || v.type_name || '',
            }
          })
        }
      } catch (err) {
        console.info('Menggunakan data venue lokal (fallback):', err.message)
      } finally {
        this.loading = false
      }
    },

    async fetchVenue(id) {
      try {
        const res = await api.get(`/public/venues/${id}`)
        if (res.data?.data) {
          this.selectedVenue = res.data.data
          return res.data.data
        }
      } catch (err) {
        console.info('Menggunakan data detail venue lokal (fallback):', err.message)
        const found = this.venueById(id)
        if (found) this.selectedVenue = found
        return found
      }
    },

    async fetchHeatmap() {
      try {
        const res = await api.get('/public/heatmap')
        if (res.data?.data) {
          this.heatmap = res.data.data
        }
      } catch (err) {
        console.info('Menggunakan data heatmap lokal (fallback):', err.message)
      }
    },

    async fetchAthletesStats() {
      try {
        const res = await api.get('/public/stats/athletes')
        if (res.data?.data) {
          const raw = res.data.data
          let total = raw.total
          if (!total && Array.isArray(raw.by_sport)) {
            total = raw.by_sport.reduce((sum, s) => sum + (typeof s.count === 'number' ? s.count : 0), 0)
          }
          if (!total && Array.isArray(raw.by_district)) {
            total = raw.by_district.reduce((sum, d) => sum + (typeof d.count === 'number' ? d.count : 0), 0)
          }

          let byAgeGender = raw.by_age_gender
          if (!byAgeGender && Array.isArray(raw.demographics)) {
            byAgeGender = raw.demographics.map((demo) => ({
              group: demo.age_group || demo.group || '',
              male: typeof demo.male_count === 'number' ? demo.male_count : (demo.male || 0),
              female: typeof demo.female_count === 'number' ? demo.female_count : (demo.female || 0),
              total: typeof demo.total_count === 'number' ? demo.total_count : (demo.total || 0),
            }))
          }

          this.athletesStats = {
            ...raw,
            total: total || this.athletesStats.total || 148,
            by_sport: Array.isArray(raw.by_sport) ? raw.by_sport : (this.athletesStats.by_sport || []),
            by_district: Array.isArray(raw.by_district) ? raw.by_district : (this.athletesStats.by_district || []),
            by_level: Array.isArray(raw.by_level) ? raw.by_level : (this.athletesStats.by_level || []),
            by_age_gender: byAgeGender || this.athletesStats.by_age_gender || [],
          }
        }
      } catch (err) {
        console.info('Menggunakan data atlet lokal (fallback):', err.message)
      }
    },

    async fetchMedalStats() {
      try {
        const res = await api.get('/public/stats/medals')
        if (res.data?.data) {
          if (Array.isArray(res.data.data)) {
            this.medalHistory = res.data.data
          } else if (Array.isArray(res.data.data.by_event)) {
            const apiEvents = res.data.data.by_event.map((e) => ({
              ...e,
              total: (e.gold || 0) + (e.silver || 0) + (e.bronze || 0),
            }))
            this.medalHistory = apiEvents.length >= 2 ? apiEvents : [...mockMedalHistory.slice(0, 2), ...apiEvents]
          }
        }
      } catch (err) {
        console.info('Menggunakan data riwayat medali lokal (fallback):', err.message)
      }
    },

    async fetchYouthOrgs() {
      try {
        const res = await api.get('/public/stats/youth-orgs')
        if (res.data?.data) {
          if (Array.isArray(res.data.data)) {
            this.youthOrgs = res.data.data
          } else if (Array.isArray(res.data.data.by_district)) {
            this.youthOrgs = res.data.data.by_district
          }
        }
      } catch (err) {
        console.info('Menggunakan data organisasi pemuda lokal (fallback):', err.message)
      }
    },

    async fetchPredictions() {
      try {
        const resMedals = await api.get('/public/predictions/medals')
        if (resMedals.data?.data) {
          const rawProjections = Array.isArray(resMedals.data.data)
            ? resMedals.data.data
            : (resMedals.data.data.projections || [])

          if (rawProjections.length > 0) {
            this.medalProjections = rawProjections.map((p) => {
              const lastPoints = p.last_points ?? (p.history?.[0]?.points || p.projected_points || 15)
              const minPoints = p.min_points ?? p.points_range_min ?? Math.max(0, p.projected_points - 3)
              const maxPoints = p.max_points ?? p.points_range_max ?? (p.projected_points + 4)
              return {
                ...p,
                last_points: lastPoints,
                min_points: minPoints,
                max_points: maxPoints,
                confidence: p.confidence ?? p.confidence_label ?? 'Sedang',
                r_squared: p.r_squared ?? 0.76,
              }
            })
          }
        }
      } catch (e) {
        // fallback
      }
    },

    async simulateBudget({ sportId, changePct }) {
      try {
        const res = await api.post('/public/simulate/budget', {
          sport_id: sportId,
          change_pct: changePct,
        })
        return res.data.data
      } catch (err) {
        // Linear regression simulator fallback logic based on OLS model in spec:
        const sport = this.sports.find((s) => s.id === Number(sportId)) || { name: 'Cabor' }
        const proj = this.medalProjections.find((p) => p.sport_id === Number(sportId)) || {
          projected_points: 20,
        }
        const baseline = proj.projected_points
        // Elasticity assumption from spec: ~0.45 point impact per 10% budget shift
        const elasticity = 0.45
        const delta = Math.round((changePct / 10) * elasticity * 10) / 10
        const simulated = Math.max(0, Math.round((baseline + delta) * 10) / 10)

        return {
          sport_id: sportId,
          sport_name: sport.name,
          change_pct: changePct,
          baseline_points: baseline,
          simulated_points: simulated,
          delta_points: Math.round((simulated - baseline) * 10) / 10,
          confidence: 'Sedang',
          note: 'Estimasi berbasis asumsi OLS linear regression (elastisitas anggaran 0.45)',
        }
      }
    },

    async simulateRenovation({ venueId, targetScore }) {
      try {
        const res = await api.post('/public/simulate/renovation', {
          venue_id: venueId,
          target_score: targetScore,
        })
        return res.data.data
      } catch (err) {
        const venue = this.venueById(venueId) || { name: 'Venue', score: 50 }
        let newStatus = 'critical'
        if (targetScore >= 70) newStatus = 'good'
        else if (targetScore >= 45) newStatus = 'watch'

        // Maintenance formula: next maintenance extends by factor based on score
        const daysExtension = Math.round((targetScore / 100) * 180)
        const nextDate = new Date()
        nextDate.setDate(nextDate.getDate() + daysExtension)

        return {
          venue_id: venueId,
          venue_name: venue.name,
          baseline_score: venue.score,
          simulated_score: targetScore,
          old_status: venue.status,
          new_status: newStatus,
          estimated_next_maintenance: nextDate.toISOString().split('T')[0],
          life_extension_years: ((targetScore - venue.score) * 0.08).toFixed(1),
          note: 'Kalkulasi ulang skor kondisi aset & masa pakai berdasarkan target intervensi.',
        }
      }
    },
  },
})
