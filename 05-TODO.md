# 05 — To-Do / Implementation Plan

**Project:** Satu Data Dispora — Palu City
**Version:** 1.0 · Legend: `[ ]` todo · `[x]` done · **P0** must · **P1** should · **P2** nice to have · Estimates in hours (h)

> Tip: work phase by phase. Each phase ends with a **Definition of Done (DoD)** so you can show progress to your lecturer.

---

## Phase 0 — Planning & Documentation (due: 18 Nov 2026)

- [x] 01-PRD.md
- [x] 02-DESIGN.md
- [x] 03-WORKFLOW.md
- [x] 04-ARCHITECTURE.md
- [x] 05-TODO.md
- [ ] Re-read all five documents once and adjust names/numbers to match your lecturer's template (1h)
- [ ] Prepare a 5-minute explanation: problem → solution → data → prediction → privacy (1h)

**DoD:** five MD files submitted.

---

## Phase 1 — Project Setup (≈ 3h)

- [x] **P0** Create monorepo `satu-data-dispora/` with `backend/`, `frontend/`, `docs/` (0.5h)
- [x] **P0** Init Laravel 12 in `backend/`; configure `.env` (MySQL `satu_data_dispora`) (0.5h)
- [x] **P0** Install Sanctum; configure CORS for `http://localhost:5173` (0.5h)
- [x] **P0** Init Vue 3 with Vite in `frontend/`; add Vue Router, Pinia, Axios, Tailwind (1h)
- [x] **P0** Add `.env.example` files, `.gitignore`, README with run steps (0.5h)
- [x] **P1** Add GitHub Actions workflow from `04-ARCHITECTURE.md` §11 (0.5h)

**DoD:** `php artisan serve` and `npm run dev` both run; frontend can call a `/api/ping` endpoint.

---

## Phase 2 — Database & Seed Data (≈ 6h)

- [x] **P0** Migrations: districts, venue_types, venues, inspections, maintenance_records, venue_usage, venue_revenue (1.5h)
- [x] **P0** Migrations: sports, athletes, achievements, medal_history, budgets, youth_orgs (1h)
- [x] **P0** Migrations: users, audit_logs, prediction_runs, prediction_results (0.5h)
- [x] **P0** Eloquent models + relations + enums (`state`: draft/validated/published) (1h)
- [x] **P0** `DistrictSeeder` (8 kecamatan of Palu), `SportSeeder`, `VenueTypeSeeder` (0.5h)
- [x] **P0** `RealDataSeeder`: Popda XXII 2024 Palu medals (22 G / 13 S / 10 B), Porprov IX 2022 rank 2, source labels (0.5h)
- [x] **P0** Collect real venue coordinates from OpenStreetMap (Overpass) → CSV → seed; verify names (1.5h)
- [x] **P0** `DummyDataSeeder` with fixed seed: athletes (codes `ATL-xxxx`), usage/levy 24 months, inspections, budgets, medal history, OKP (1.5h)
- [x] **P1** Check data realism (levy ranges, inspection scores) (0.5h)

**DoD:** `php artisan migrate:fresh --seed` yields ~25 venues, ~150 athletes, 24 months usage/levy; all records have `data_source` and `is_dummy`.

---

## Phase 3 — Backend Core (≈ 10h)

### 3.1 Auth & Security
- [x] **P0** `AuthController` (login/logout/me) + throttle (1h)
- [x] **P0** Admin seeder user (credentials in `.env`, not hard-coded) (0.25h)
- [x] **P0** `AuditObserver` for create/update/delete/transition (1h)

### 3.2 Admin CRUD
- [x] **P0** Venues + Inspections + Maintenance + Usage + Revenue controllers, Form Requests, Resources (3h)
- [x] **P0** Athletes + Achievements (encrypted name cast, pseudonym code generator) (1.5h)
- [x] **P0** Medals, Budgets, Youth orgs, Sports (1.5h)
- [x] **P0** State transition endpoint (draft → validated → published) with rules (1h)
- [x] **P1** CSV import service: validate → report → commit (3h)
- [x] **P1** Data-quality endpoint (completeness, dummy %, drafts) (0.75h)

**DoD:** all admin endpoints tested via Postman/Pest; every write creates an audit entry.

---

## Phase 4 — Analytics & Privacy Services (≈ 10h)

- [x] **P0** `LinearRegression` class (OLS, predict, standard error) + unit tests (1.5h)
- [x] **P0** `MedalProjectionService` (fallback to average when n<3, confidence label) + tests (2h)
- [x] **P0** `MaintenanceScoreService` (age, inspection deficit, damage frequency; thresholds; next date; rank) + tests (2.5h)
- [x] **P0** `LevyInsightService` (usage index, revenue/m², cost recovery, quadrants, recommendations, 6-month forecast) + tests (2h)
- [x] **P0** `SimulationService` (budget what-if, renovation what-if, assumption flag) + tests (1.5h)
- [x] **P0** `config/analytics.php` (weights, thresholds, defaults) (0.5h)
- [x] **P0** `AggregationService` + `SmallCellSuppressor` (K=5) + tests (1.5h)
- [x] **P0** Prediction run command/endpoint storing snapshots (1h)

**DoD:** unit tests pass with fixed fixtures; suppression test proves counts < 5 become `"<5"`.

