# 01 — Product Requirements Document (PRD)

**Project:** Predictive Analytics Dashboard "Satu Data Dispora" — Palu City
**Type:** University course assignment (web-based information system)
**Version:** 1.0 (draft for submission) · **Date:** 17 Nov 2026
**Run environment:** Localhost only (public read-only dashboard + admin panel)

---

## 1. Overview

"Satu Data Dispora" is a single web platform that consolidates three data domains of the Palu City Youth and Sports Office (Dispora):

1. **Sports infrastructure** (stadiums, courts, pools, halls) mapped geospatially per sub-district (kecamatan).
2. **Athletes and youth organizations** (OKP / Karang Taruna) track records.
3. **Operational finance** (venue levies/retribusi, maintenance records, coaching budgets).

On top of this data, a lightweight **predictive analytics engine** produces: medal projections, asset maintenance priorities, and venue levy insights. An executive-style **public dashboard** presents the results (heatmap, charts, rankings, what-if simulation). A private **admin panel** is the only place where data is created or edited.

## 2. Problem Statement

| Problem | Impact |
|---|---|
| Sports facility, athlete, and youth data are scattered (spreadsheets, paper, separate offices). | Slow reporting; no single source of truth. |
| Maintenance is reactive (repair after damage). | Facility degradation, higher repair cost, safety risk. |
| Budget allocation for coaching is not clearly linked to past results. | Hard to justify allocation to leadership and the public. |
| Venue levy/usage is not analysed per venue or per sub-district. | Under-used venues and weak revenue go unnoticed. |
| Public has no transparent view of sports assets and achievements. | Low transparency and trust. |

## 3. Goals and Non-Goals

### Goals
- G1. Provide **one integrated, map-based view** of sports facilities across all 8 sub-districts of Palu.
- G2. Provide **explainable predictions** (medals, maintenance, levy) that leadership can justify.
- G3. Provide a **public, transparent dashboard** with strictly aggregated athlete data.
- G4. Give admins a **simple, validated data-entry workflow** with audit trail.
- G5. Apply **personal data protection** by design (UU No. 27/2022 on Personal Data Protection).

### Non-Goals (out of scope)
- No venue booking / reservation / online payment.
- No public user accounts or login (public = read-only, anonymous).
- No multi-role system (admin only). No integration with SIPD / external systems in v1.
- No deep-learning or black-box models.
- No cloud deployment (localhost only for this assignment).

## 4. Users and Personas

| Persona | Access | Needs |
|---|---|---|
| **Admin (Dispora data officer)** | Login, full CRUD, run predictions, publish data | Fast, validated input; import CSV; see data quality warnings |
| **Public viewer** (citizen, journalist, KONI, OKP, lecturer) | No login, read-only | Understand facility locations/conditions, achievement trends, transparency |
| **Executive viewer** (Dispora head — uses the public dashboard) | No login | Executive summary, rankings, what-if simulation to support budget decisions |

## 5. Scope Summary

**In scope (MVP):** facility map + heatmap, athlete/OKP aggregates, medal history, maintenance scoring, levy analysis, 2 simulations, admin CRUD, CSV import, audit log, data-quality flags, real/dummy data labelling.

**Later (post-MVP / optional):** PDF export of the executive summary, bilingual UI (ID/EN), scheduled recalculation, external integrations.

## 6. Functional Requirements

Priority: **P0** = must (MVP), **P1** = should, **P2** = nice to have.

### 6.1 Authentication & Admin Access
| ID | Requirement | Pri |
|---|---|---|
| FR-A1 | Admin logs in with email + password (token-based, Laravel Sanctum). | P0 |
| FR-A2 | Login throttling (max 5 attempts/minute) and secure password hashing. | P0 |
| FR-A3 | Admin can change own password. | P1 |
| FR-A4 | All create/update/delete actions are written to an audit log (who, what, when, old/new value). | P0 |

