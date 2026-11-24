<template>
  <div class="h-[calc(100vh-64px-30px)] flex flex-col md:flex-row relative overflow-hidden">
    <!-- Left Sidebar: Filter Bar & Venue List (Synchronized) -->
    <div
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
      class="w-full md:w-96 bg-surface border-r border-gray-200 z-20 flex flex-col transition-transform duration-300 absolute md:relative inset-y-0 left-0 shadow-lg md:shadow-none"
    >
      <!-- Filter Bar Header -->
      <div class="p-4 border-b border-gray-100 space-y-3 shrink-0 bg-gray-50/50">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-sm text-brand-900 flex items-center gap-1.5">
            <Filter class="w-4 h-4 text-brand-600" />
            <span>Filter Sarana Olahraga</span>
          </h3>
          <button
            @click="sidebarOpen = false"
            class="md:hidden p-1 text-muted hover:text-text-main"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Filter Controls -->
        <div class="space-y-2">
          <!-- District Filter -->
          <div>
            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1">Kecamatan</label>
            <select
              v-model="selectedDistrict"
              class="w-full text-xs rounded-lg border border-gray-200 px-2.5 py-1.5 bg-white text-text-main focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
            >
              <option value="all">Semua Kecamatan (8 Wilayah)</option>
              <option v-for="d in store.districts" :key="d.id" :value="d.name">
                {{ d.name }}
              </option>
            </select>
          </div>

          <!-- Venue Type Filter -->
          <div>
            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1">Tipe Sarana</label>
            <select
              v-model="selectedType"
              class="w-full text-xs rounded-lg border border-gray-200 px-2.5 py-1.5 bg-white text-text-main focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600"
            >
              <option value="all">Semua Tipe Sarana</option>
              <option v-for="t in store.venueTypes" :key="t.id" :value="t.name">
                {{ t.name }}
              </option>
            </select>
          </div>

          <!-- Status Checkboxes -->
          <div>
            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1">Kondisi Fisik</label>
            <div class="flex items-center gap-2 text-xs">
              <label class="inline-flex items-center gap-1 cursor-pointer">
                <input type="checkbox" v-model="statusFilter.good" class="rounded text-status-good focus:ring-emerald-400" />
                <span class="text-status-good font-medium">Baik</span>
              </label>
              <label class="inline-flex items-center gap-1 cursor-pointer">
                <input type="checkbox" v-model="statusFilter.watch" class="rounded text-status-watch focus:ring-amber-400" />
                <span class="text-status-watch font-medium">Waspada</span>
              </label>
              <label class="inline-flex items-center gap-1 cursor-pointer">
                <input type="checkbox" v-model="statusFilter.critical" class="rounded text-status-critical focus:ring-rose-400" />
                <span class="text-status-critical font-medium">Kritis</span>
              </label>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between text-[11px] text-muted pt-1">
          <span>Ditemukan <b>{{ filteredVenues.length }}</b> fasilitas</span>
          <button
            @click="resetFilters"
            class="text-brand-600 hover:underline font-medium"
          >
            Reset Filter
          </button>
        </div>
      </div>

      <!-- Synchronized Venue List -->
      <div class="flex-1 overflow-y-auto divide-y divide-gray-100 p-2 space-y-1">
        <div
          v-for="venue in filteredVenues"
          :key="venue.id"
          @click="selectVenue(venue)"
          class="p-3 rounded-xl cursor-pointer transition-all border"
          :class="activeVenueId === venue.id ? 'bg-brand-50 border-brand-300 shadow-xs' : 'border-transparent hover:bg-gray-50'"
        >
          <div class="flex items-start justify-between gap-2">
            <div>
              <h4 class="text-xs sm:text-sm font-bold text-text-main leading-tight line-clamp-1">
                {{ venue.name }}
              </h4>
              <p class="text-[11px] text-muted mt-0.5">{{ venue.district_name || venue.district }} • {{ venue.type_name || venue.type }}</p>
            </div>
            <StatusBadge :status="venue.status" :score="venue.score ?? venue.condition_score ?? 70" size="sm" />
          </div>

          <div class="mt-2.5 flex items-center justify-between text-[11px] text-muted border-t border-gray-100/80 pt-2">
            <span>Kapasitas: {{ venue.capacity ? venue.capacity.toLocaleString('id-ID') : '-' }}</span>
            <router-link
              :to="'/venues/' + venue.id"
              class="font-semibold text-brand-600 hover:text-brand-900 inline-flex items-center gap-0.5"
              @click.stop
            >
              <span>Detail</span>
              <ArrowRight class="w-3 h-3" />
            </router-link>
          </div>
        </div>

        <div v-if="filteredVenues.length === 0" class="p-8 text-center text-xs text-muted">
          Tidak ada sarana olahraga yang cocok dengan filter.
        </div>
      </div>
    </div>

    <!-- Map View Area -->
    <div class="flex-1 relative">
      <!-- Floating Layer Switcher Controls -->
      <div class="absolute top-4 left-4 z-10 flex flex-col sm:flex-row gap-2">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="md:hidden bg-white/95 backdrop-blur-md px-3 py-2 rounded-xl text-xs font-semibold text-brand-900 shadow-md border border-gray-200 flex items-center gap-1.5"
        >
          <Filter class="w-4 h-4 text-brand-600" />
          <span>Filter & Daftar</span>
        </button>

        <div class="bg-white/95 backdrop-blur-md p-1 rounded-xl shadow-md border border-gray-200 flex flex-wrap items-center gap-1">
          <button
            @click="setLayer('markers')"
            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
            :class="activeLayer === 'markers' ? 'bg-brand-900 text-white shadow-xs' : 'text-text-main hover:bg-gray-100'"
          >
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" :class="activeLayer === 'markers' ? 'bg-white/20 text-white' : 'bg-gray-100 text-brand-900'">Chart 17</span>
            <span>Point Map (Pin Kondisi)</span>
          </button>
          <button
            @click="setLayer('bubble')"
            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
            :class="activeLayer === 'bubble' ? 'bg-brand-900 text-white shadow-xs' : 'text-text-main hover:bg-gray-100'"
          >
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" :class="activeLayer === 'bubble' ? 'bg-white/20 text-white' : 'bg-gray-100 text-brand-900'">Chart 18</span>
            <span>Bubble Map (Kapasitas)</span>
          </button>
          <button
            @click="setLayer('choropleth')"
            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
            :class="activeLayer === 'choropleth' ? 'bg-brand-900 text-white shadow-xs' : 'text-text-main hover:bg-gray-100'"
          >
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" :class="activeLayer === 'choropleth' ? 'bg-white/20 text-white' : 'bg-gray-100 text-brand-900'">Chart 16</span>
            <span>Choropleth (Kecamatan)</span>
          </button>
          <button
            @click="setLayer('heatmap')"
            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
            :class="activeLayer === 'heatmap' ? 'bg-brand-900 text-white shadow-xs' : 'text-text-main hover:bg-gray-100'"
          >
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" :class="activeLayer === 'heatmap' ? 'bg-white/20 text-white' : 'bg-gray-100 text-brand-900'">Chart 20</span>
            <span>Heatmap (Pemanfaatan)</span>
          </button>
        </div>
      </div>

      <!-- Map Legend (Bottom Right) -->
      <div class="absolute bottom-6 right-4 z-10 bg-white/95 backdrop-blur-md p-3.5 rounded-xl shadow-md border border-gray-200 text-xs space-y-2 hidden sm:block max-w-xs">
        <div class="flex items-center justify-between pb-1.5 border-b border-gray-100 gap-2">
          <p class="font-bold text-[11px] uppercase tracking-wider text-muted">Legenda Peta</p>
          <span v-if="activeLayer === 'markers'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-brand-100 text-brand-900 border border-brand-300">Chart 17 • Point Map</span>
          <span v-else-if="activeLayer === 'bubble'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-brand-100 text-brand-900 border border-brand-300">Chart 18 • Bubble Map</span>
          <span v-else-if="activeLayer === 'choropleth'" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-brand-100 text-brand-900 border border-brand-300">Chart 16 • Choropleth Map</span>
          <span v-else class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-brand-100 text-brand-900 border border-brand-300">Chart 20 • Heatmap</span>
        </div>
        <div v-if="activeLayer === 'markers'" class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-status-good border border-white shadow-xs"></span>
            <span>Kondisi Baik (Skor 70-100)</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-status-watch border border-white shadow-xs"></span>
            <span>Perlu Waspada (Skor 45-69)</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-status-critical border border-white shadow-xs"></span>
            <span>Kondisi Kritis (Skor &lt; 45)</span>
          </div>
        </div>
        <div v-else-if="activeLayer === 'bubble'" class="space-y-1">
          <div class="text-[11px] text-muted">Radius lingkaran = Kapasitas Penonton</div>
          <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-full bg-status-good/80 border border-status-good inline-block"></span>
            <span>Fasilitas Baik</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-full bg-status-watch/80 border border-status-watch inline-block"></span>
            <span>Fasilitas Waspada</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-full bg-status-critical/80 border border-status-critical inline-block"></span>
            <span>Fasilitas Kritis</span>
          </div>
        </div>
        <div v-else-if="activeLayer === 'choropleth'" class="space-y-1">
          <div class="text-[11px] text-muted">Kepadatan Sarana per 10.000 Penduduk</div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-xs bg-[#0B3C5D]"></span>
            <span>Tinggi (&gt; 0.8 unit / 10k)</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-xs bg-[#2585C4]"></span>
            <span>Sedang (0.5 - 0.8 unit / 10k)</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-xs bg-[#93C5FD]"></span>
            <span>Rendah (&lt; 0.5 unit / 10k)</span>
          </div>
        </div>
        <div v-else class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            <span>Intensitas Kunjungan Tinggi</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            <span>Intensitas Sedang</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            <span>Intensitas Rendah</span>
          </div>
        </div>
      </div>

      <!-- Leaflet Map Container -->
      <div id="facility-map" class="w-full h-full z-0"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { usePublicDataStore } from '@/stores/publicData'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import { paluDistrictsGeoJSON } from '@/data/paluDistrictsGeoJSON'
import L from 'leaflet'
import 'leaflet.heat'
import { Filter, X, ArrowRight } from 'lucide-vue-next'

const router = useRouter()
const store = usePublicDataStore()

const sidebarOpen = ref(false)
const activeLayer = ref('markers')
const activeVenueId = ref(null)

const selectedDistrict = ref('all')
const selectedType = ref('all')
const statusFilter = reactive({
  good: true,
  watch: true,
  critical: true,
})

let mapInstance = null
let markerGroup = null
let heatGroup = null
let bubbleGroup = null
let choroplethGroup = null
const markerMap = new Map()

const filteredVenues = computed(() => {
  return store.venues.filter((v) => {
    const vDistrict = (v.district_name || v.district || '').trim()
    if (selectedDistrict.value !== 'all' && vDistrict !== selectedDistrict.value) {
      return false
    }
    const vType = (v.type_name || v.type || '').trim()
    if (selectedType.value !== 'all' && vType !== selectedType.value) {
      return false
    }
    const vStatus = (v.status || v.condition_status || 'good').toLowerCase()
    if (!statusFilter[vStatus]) {
      return false
    }
    return true
  })
})

const resetFilters = () => {
  selectedDistrict.value = 'all'
  selectedType.value = 'all'
  statusFilter.good = true
  statusFilter.watch = true
  statusFilter.critical = true
  if (mapInstance) {
    mapInstance.flyTo([-0.8850, 119.8707], 12, { duration: 1 })
  }
}

// Watch filters to automatically update map markers / bubbles
watch(
  () => [filteredVenues.value, activeLayer.value],
  () => {
    if (!mapInstance) return
    if (activeLayer.value === 'markers') {
      renderMarkers()
    } else if (activeLayer.value === 'bubble') {
      renderBubbleMap()
    }
  },
  { deep: true }
)

// When district filter changes, fly to its venues
watch(
  () => selectedDistrict.value,
  (newDist) => {
    if (!mapInstance) return
    if (newDist === 'all') {
      mapInstance.flyTo([-0.8850, 119.8707], 12, { duration: 1 })
    } else {
      const matched = filteredVenues.value.filter((v) => v.latitude && v.longitude)
      if (matched.length > 0) {
        const bounds = L.latLngBounds(matched.map((v) => [v.latitude, v.longitude]))
        mapInstance.fitBounds(bounds.pad(0.3), { duration: 1, maxZoom: 14 })
      }
    }
  }
)

onMounted(async () => {
  await store.fetchVenues()
  await store.fetchHeatmap()

  await nextTick()
  initMap()
})

const initMap = () => {
  const container = document.getElementById('facility-map')
  if (!container || container._leaflet_id) return

  // Center Kota Palu: [-0.8917, 119.8707]
  mapInstance = L.map('facility-map', {
    center: [-0.8850, 119.8707],
    zoom: 12,
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(mapInstance)

  markerGroup = L.featureGroup().addTo(mapInstance)

  renderMarkers()
}

const renderMarkers = () => {
  if (!markerGroup) return
  markerGroup.clearLayers()
  markerMap.clear()

  filteredVenues.value.forEach((v) => {
    if (!v.latitude || !v.longitude) return

    let color = '#2E9E5B'
    if (v.status === 'watch') color = '#E0A100'
    if (v.status === 'critical') color = '#D64545'

    const markerHtml = `
      <div style="background-color: ${color}; width: 18px; height: 18px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.35); cursor: pointer;"></div>
    `

    const icon = L.divIcon({
      html: markerHtml,
      className: 'venue-pin',
      iconSize: [18, 18],
      iconAnchor: [9, 9],
    })

    const marker = L.marker([v.latitude, v.longitude], { icon })

    const popupContent = `
      <div class="p-3 text-xs space-y-2 min-w-[200px]">
        <div>
          <h4 class="font-bold text-sm text-[#0B3C5D]">${v.name}</h4>
          <p class="text-gray-500">${v.district_name || v.district || ''} • ${v.type_name || v.type || ''}</p>
        </div>
        <div class="bg-gray-50 p-2 rounded border border-gray-100 space-y-1">
          <div class="flex justify-between">
            <span class="text-gray-500">Skor Kondisi:</span>
            <span class="font-bold" style="color: ${color}">${v.score ?? v.condition_score ?? 70} / 100</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Inspeksi Terakhir:</span>
            <span>${v.last_inspected || '-'}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Estimasi Servis:</span>
            <span>${v.next_maintenance || 'Segera'}</span>
          </div>
        </div>
        <a
          href="/venues/${v.id}"
          class="block text-center w-full py-1.5 rounded-lg bg-[#0B3C5D] text-white font-semibold text-xs hover:bg-[#1D6FA5] transition"
        >
          Lihat Profil Lengkap →
        </a>
      </div>
    `

    marker.bindPopup(popupContent)

    marker.on('click', () => {
      activeVenueId.value = v.id
    })

    marker.addTo(markerGroup)
    markerMap.set(v.id, marker)
  })
}

const selectVenue = (venue) => {
  activeVenueId.value = venue.id
  if (mapInstance && venue.latitude && venue.longitude) {
    mapInstance.flyTo([venue.latitude, venue.longitude], 15, { duration: 1 })
    const marker = markerMap.get(venue.id)
    if (marker) {
      setTimeout(() => marker.openPopup(), 400)
    }
  }
}

const renderBubbleMap = () => {
  if (!bubbleGroup) bubbleGroup = L.featureGroup()
  bubbleGroup.clearLayers()

  filteredVenues.value.forEach((v) => {
    if (!v.latitude || !v.longitude) return

    let color = '#2E9E5B'
    if (v.status === 'watch') color = '#E0A100'
    if (v.status === 'critical') color = '#D64545'

    const capacity = v.capacity || 500
    const radius = Math.max(7, Math.min(32, Math.sqrt(capacity) * 0.42))

    const circle = L.circleMarker([v.latitude, v.longitude], {
      radius,
      fillColor: color,
      color: '#FFFFFF',
      weight: 2,
      opacity: 1,
      fillOpacity: 0.75,
    })

    const popupContent = `
      <div class="p-3 text-xs space-y-2 min-w-[200px]">
        <div>
          <h4 class="font-bold text-sm text-[#0B3C5D]">${v.name}</h4>
          <p class="text-gray-500">${v.district_name || v.district || ''} • ${v.type_name || v.type || ''}</p>
        </div>
        <div class="bg-gray-50 p-2 rounded border border-gray-100 space-y-1">
          <div class="flex justify-between">
            <span class="text-gray-500">Kapasitas:</span>
            <span class="font-bold text-brand-900">${capacity.toLocaleString('id-ID')} orang</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Skor Kondisi:</span>
            <span class="font-bold" style="color: ${color}">${v.score ?? v.condition_score ?? 70} / 100</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Kunjungan/Thn:</span>
            <span>${(v.annual_visits || 12000).toLocaleString('id-ID')} org</span>
          </div>
        </div>
        <a href="/venues/${v.id}" class="block text-center w-full py-1.5 rounded-lg bg-[#0B3C5D] text-white font-semibold text-xs hover:bg-[#1D6FA5] transition">
          Detail Sarana →
        </a>
      </div>
    `
    circle.bindPopup(popupContent)
    circle.addTo(bubbleGroup)
  })
}

const renderChoropleth = () => {
  if (!choroplethGroup) choroplethGroup = L.featureGroup()
  choroplethGroup.clearLayers()

  const geoJsonLayer = L.geoJSON(paluDistrictsGeoJSON, {
    style: (feature) => {
      const dName = feature.properties.name
      const count = store.venues.filter((v) => v.district_name === dName).length
      const pop = feature.properties.population || 50000
      const density = count / (pop / 10000)

      let fillColor = '#93C5FD' // Low
      if (density >= 0.8) fillColor = '#0B3C5D' // High
      else if (density >= 0.5) fillColor = '#2585C4' // Medium

      return {
        fillColor,
        weight: 2,
        opacity: 1,
        color: '#FFFFFF',
        dashArray: '3',
        fillOpacity: 0.65,
      }
    },
    onEachFeature: (feature, layer) => {
      const dName = feature.properties.name
      const count = store.venues.filter((v) => v.district_name === dName).length
      const pop = feature.properties.population || 50000
      const density = (count / (pop / 10000)).toFixed(2)

      const popupContent = `
        <div class="p-3 text-xs space-y-1.5 min-w-[180px]">
          <h4 class="font-bold text-sm text-[#0B3C5D]">Kecamatan ${dName}</h4>
          <div class="text-gray-600">Populasi: <b>${pop.toLocaleString('id-ID')}</b> jiwa</div>
          <div class="text-gray-600">Total Sarana: <b>${count}</b> unit</div>
          <div class="text-brand-600 font-bold border-t border-gray-100 pt-1 mt-1">
            Kepadatan: ${density} unit / 10k jiwa
          </div>
        </div>
      `
      layer.bindPopup(popupContent)

      layer.on({
        mouseover: (e) => {
          const l = e.target
          l.setStyle({ weight: 3, color: '#F2A900', fillOpacity: 0.85 })
          l.bringToFront()
        },
        mouseout: (e) => {
          geoJsonLayer.resetStyle(e.target)
        },
      })
    },
  })

  geoJsonLayer.addTo(choroplethGroup)
}

const setLayer = (layer) => {
  activeLayer.value = layer
  if (!mapInstance) return

  // Remove all active layers first
  if (markerGroup && mapInstance.hasLayer(markerGroup)) mapInstance.removeLayer(markerGroup)
  if (heatGroup && mapInstance.hasLayer(heatGroup)) mapInstance.removeLayer(heatGroup)
  if (bubbleGroup && mapInstance.hasLayer(bubbleGroup)) mapInstance.removeLayer(bubbleGroup)
  if (choroplethGroup && mapInstance.hasLayer(choroplethGroup)) mapInstance.removeLayer(choroplethGroup)

  if (layer === 'markers') {
    renderMarkers()
    mapInstance.addLayer(markerGroup)
  } else if (layer === 'bubble') {
    renderBubbleMap()
    mapInstance.addLayer(bubbleGroup)
  } else if (layer === 'choropleth') {
    renderChoropleth()
    mapInstance.addLayer(choroplethGroup)
  } else if (layer === 'heatmap') {
    if (!heatGroup) {
      const heatPoints = store.heatmap.map((h) => [h[0], h[1], h[2] || 0.8])
      heatGroup = L.heatLayer(heatPoints, {
        radius: 25,
        blur: 15,
        maxZoom: 16,
        gradient: { 0.4: '#3b82f6', 0.65: '#f59e0b', 1.0: '#ef4444' },
      })
    }
    mapInstance.addLayer(heatGroup)
  }
}
</script>