---

## Phase 5 — Public API (≈ 5h)

- [x] **P0** `/public/summary`, `/public/venues`, `/public/venues/{id}`, `/public/heatmap` (2h)
- [x] **P0** `/public/stats/athletes|medals|youth-orgs` (aggregate-only Resources) (1.5h)
- [x] **P0** `/public/predictions/*`, `/public/simulate/*`, `/public/methodology` (1h)
- [x] **P0** Caching (5 min) + cache invalidation on publish (0.5h)
- [x] **P0** Feature test: **no public response contains athlete name/code/birth year** (0.5h)

**DoD:** public endpoints return only published data; privacy feature test passes.

---

## Phase 6 — Frontend Public Dashboard (≈ 16h)

- [x] **P0** Layout, router, design tokens, Tailwind config (1.5h)
- [x] **P0** UI components: KpiCard, StatusBadge, DummyBadge, ConfidenceTag, ChartCard, EmptyState, skeletons (2h)
- [x] **P0** Home / Executive Summary with KPI cards + 4 charts (3h)
- [x] **P0** Facility Map: Leaflet, markers by status, clustering, filters, list sync (3h)
- [x] **P0** Heatmap layer toggle (`leaflet.heat`) + legend (1h)
- [x] **P0** Venue Detail page (score gauge, inspection timeline, usage/levy charts) (2h)
- [x] **P0** Athletes & Youth page (5 aggregate charts, "<5" tooltip) (2h)
- [x] **P0** Predictions pages (medal, maintenance, levy matrix) + ExplainPanel (2.5h)
- [x] **P0** Policy Simulator (budget + renovation) (2h)
- [x] **P0** Methodology + Privacy notice pages (1h)
- [x] **P1** Responsive polish and accessibility pass (contrast, keyboard, chart text summaries) (2h)
- [x] **P2** i18n scaffolding (`vue-i18n`, EN now, ID later) (1h)

**DoD:** ≥ 12 charts rendered; map + heatmap work; every prediction shows its explanation.

---

## Phase 7 — Frontend Admin Panel (≈ 12h)

- [x] **P0** Login page + auth store + route guard + token handling (1.5h)
- [x] **P0** Admin layout (sidebar, topbar) + Admin dashboard (data quality, recent audit) (1.5h)
- [x] **P0** Reusable `DataTable` (search, filter by state, pagination) (1.5h)
- [x] **P0** Venue forms with coordinate picker + inspection/maintenance/usage/levy sub-forms (3h)
- [x] **P0** Athlete, Medal, Budget, Youth-org forms (2h)
- [x] **P0** State transition buttons (validate/publish) with confirmation (0.5h)
- [x] **P1** CSV import wizard (upload → preview → error report → confirm) (2h)
- [x] **P1** Predictions page (Recalculate + snapshot history) and Audit log page (1h)

**DoD:** admin can add a venue + inspection, publish it, and see the status on the public map in < 2 minutes.

---

## Phase 8 — Testing, QA, Hardening (≈ 6h)

- [x] **P0** Run all Pest tests; fix failures (1h)
- [x] **P0** Security checklist: validation on all inputs, no `v-html`, CORS, throttle, `.env` not committed (1h)
- [x] **P0** Privacy audit: inspect every public response manually (1h)
- [x] **P1** Vitest tests for key components (1.5h)
- [x] **P1** Manual test script (login, CRUD, import error, filters, heatmap, simulator) (1h)
- [x] **P1** Performance check (dashboard load, caching works) (0.5h)

**DoD:** zero failing tests; privacy audit passes; checklist signed off.

---

## Phase 9 — Delivery (≈ 3h)

- [ ] **P0** README: setup, default admin, run commands, data-source note (real vs dummy) (1h)
- [ ] **P0** Screenshots: map, heatmap, dashboard, simulator, admin (0.5h)
- [ ] **P1** Short demo script / video (1h)
- [ ] **P1** Final presentation slides (0.5h)

---

## Milestones Summary

| Milestone | Phases | Cumulative effort (approx.) |
|---|---|---|
| M0 — Documentation | 0 | done |
| M1 — Data & Backend ready | 1–5 | ~34h |
| M2 — Public dashboard | 6 | ~50h |
| M3 — Admin panel | 7 | ~62h |
| M4 — QA & delivery | 8–9 | ~71h |

If time is short, cut in this order: P2 items → CSV import → Vitest tests → heatmap polish. **Never cut:** privacy filter, audit log, explanation panels, dummy-data labelling.

---

## Backlog (Post-MVP)

- [ ] PDF export of the executive summary
- [ ] Indonesian UI (`vue-i18n` ID locale)
- [ ] Sub-district choropleth (needs boundary GeoJSON from BPS)
- [ ] Scheduled prediction recalculation (Laravel scheduler)
- [ ] Integration with SIPD / Satu Data Kota Palu
- [ ] Replace dummy data with official Dispora/KONI datasets (send formal data request letter)

---

## Open Questions to Resolve with Lecturer / Dispora

1. Is a formal data request letter to Dispora Kota Palu / KONI Kota Palu allowed for this assignment?
2. Is a hybrid real + dummy dataset acceptable, and what minimum % of real data is expected?
3. Should the final submission include running code, or only these planning documents?
4. Required format for the final report (template, language)?