### 6.2 Master Data Management (Admin)
| ID | Requirement | Pri |
|---|---|---|
| FR-M1 | CRUD **Venues**: name, type, sub-district, address, latitude/longitude, area (m²), capacity, year built, manager, status. | P0 |
| FR-M2 | CRUD **Sports (cabor)** and **Sub-districts** (8 kecamatan seeded). | P0 |
| FR-M3 | CRUD **Athletes** (pseudonymized, see §9): sport, sub-district, birth year, gender, level, active status. | P0 |
| FR-M4 | CRUD **Achievements / medal history**: event, year, sport, gold/silver/bronze. | P0 |
| FR-M5 | CRUD **Youth Organizations** (OKP / Karang Taruna): name, type, sub-district, members count, activity level. | P0 |
| FR-M6 | CRUD **Venue inspections** (date, score 0–100, notes) and **Maintenance records** (date, type, cost, description). | P0 |
| FR-M7 | CRUD **Monthly venue usage** (visit count) and **monthly levy revenue**. | P0 |
| FR-M8 | CRUD **Coaching budgets** per sport per year. | P1 |
| FR-M9 | **CSV import** with validation report (row-level errors) for venues, medals, revenue, usage. | P1 |
| FR-M10 | Every record has `data_source` and `is_dummy` flag; the UI shows a "Dummy" badge. | P0 |
| FR-M11 | Record lifecycle: `draft → validated → published`. Only published data appears publicly. | P0 |

### 6.3 Geospatial Mapping (Public + Admin)
| ID | Requirement | Pri |
|---|---|---|
| FR-G1 | Interactive map (Leaflet + OpenStreetMap) with venue markers coloured by condition status. | P0 |
| FR-G2 | Filters: venue type, sub-district, condition status. | P0 |
| FR-G3 | **Heatmap** of facility utilization (usage index) toggled as a layer. | P0 |
| FR-G4 | Venue detail popup/page: profile, condition score, last inspection, next maintenance date, usage and levy trend. | P0 |
| FR-G5 | Sub-district choropleth (facility density per 10,000 residents). | P2 |

### 6.4 Public Dashboard
| ID | Requirement | Pri |
|---|---|---|
| FR-D1 | **Executive summary** cards: total venues, % in good/watch/critical condition, total athletes (aggregate), total medals (latest event), levy total. | P0 |
| FR-D2 | Charts (min. 8 in MVP): medals by event/year, medals by sport, athletes by sport, athletes by sub-district, athletes by age group/gender, venue type composition, condition status distribution, monthly levy trend, utilization by venue, OKP by sub-district. | P0 |
| FR-D3 | Rankings: Top 10 venues by utilization; Top 10 maintenance priority. | P0 |
| FR-D4 | Data freshness indicator ("Last updated") and data-source/disclaimer page. | P0 |
| FR-D5 | Public pages show **aggregates only** (see §9). | P0 |

### 6.5 Predictive Analytics
| ID | Requirement | Pri |
|---|---|---|
| FR-P1 | **Medal projection** per sport for the next event, with a confidence label and a range. | P0 |
| FR-P2 | **Predictive maintenance**: condition score, status, estimated next maintenance date, priority rank. | P0 |
| FR-P3 | **Levy optimization insight**: classify venues in a utilization × revenue matrix with a rule-based recommendation. | P0 |
| FR-P4 | **Policy simulation (what-if)**: (a) change coaching budget of a sport by ±%, (b) renovate a venue. Results shown live on the dashboard. | P0 |
| FR-P5 | "How is this calculated?" explanation panel next to each prediction (explainability). | P0 |
| FR-P6 | Admin can trigger "Recalculate predictions"; each run is stored as a snapshot with timestamp. | P1 |

### 6.6 Transparency & Quality
| ID | Requirement | Pri |
|---|---|---|
| FR-T1 | Methodology page describing models, assumptions, and limitations. | P0 |
| FR-T2 | Data quality indicators (completeness %, number of dummy records). | P1 |

## 7. Predictive Analytics Specification

Principle: **simple, explainable, honest about uncertainty.** All models run inside Laravel (PHP service classes).

