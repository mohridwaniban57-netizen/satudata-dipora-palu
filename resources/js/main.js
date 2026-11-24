import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'

// ECharts setup
import ECharts from 'vue-echarts'
import { use } from 'echarts/core'
import { CanvasRenderer } from 'echarts/renderers'
import {
  BarChart,
  LineChart,
  PieChart,
  ScatterChart,
  GaugeChart,
  RadarChart,
  TreemapChart,
  FunnelChart,
  SankeyChart,
  HeatmapChart,
  CustomChart,
} from 'echarts/charts'
import {
  GridComponent,
  TooltipComponent,
  LegendComponent,
  TitleComponent,
  MarkLineComponent,
  MarkAreaComponent,
  GraphicComponent,
  RadarComponent,
  CalendarComponent,
  VisualMapComponent,
  DataZoomComponent,
} from 'echarts/components'

use([
  CanvasRenderer,
  BarChart,
  LineChart,
  PieChart,
  ScatterChart,
  GaugeChart,
  RadarChart,
  TreemapChart,
  FunnelChart,
  SankeyChart,
  HeatmapChart,
  CustomChart,
  GridComponent,
  TooltipComponent,
  LegendComponent,
  TitleComponent,
  MarkLineComponent,
  MarkAreaComponent,
  GraphicComponent,
  RadarComponent,
  CalendarComponent,
  VisualMapComponent,
  DataZoomComponent,
])

const app = createApp(App)

app.component('v-chart', ECharts)
app.use(createPinia())
app.use(router)

app.mount('#app')
