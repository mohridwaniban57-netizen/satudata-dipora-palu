# 02 — Design Document (UX / UI)

**Project:** Satu Data Dispora — Palu City
**Version:** 1.0 · **Frontend:** Vue 3 + Vite + Tailwind CSS · **Map:** Leaflet + OpenStreetMap · **Charts:** Apache ECharts (via `vue-echarts`)

---

## 1. Design Principles

1. **Executive first:** the most important answer ("Which assets need attention? How will we perform?") is visible without scrolling.
2. **Explainable:** every prediction has a "How is this calculated?" link.
3. **Honest data:** dummy data and low-confidence predictions are always visibly labelled.
4. **Privacy by design:** the interface never shows individual athletes publicly.
5. **Simple for non-technical users:** few controls, plain language, sensible defaults.

## 2. Information Architecture

```
PUBLIC (no login)
├── Home / Executive Summary
├── Facility Map (map + heatmap + filters)
│   └── Venue Detail
├── Athletes & Youth (aggregate charts)
├── Predictions
│   ├── Medal Projection
│   ├── Maintenance Priority
│   └── Levy Insight
├── Policy Simulator (what-if)
├── Methodology & Data Sources
└── Privacy & Data Notice

ADMIN (login required)  /admin
├── Dashboard (data quality, recent activity)
├── Venues (+ Inspections, Maintenance, Usage, Levy)
├── Athletes (+ Achievements)
├── Medal History
├── Youth Organizations
├── Sports & Sub-districts
├── Coaching Budgets
├── CSV Import
├── Predictions (recalculate, snapshots)
└── Audit Log
```

## 3. Visual Identity

### 3.1 Colour Tokens
| Token | Hex | Usage |
|---|---|---|
| `--brand-900` | `#0B3C5D` | Header, primary text accents |
| `--brand-600` | `#1D6FA5` | Primary buttons, links |
| `--accent-500` | `#F2A900` | Highlights, key numbers |
| `--bg` | `#F6F8FA` | Page background |
| `--surface` | `#FFFFFF` | Cards |
| `--text` | `#1F2933` | Body text |
| `--muted` | `#6B7785` | Secondary text |
| `--status-good` | `#2E9E5B` | Condition: Good |
| `--status-watch` | `#E0A100` | Condition: Watch |
| `--status-critical` | `#D64545` | Condition: Critical |
| `--dummy` | `#8A63D2` | "Dummy data" badge |

Status is **never colour-only**: always paired with an icon and label (✔ Good, ⚠ Watch, ✖ Critical).

### 3.2 Typography
- Headings: **Inter** (600–700). Body: **Inter** (400–500). Numbers use tabular figures.
- Scale: 12 / 14 / 16 / 20 / 24 / 32 px. Base line height 1.5.

### 3.3 Spacing & Components
- 8-px spacing grid; cards with 12-px radius and subtle shadow; max content width 1280 px.
- Components: `KpiCard`, `StatusBadge`, `DummyBadge`, `ChartCard`, `MapPanel`, `FilterBar`, `DataTable`, `ExplainPanel`, `ConfidenceTag`, `SimulatorForm`, `EmptyState`, `Toast`.

## 4. Page Designs (Wireframes)

### 4.1 Home / Executive Summary
```
┌──────────────────────────────────────────────────────────────┐
│ Logo  SATU DATA DISPORA · KOTA PALU      Map  Athletes  ...  │
├──────────────────────────────────────────────────────────────┤
│ [Venues 25] [Good 60%] [Watch 28%] [Critical 12%] [Medals]   │  ← KpiCards
├───────────────────────────────┬──────────────────────────────┤
│  Mini map (status markers)    │  Top 5 maintenance priority  │
│  [Open full map →]            │  1. Venue A  ✖ 38            │
│                               │  2. Venue B  ⚠ 52            │
├───────────────────────────────┴──────────────────────────────┤
│ Medal trend by event        │ Medal projection next event    │
├──────────────────────────────────────────────────────────────┤
│ Last updated · Contains dummy data (n%) · Methodology        │
└──────────────────────────────────────────────────────────────┘
```

### 4.2 Facility Map
```
┌──────────────┬───────────────────────────────────────────────┐
│ FILTERS      │                                               │
│ Type  [▼]    │            LEAFLET MAP                        │
│ District [▼] │      ● markers (colour+icon by status)        │
│ Status ☑☑☑   │      ░ heatmap layer (utilization)            │
│ Layers:      │                                               │
│ ☑ Markers    │                         ┌──────────────┐      │
│ ☐ Heatmap    │                         │ Venue popup  │      │
│              │                         │ score, next  │      │
│ Venue list   │                         │ maintenance  │      │
└──────────────┴─────────────────────────┴──────────────┴──────┘
```
- Marker clustering when zoomed out; legend always visible; list and map are synchronized (click list → map flies to marker).

### 4.3 Venue Detail
Header (name, type, sub-district, status badge) → profile card → condition score gauge → inspection timeline → usage & levy line charts → "How is this calculated?" panel.