### 7.1 Medal Projection
- **Input:** medal history per sport per event year (gold/silver/bronze). Weighted points: `points = 3·G + 2·S + 1·B`.
- **Method:** ordinary least squares (OLS) linear trend `points = a + b·year`.
  - If `n < 3` events → fall back to **average of available events** (label: "Low confidence").
  - If `n ≥ 3` → linear trend; prediction range = ± 1 standard error of the estimate.
- **Output:** projected points and projected gold count (proportional share), confidence label (Low `n<3`, Medium `n=3–4`, High `n≥5`).
- **Note:** projections are clamped at ≥ 0.

### 7.2 Predictive Maintenance (Rule-Based Condition Score)
```
age_factor        = min(age_years / design_life_years, 1) × 100
inspection_deficit = 100 − latest_inspection_score
damage_frequency  = min(incidents_last_24_months / 5, 1) × 100

risk_score        = 0.30·age_factor + 0.50·inspection_deficit + 0.20·damage_frequency
condition_score   = 100 − risk_score
```
| Condition score | Status |
|---|---|
| ≥ 75 | Good |
| 50 – 74 | Watch |
| < 50 | Critical |

- **Next maintenance date** = `last_maintenance_date + base_interval(type) × (condition_score / 100)`. Lower score → shorter interval.
- **Priority rank** = sort by `risk_score` desc, tie-break by usage (higher usage first).
- Weights and thresholds are configurable (`config/analytics.php`).

### 7.3 Levy (Retribusi) Optimization Insight
Metrics per venue (last 12 months): `revenue`, `usage_index = visits / (capacity × operating_days)`, `revenue_per_m2`, `cost_recovery = revenue / maintenance_cost`.

| Usage | Revenue | Classification | Recommendation (rule-based text) |
|---|---|---|---|
| High | High | Star | Maintain quality; schedule maintenance outside peak hours |
| High | Low | Under-priced / leakage | Review tariff and collection process |
| Low | High | Niche / event-driven | Keep tariff; promote regular use |
| Low | Low | Under-utilized | Promote usage, review programming or function |

High/Low = above/below the median across venues. Also provides a 6-month linear revenue trend forecast per venue.

### 7.4 Policy Simulation (What-If)
1. **Coaching budget:** projected points = `baseline_projection + β_budget × Δbudget`.
   - `β_budget` is estimated by simple regression of points on budget (per sport) when ≥ 4 data points exist; otherwise a **configurable default assumption** is used and the UI clearly labels it "assumption-based".
2. **Venue renovation:** the user selects a venue and a post-renovation inspection score → the condition score, status, and maintenance date are recalculated; usage uplift uses a configurable assumption (default +10% per status level improved).

Simulations never modify stored data; they are computed on request.

## 8. Data Requirements

### 8.1 Data Sources and Status

| Dataset | Source | Status |
|---|---|---|
| Venue coordinates (stadium, courts, pools, sports centres in Palu) | OpenStreetMap (Overpass query: `leisure=pitch/stadium/sports_centre/swimming_pool`) | **Real — to be collected.** Verify names/coordinates in the field or via Dispora. |
| Sub-district list (8 kecamatan) and population | BPS Kota Palu | **Real — to be collected** |
| Popda XXII Sulteng 2024: Palu was overall champion with 22 gold, 13 silver, 10 bronze (203 medals contested, 8 sports) | luwuktimes.id | **Real — found** |
| Porprov IX Sulteng 2022: Palu finished 2nd overall (behind Banggai) | luwuktimes.id | **Real — found** (medal counts per sport not found) |
| Porprov X Sulteng 2026 (Morowali, Nov 2026, 27 sports) | rri.co.id | **Real — context only** (results not yet available) |
| Per-sport medal history, athlete lists, budgets, inspections, usage, levy | Dispora / KONI Kota Palu (official request letter needed) | **Not publicly found → dummy data** |

> **Important finding:** no public, structured Dispora Palu venue/athlete/levy dataset was found online. The project therefore uses a **hybrid dataset**: real data where verifiable, clearly labelled **dummy data** elsewhere (`is_dummy = true`). The methodology page must state this explicitly.

