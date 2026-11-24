# 03 — Workflow Document

**Project:** Satu Data Dispora — Palu City
**Version:** 1.0 · Diagrams use [Mermaid](https://mermaid.js.org/) (render in GitHub, VS Code with the Mermaid extension, or mermaid.live).

---

## 1. Actors

| Actor | Description |
|---|---|
| **Admin** | Dispora data officer; logs in, inputs/validates/publishes data, runs predictions |
| **Public viewer** | Anonymous visitor; read-only access to published aggregates |
| **System** | Laravel backend (validation, aggregation, prediction, caching, audit) |

## 2. Data Lifecycle States

```mermaid
stateDiagram-v2
    [*] --> Draft: Admin creates / imports
    Draft --> Validated: Admin reviews (no errors)
    Validated --> Published: Admin publishes
    Published --> Draft: Admin edits (re-validation required)
    Draft --> [*]: Deleted (soft delete)
```

- Only `Published` records are used by public endpoints and predictions shown publicly.
- Every transition writes an **audit log** entry.

## 3. End-to-End System Workflow

```mermaid
flowchart LR
    A[Data sources: OSM, BPS, news, Dispora/KONI, dummy seeders] --> B[Admin input / CSV import]
    B --> C[Validation + Data quality check]
    C --> D[(MySQL)]
    D --> E[Aggregation + Privacy filter k>=5]
    D --> F[Prediction services]
    E --> G[Public API]
    F --> G
    G --> H[Vue public dashboard]
    D --> I[Admin API]
    I --> J[Vue admin panel]
```

## 4. Workflow 1 — Admin Authentication

```mermaid
sequenceDiagram
    actor Admin
    participant UI as Vue Admin
    participant API as Laravel API
    Admin->>UI: Enter email + password
    UI->>API: POST /api/auth/login
    API->>API: Throttle check (5/min), verify hash
    alt valid
        API-->>UI: Sanctum token + user
        UI->>UI: Store token (memory/session), redirect /admin
    else invalid
        API-->>UI: 422 / 429 error
        UI-->>Admin: Show error message
    end
    Admin->>UI: Logout
    UI->>API: POST /api/auth/logout (revoke token)
```

## 5. Workflow 2 — Manual Data Entry (example: Venue + Inspection)

```mermaid
flowchart TD
    S([Admin opens Venues]) --> N[Click 'Add venue']
    N --> F[Fill form + pick coordinates on mini-map]
    F --> V{Client + server validation}
    V -- errors --> F
    V -- ok --> D[Save as Draft + is_dummy / data_source]
    D --> I[Add inspection score + maintenance records]
    I --> R[Review record]
    R --> VA[Mark Validated]
    VA --> P[Publish]
    P --> L[Audit log written]
    L --> C[Cache cleared for dashboard aggregates]
    C --> E([Venue visible on public map])
```

**Validation rules (examples)**
- Latitude between −0.95 and −0.80 and longitude between 119.80 and 119.95 (approximate Palu bounding box, configurable) — warning if outside.
- Inspection score 0–100; dates not in the future; capacity and area > 0.
- Duplicate check on (name, sub-district).

## 6. Workflow 3 — CSV Import

```mermaid
flowchart TD
    A([Admin selects dataset type]) --> B[Download template CSV]
    B --> C[Upload filled CSV]
    C --> D[Parse + validate every row]
    D --> E{Any errors?}
    E -- yes --> F[Show row-level error report]
    F --> G[Admin fixes file and re-uploads]
    G --> C
    E -- no --> H[Preview first rows + counts]
    H --> I{Confirm import?}
    I -- no --> Z([Cancel])
    I -- yes --> J[Insert as Draft within DB transaction]
    J --> K[Audit log: import summary]
    K --> L([Done: records appear as Draft])
```

## 7. Workflow 4 — Prediction Run

```mermaid
sequenceDiagram
    actor Admin
    participant API as Laravel API
    participant SVC as Prediction Services
    participant DB as MySQL
    Admin->>API: POST /api/admin/predictions/run
    API->>SVC: MedalProjectionService.run()
    SVC->>DB: Read published medal history
    SVC-->>API: Projections + confidence
    API->>SVC: MaintenanceScoreService.run()
    SVC->>DB: Read venues, inspections, incidents
    SVC-->>API: Scores, status, next dates, ranks
    API->>SVC: LevyInsightService.run()
    SVC->>DB: Read usage + levy (12 months)
    SVC-->>API: Quadrants + recommendations
    API->>DB: Store snapshot (run_id, timestamp, results JSON)
    API-->>Admin: Summary + link to dashboard
```

- Public dashboard always reads the **latest snapshot**, so public users never trigger heavy computation.
- If insufficient data: service returns `confidence = low` and a reason; it never fails silently.

## 8. Workflow 5 — Public Dashboard Viewing (with Privacy Filter)

```mermaid
sequenceDiagram
    actor User as Public viewer
    participant UI as Vue Public
    participant API as Laravel Public API
    participant AGG as Aggregation + Privacy Filter
    participant DB as MySQL / Cache
    User->>UI: Open dashboard
    UI->>API: GET /api/public/summary
    API->>DB: Check cache (5 min)
    alt cache miss
        API->>AGG: Build aggregates from Published records
        AGG->>AGG: Suppress cells with count < K (5)
        AGG->>DB: Store in cache
    end
    API-->>UI: JSON aggregates only (no personal data)
    UI-->>User: KPIs, charts, map
```

## 9. Workflow 6 — Policy Simulation

```mermaid
flowchart TD
    A([User opens Simulator]) --> B{Choose scenario}
    B -- Budget --> C[Select sport + move slider ±%]
    B -- Renovation --> D[Select venue + target inspection score]
    C --> E[POST /api/public/simulate/budget]
    D --> F[POST /api/public/simulate/renovation]
    E --> G[Service: baseline + β × Δbudget]
    F --> H[Service: recompute condition score, status, next date]
    G --> I[Return result + assumption flag]
    H --> I
    I --> J[UI shows baseline vs simulated + explanation panel]
    J --> K([No data stored])
```

## 10. Workflow 7 — Data Protection Controls (Cross-Cutting)

```mermaid
flowchart LR
    IN[Input athlete data] --> M[Minimize: no NIK / phone / address]
    M --> P[Pseudonym code + encrypt name]
    P --> S[(Database)]
    S --> A[Aggregate]
    A --> K{Cell count >= 5?}
    K -- yes --> O[Return value]
    K -- no --> X[Return '< 5']
    S --> AD[Admin panel only: decrypted name]
    AD --> AU[Audit log of access/changes]
```

## 11. Workflow 8 — Real vs Dummy Data Handling

1. Admin imports/enters records and sets `data_source` (e.g., "OSM", "Luwuk Times 2024", "Dummy seeder").
2. `is_dummy` is `true` for generated/estimated records.
3. UI shows a **Dummy** badge in admin; public pages show "n% of data is simulated" in the footer.
4. When real data replaces dummy data, admin edits the record: `is_dummy=false`, source updated, audit logged.

## 12. Error & Exception Handling

| Situation | System behaviour |
|---|---|
| Invalid form input | 422 with field-level messages |
| Unauthenticated admin request | 401 → redirect to login |
| Too many login attempts | 429 + retry-after message |
| CSV with errors | No insert; downloadable error report |
| Not enough history for prediction | Fallback method + `confidence=low` + reason text |
| Aggregation cell < 5 | Value replaced by `"<5"` |
| Server error | Generic message to the user; detailed log only in server logs |

## 13. Roles & Permission Matrix

| Action | Public | Admin |
|---|:--:|:--:|
| View published aggregates, map, predictions | ✔ | ✔ |
| Run simulations | ✔ | ✔ |
| View individual athlete records | ✘ | ✔ |
| Create / edit / delete data | ✘ | ✔ |
| Validate / publish data | ✘ | ✔ |
| Import CSV | ✘ | ✔ |
| Trigger prediction recalculation | ✘ | ✔ |
| View audit log | ✘ | ✔ |