### 4.4 Athletes & Youth (aggregate only)
Charts: athletes by sport (bar), by sub-district (bar), by age group & gender (stacked bar), OKP by sub-district (bar), achievements per year (line). Cells with < 5 individuals render as "< 5" with a tooltip: "Hidden to protect privacy."

### 4.5 Predictions
- **Medal projection:** table per sport (last event, projected points, range, `ConfidenceTag`) + bar chart comparing last vs projected.
- **Maintenance priority:** ranked table (score, status, next date) + stacked status chart.
- **Levy insight:** 2×2 scatter (utilization vs revenue) with quadrant labels and recommendation list.

### 4.6 Policy Simulator
```
┌───────────────────────────────┬──────────────────────────────┐
│ Scenario: (•) Budget ( ) Reno │  RESULT                      │
│ Sport:   [Athletics ▼]        │  Baseline: 24 pts            │
│ Budget change: ──●────  +20%  │  Simulated: 29 pts (+5)      │
│ [Reset]                       │  [bar: baseline vs simulated]│
│ ⓘ Assumption-based estimate   │  ⓘ How is this calculated?   │
└───────────────────────────────┴──────────────────────────────┘
```
- Live update on slider release; assumption-based results carry a visible tag.

### 4.7 Admin Panel
- Left sidebar navigation; top bar with user menu.
- **Dashboard:** data completeness bar, # drafts awaiting validation, recent audit entries.
- **List pages:** searchable `DataTable` with status filter (draft/validated/published) and Dummy badge.
- **Forms:** single-column, inline validation, coordinate picker (click on mini-map to set lat/lng).
- **CSV Import:** upload → preview → validation report (row errors) → confirm.

## 5. Chart Catalogue (MVP target ≥ 12)

| # | Chart | Type | Page |
|---|---|---|---|
| 1 | Medals per event/year (G/S/B) | Stacked bar | Home |
| 2 | Medal share by sport | Donut | Home |
| 3 | Medal projection vs last event | Grouped bar | Predictions |
| 4 | Venue type composition | Donut | Home |
| 5 | Condition status distribution | Stacked bar per sub-district | Map/Home |
| 6 | Utilization by venue (top 10) | Horizontal bar | Map |
| 7 | Monthly levy trend | Line | Levy |
| 8 | Utilization vs revenue matrix | Scatter (quadrants) | Levy |
| 9 | Athletes by sport | Bar | Athletes |
| 10 | Athletes by sub-district | Bar | Athletes |
| 11 | Athletes by age group & gender | Stacked bar | Athletes |
| 12 | OKP/Karang Taruna by sub-district | Bar | Athletes |
| 13 | Venue condition score history | Line | Venue detail |
| 14 | Coaching budget vs points | Scatter + trend line | Simulator |

## 6. Interaction & States

| State | Behaviour |
|---|---|
| Loading | Skeleton cards/charts (no blank flashes) |
| Empty | Friendly message + hint ("No inspection data yet") |
| Low data | `ConfidenceTag` = Low + tooltip explaining why |
| Error | Inline error with retry; toast for admin actions |
| Suppressed cell | "< 5" with privacy tooltip |
| Dummy data | Purple `Dummy` badge on tables, tooltips in charts footer note |

## 7. Responsiveness
- Breakpoints: `sm 640`, `md 768`, `lg 1024`, `xl 1280`.
- Mobile: KPI cards 2-column, filters collapse into a drawer, map full-width with list below, tables scroll horizontally.

## 8. Accessibility Checklist
- Contrast ≥ 4.5:1; focus rings visible; all charts have a text summary or data-table toggle.
- Status uses icon + text + colour. Form labels linked to inputs. Map has a list alternative.
- `prefers-reduced-motion` respected for chart animations.

## 9. Content (Microcopy) Guidelines
- Plain language; avoid jargon (use "Needs attention" next to "Critical" in tooltips).
- Default UI language: **English** (documents) — add i18n keys from the start (`vue-i18n`) so Indonesian can be added later.
- Standard disclaimer in footer: *"Predictions are estimates based on limited historical data and are intended for decision support only."*

## 10. Frontend Structure (Design System Mapping)

```
src/
├── components/ui/        KpiCard, StatusBadge, DummyBadge, ConfidenceTag, DataTable...
├── components/charts/    BarChart, LineChart, DonutChart, ScatterQuadrant...
├── components/map/       MapPanel, VenueMarker, HeatLayer, MapLegend
├── views/public/         Home, FacilityMap, VenueDetail, Athletes, Predictions, Simulator, Methodology, Privacy
├── views/admin/          Login, AdminDashboard, VenueList, VenueForm, ...
├── stores/               auth, filters, dashboard (Pinia)
├── services/             api.js (axios), endpoints per domain
├── composables/          useChartTheme, useSuppression, useDebounce
└── styles/tokens.css     colour/typography tokens above
```