### 8.2 Dummy Data Rules
- Generated with seeders using a fixed random seed (reproducible).
- Realistic ranges (e.g., levy per venue type, inspection scores 40–95).
- Never use real names for dummy athletes (generic codes only, e.g., `ATL-0001`).
- Dummy volume target: ~25 venues, ~150 athletes, 8–10 sports, 3–5 events of history, 24 months of usage/levy.

## 9. Data Protection Requirements (UU PDP No. 27/2022)

| ID | Requirement |
|---|---|
| DP-1 | **Data minimization:** do not store national ID (NIK), phone, home address, or photos of athletes. |
| DP-2 | **Pseudonymization:** athletes identified by code; full name stored **encrypted at rest** (Laravel `encrypted` cast) and visible only in the admin panel. |
| DP-3 | **Public aggregates only:** the public API never returns individual athlete records. |
| DP-4 | **Small-cell suppression:** any public aggregate with fewer than **5** individuals (configurable `K_ANONYMITY_MIN`) is shown as "< 5". |
| DP-5 | **Minors:** athletes under 18 are flagged; only birth year (not full date) is stored; consent status field recorded by admin. |
| DP-6 | **Access control and audit:** admin-only writes, rate limiting, audit log of all changes, HTTPS-ready config. |
| DP-7 | **Publication gate:** data is public only when `status = published`. |
| DP-8 | **Transparency:** a Privacy & Data Notice page explains what is collected and why. |

## 10. Non-Functional Requirements

| Area | Requirement |
|---|---|
| Performance | Dashboard first load < 3 s on localhost with the target dataset; aggregates cached (5 min). |
| Security | OWASP Top 10 basics: validation, CSRF-safe token API, SQL injection safe (Eloquent), XSS-safe rendering, rate limiting. |
| Usability | Responsive (desktop first, usable on mobile); clear empty and low-data states. |
| Accessibility | Colour-blind-safe status colours + icons/labels; keyboard navigable; contrast ≥ 4.5:1. |
| Reliability | Migrations + seeders reproduce the full dataset in one command. |
| Maintainability | Service-layer architecture, feature tests for prediction logic, CI pipeline. |
| Portability | Runs with `php artisan serve` + `npm run dev` + local MySQL. |

## 11. Success Metrics

| Metric | Target |
|---|---|
| All P0 requirements implemented | 100% |
| Public pages expose any individual athlete data | 0 cases |
| Prediction logic unit-test coverage | ≥ 80% of service classes |
| Admin can add a venue + inspection and see updated status on the map | < 2 minutes |
| Charts delivered on the dashboard | ≥ 8 (target 12) |
| Every prediction has an explanation panel | 100% |

## 12. Assumptions, Risks, Dependencies

| # | Item | Mitigation |
|---|---|---|
| R1 | Real Dispora data unavailable | Hybrid dataset; label dummy data; provide CSV import so real data can be dropped in later |
| R2 | Very few historical data points make forecasts weak | Confidence labels, fallback to averages, explicit limitations text |
| R3 | OSM data for Palu may be incomplete or outdated | Manual verification; admin can edit coordinates |
| R4 | One-day deadline for documents | Prioritize P0; keep P1/P2 as backlog in `05-TODO.md` |
| R5 | Personal data misuse risk | Section 9 controls, aggregate-only public API |
| A1 | Admin is a single trusted role | No RBAC in v1 |
| A2 | Localhost only | No production hardening beyond config readiness |

## 13. Glossary

- **Dispora:** Dinas Pemuda dan Olahraga (Youth and Sports Office).
- **OKP:** Organisasi Kepemudaan (youth organization); **Karang Taruna:** community youth organization.
- **Porprov / Popda / PON:** provincial / student regional / national sports weeks.
- **Cabor:** cabang olahraga (sport discipline).
- **Retribusi:** local government levy/fee.
- **Heatmap:** density visualization of utilization on the map.
