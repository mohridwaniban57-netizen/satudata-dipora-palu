# 📋 LAPORAN KOMPREHENSIF ARSITEKTUR & DOKUMENTASI SISTEM
# "SATU DATA DISPORA" KOTA PALU
**Dashboard Analitik Prediktif untuk Pemetaan Prestasi Olahraga, Kepemudaan, dan Optimalisasi Aset**

---

| Parameter | Informasi Proyek |
|:---|:---|
| **Nama Sistem** | Portal Satu Data Dispora Kota Palu (Sistem Informasi Analitik & Prediktif) |
| **Instansi Pengguna** | Dinas Pemuda dan Olahraga (Dispora) Pemerintah Kota Palu, Sulawesi Tengah |
| **Dasar Hukum & Tata Kelola** | Perpres No. 39 Tahun 2019 (*Satu Data Indonesia*), UU No. 27 Tahun 2022 (*Pelindungan Data Pribadi / UU PDP*), UU No. 11 Tahun 2022 (*Keolahragaan*) |
| **Arsitektur Aplikasi** | Decoupled Hybrid: Laravel 11 RESTful API Backend + Vue 3 Single Page Application (SPA) Frontend |
| **Engine Visualisasi** | Apache ECharts (22 Charts) + Leaflet Geoportal OpenStreetMap (3 Map Layers) |
| **Engine Statistik & Analitik**| Ordinary Least Squares (OLS) Regression Engine, Matrix Scoring Pemeliharaan Aset, 4-Quadrant Levy Matrix |
| **Status Produksi** | Siap Rilis (Production Ready - Build Vite 100% Valid) |

---

## DAFTAR ISI

1. [Ringkasan Eksekutif & Tujuan Sistem](#1-ringkasan-eksekutif--tujuan-sistem)
2. [Arsitektur Teknologi (Full-Stack Architecture)](#2-arsitektur-teknologi-full-stack-architecture)
3. [Daftar Lengkap 25 Visualisasi Data (Katalog Chart 1 – 25)](#3-daftar-lengkap-25-visualisasi-data-katalog-chart-1--25)
4. [Struktur Direktori & Pemetaan Komponen Program](#4-struktur-direktori--pemetaan-komponen-program)
5. [Struktur Basis Data & Kamus Data (Database Schema)](#5-struktur-basis-data--kamus-data-database-schema)
6. [Algoritma Prediktif & Formula Matematis Analitik](#6-algoritma-prediktif--formula-matematis-analitik)
7. [Kepatuhan Pelindungan Data Pribadi (UU PDP) & Arsitektur Data Hybrid](#7-kepatuhan-pelindungan-data-pribadi-uu-pdp--arsitektur-data-hybrid)
8. [Spesifikasi Antarmuka Pengguna (Public Portal & Admin Panel)](#8-spesifikasi-antarmuka-pengguna-public-portal--admin-panel)
9. [Tata Cara Pengoperasian & Panduan Rilis (Deployment Runbook)](#9-tata-cara-pengoperasian--panduan-rilis-deployment-runbook)
10. [Kesimpulan & Rekomendasi Pengembangan Lanjutan](#10-kesimpulan--rekomendasi-pengembangan-lanjutan)

---

## 1. RINGKASAN EKSEKUTIF & TUJUAN SISTEM

### 1.1 Latar Belakang
Dinas Pemuda dan Olahraga (Dispora) Kota Palu mengelola berbagai urusan strategis daerah yang mencakup pembinaan atlet pelajar/muda, pembinaan organisasi kepemudaan, serta pengelolaan dan pemeliharaan aset sarana prasarana olahraga di 8 kecamatan Kota Palu. 

Sebelum sistem ini dibangun, data keolahragaan dan pemuda tersebar di berbagai dokumen fisik dan berkas terpisah, mengakibatkan:
1. Kurangnya visibilitas kondisi fisik aktual sarana prasarana olahraga.
2. Tidak adanya proyeksi berbasis data matematis untuk estimasi perolehan medali pada ajang pekan olahraga (Popda/Porprov).
3. Sulitnya mengidentifikasi potensi kebocoran retribusi daerah (PAD) dari penyewaan fasilitas olahraga.
4. Resiko pelanggaran privasi data atlet anak/remaja.

### 1.2 Tujuan Pembangunan Sistem
Sistem **Satu Data Dispora Kota Palu** dirancang sebagai *Decision Support System* (DSS) eksekutif dan portal informasi publik yang bertujuan untuk:
- **Optimalisasi Aset:** Memberikan pemantauan kesehatan fisik fasilitas olahraga secara berkala dan memeringkat prioritas renovasi berdasarkan matriks risiko ilmiah.
- **Prediksi Prestasi Olahraga:** Menggunakan metode ekonometrika regresi linier *Ordinary Least Squares* (OLS) untuk memproyeksikan target perolehan medali cabor pada Popda 2026.
- **Transparansi Pendapatan Asli Daerah (PAD):** Mengklasifikasikan 25 sarana ke dalam 4 kuadran performa (Bintang, Potensi Bocor, Niche, Kurang Utilisasi).
- **Kepatuhan Privasi (UU PDP):** Mengimplementasikan algoritma *$k$-anonymity suppression* ($k \ge 5$) dan enkripsi data sensitif atlet anak/remaja.

---

## 2. ARSITEKTUR TEKNOLOGI (FULL-STACK ARCHITECTURE)

Sistem menggunakan arsitektur modern berbasis pemisahan *backend-frontend* (Decoupled SPA via Laravel Vite Bundle):

```
+---------------------------------------------------------------------------------+
|                                 USER BROWSER                                    |
|      (Desktop / Tablet / Mobile - Responsive Inter Typography & Tailwind UI)    |
+----------------------------------------+----------------------------------------+
                                         |
                                         | HTTP / JSON API (RESTful)
                                         v
+---------------------------------------------------------------------------------+
|                       VUE 3 FRONTEND LAYER (resources/js)                       |
|  - Vue Router 4 (Guarded Public & Admin Routes, Navigation Guards)             |
|  - Pinia Stores (auth.js, publicData.js, adminData.js)                          |
|  - Apache ECharts (22 Advanced Interactive Analytics Charts)                   |
|  - Leaflet Geoportal (Pin Map, Bubble Map, Polygon GeoJSON Choropleth, Heatmap) |
|  - Tailwind CSS 3 (Deep Navy #0B3C5D, Vibrant Amber #F2A900, Semantic Status)   |
+----------------------------------------+----------------------------------------+
                                         |
                                         | API Endpoints (/api/public/* & /api/admin/*)
                                         v
+---------------------------------------------------------------------------------+
|                       LARAVEL 11 BACKEND API LAYER (app/)                       |
|  - Routing & Middleware (Throttle 60/120 rpm, Sanctum Bearer Token Auth)       |
|  - Controllers (Public Summary, Map, Stats, Admin CRUD, CSV Importer, Audit)   |
|  - Analytics Services (LinearRegression, MaintenanceScore, LevyInsight)        |
|  - Privacy Services (SmallCellSuppressor, AES-256 PII Cryptography)             |
|  - Eloquent ORM (15 Relational Models & Lifecycle State Observers)              |
+----------------------------------------+----------------------------------------+
                                         |
                                         | PDO / SQL Transactions
                                         v
+---------------------------------------------------------------------------------+
|                      MYSQL 8 / MARIADB DATABASE STORAGE                         |
|  - 17 Relational Tables: Districts, Venues, Inspections, Maintenance, Usages,  |
|    Revenues, Sports, Athletes, Achievements, Medals, Budgets, Youth Orgs, Logs  |
+---------------------------------------------------------------------------------+
```

### 2.1 Spesifikasi Perangkat Lunak Backend
- **Framework:** Laravel 11.x (PHP 8.2+)
- **Autentikasi:** Laravel Sanctum (Stateful session cookie & API bearer token)
- **Keamanan Enkripsi:** OpenSSL `AES-256-CBC` dengan salt unik per record
- **Manajemen Alur Kerja Data:** Status mutasi data (*draft* $\rightarrow$ *validated* $\rightarrow$ *published*)
- **Audit Logging:** Observer otomatis yang mencatat *actor_id*, IP address, model, payload lama (*before*), dan payload baru (*after*)

### 2.2 Spesifikasi Perangkat Lunak Frontend
- **Framework:** Vue 3.5+ (Composition API dengan sintaks `<script setup>`)
- **Build Tool:** Vite 6.x dengan Hot Module Replacement (HMR) super cepat
- **State Store:** Pinia Store dengan arsitektur reaktif terpusat
- **Grafik & Visualisasi:** `echarts` 5.x dan wrapper `vue-echarts`
- **Peta Interaktif:** `leaflet` 1.9+ dengan integrasi OpenStreetMap & Poligon GeoJSON resmi 8 kecamatan Kota Palu
- **Ikonografi:** `lucide-vue-next` (Desain bersih berstandar enterprise)

---

## 3. DAFTAR LENGKAP 25 VISUALISASI DATA (KATALOG CHART 1 – 25)

Sesuai spesifikasi resmi sistem analitik, seluruh **25 chart** telah terimplementasi penuh dengan penomoran badge, anchor ID presisi, dan perhitungan matematis:

```
+-----------------------------------------------------------------------------------------------------+
| NO | TIPE VISUALISASI           | NAMA ANALISIS                       | LOKASI VIEW & ANCHOR       |
+----+----------------------------+-------------------------------------+----------------------------+
| 01 | Bar Chart (Horizontal)     | Distribusi Atlet per Cabor          | /athletes#chart-1          |
| 02 | Grouped Bar Chart          | Komparasi Poin Riil vs OLS          | /predictions#chart-2       |
| 03 | Column Chart (Vertical)    | Realisasi Retribusi Bulanan Venue   | /venues/:id#chart-3        |
| 04 | Stacked Column Chart       | Distribusi Kondisi Sarana per Wil   | /#chart-4                  |
| 05 | Lollipop Chart             | Top 10 Kapasitas Sarana Olahraga    | /predictions#chart-5       |
| 06 | Radar Chart                | Multi-Dimensi Daya Saing Cabor      | /predictions#chart-6       |
| 07 | Histogram                  | Sebaran Frekuensi Skor Fisik Aset   | /predictions#chart-7       |
| 08 | Scatter Plot (Quadrants)   | Matriks 4 Kuadran Utilisasi vs PAD  | /predictions#chart-8       |
| 09 | Sankey Diagram             | Alur Kas Daerah -> Cabor -> Medali  | /athletes#chart-9          |
| 10 | Area Chart (Cumulative)    | Laju Akumulatif PAD vs Target       | /predictions#chart-10      |
| 11 | Donut Chart                | Komposisi Ragam Jenis Fasilitas     | /#chart-11                 |
| 12 | Treemap Chart              | Peta Partisi Anggaran Dispora       | /athletes#chart-12         |
| 13 | 100% Stacked Bar Chart     | Proporsi Jenjang Atlet per Cabor    | /athletes#chart-13         |
| 14 | Funnel Chart               | Corong Konversi Regenerasi Atlet    | /athletes#chart-14         |
| 15 | Calendar Heatmap           | Kalender Harian Inspeksi Fisik      | /venues/:id#chart-15       |
| 16 | Choropleth Map             | Peta Rasio Sarana per 10k Jiwa      | /map (Layer Choropleth)    |
| 17 | Point Map (Marker)         | Peta Sebaran Koordinat Fasilitas    | /#chart-17 & /map (Pin)    |
| 18 | Bubble Map                 | Peta Proporsi Kapasitas Penonton    | /map (Layer Bubble)        |
| 19 | Stacked Bar Chart (Horiz)  | Piramida Demografi Usia & Gender    | /athletes#chart-19         |
| 20 | Heatmap                    | Peta Densitas Konsentrasi Publik    | /map (Layer Heatmap)       |
| 21 | Bullet Chart               | Benchmark Target PAD Retribusi      | /#chart-21                 |
| 22 | Waterfall Chart            | Dekomposisi Depresiasi Skor Aset    | /venues/:id#chart-22       |
| 23 | Line Chart                 | Tren Garis Medali Popda & Porprov   | /#chart-23                 |
| 24 | Bubble Chart (3D Matrix)   | 3D: Kunjungan, Retribusi, Kapasitas | /predictions#chart-24      |
| 25 | Error Bar Chart            | Interval Keyakinan OLS (Margin Galat| /predictions#chart-25      |
+-----------------------------------------------------------------------------------------------------+
```

### Rincian Analisis Setiap Visualisasi:

#### **Chart 1: Bar Chart (Horizontal) — Distribusi Atlet per Cabang Olahraga**
- **Komponen:** `AthletesBySportChart.vue` di `/athletes#chart-1`
- **Fungsi:** Menyajikan ranking kuantitas atlet binaan resmi Dispora/PPLPD di 10 cabang olahraga unggulan secara horizontal untuk kemudahan keterbacaan label cabor.
- **Sumber Data:** Agregasi tabel `athletes` dikelompokkan berdasarkan `sport_id`.

#### **Chart 2: Grouped Bar Chart — Komparasi Poin Riil vs Proyeksi Model OLS**
- **Komponen:** `MedalProjectionChart.vue` di `/predictions#chart-2`
- **Fungsi:** Membandingkan secara berdampingan (*side-by-side*) skor poin riil kejuaraan terakhir (Popda 2024) dengan hasil komputasi estimasi regresi linier untuk ajang berikutnya.
- **Sumber Data:** Tabel `medal_history` dan hasil engine `MedalProjectionService`.

#### **Chart 3: Column Chart (Vertical) — Tren Kunjungan & Retribusi Bulanan Venue**
- **Komponen:** `VenueMonthlyChart.vue` di `/venues/:id#chart-3`
- **Fungsi:** Menampilkan volume penerimaan retribusi sewa per bulan secara vertikal dikombinasikan dengan kurva tren kunjungan masyarakat.
- **Sumber Data:** Tabel `venue_revenue` dan `venue_usage` dalam rentang waktu 24 bulan.

#### **Chart 4: Stacked Column Chart — Distribusi Kondisi Fasilitas per Kecamatan**
- **Komponen:** `DistrictConditionChart.vue` di `/#chart-4`
- **Fungsi:** Menampilkan komposisi sarana pada 8 kecamatan yang terbagi menjadi status Baik (hijau), Waspada (kuning), dan Kritis (merah).
- **Sumber Data:** Tabel `venues` di-join dengan tabel `districts` dan field `condition_score`.

#### **Chart 5: Lollipop Chart — Top 10 Kapasitas Sarana Olahraga Terbesar**
- **Komponen:** `VenueCapacityLollipopChart.vue` di `/predictions#chart-5`
- **Fungsi:** Visualisasi *stem-and-bubble* yang memeringkat 10 sarana olahraga dengan daya tampung penonton terbesar di Kota Palu tanpa beban visual bar tebal.
- **Sumber Data:** Tabel `venues` diurutkan berdasarkan `capacity DESC LIMIT 10`.

#### **Chart 6: Radar Chart — Evaluasi Multi-Dimensi Daya Saing Cabor Unggulan**
- **Komponen:** `SportPerformanceRadarChart.vue` di `/predictions#chart-6`
- **Fungsi:** Memetakan 6 dimensi strategis cabang olahraga unggulan: (1) Rasio Medali Emas, (2) Kuantitas Atlet, (3) Efisiensi Anggaran, (4) Keterlibatan Atlet Putri, (5) Konsistensi Prestasi, dan (6) Ketersediaan Sarana Latihan.
- **Sumber Data:** Normalisasi nilai min-max dari tabel `medals`, `athletes`, `budgets`, dan `venues`.

#### **Chart 7: Histogram — Distribusi Frekuensi Skor Kelayakan Fisik Fasilitas**
- **Komponen:** `VenueScoreHistogramChart.vue` di `/predictions#chart-7`
- **Fungsi:** Mengelompokkan 25 sarana ke dalam bin skor interval 10 poin (<40, 40-49, 50-59, 60-69, 70-79, 80-89, 90-100) untuk mendeteksi kecenderungan penuaan aset infrastruktur Dispora.
- **Sumber Data:** Evaluasi `score` seluruh data fasilitas olahraga.

#### **Chart 8: Scatter Plot (4 Kuadran) — Matriks Utilisasi vs Realisasi Retribusi**
- **Komponen:** `LevyQuadrantChart.vue` di `/predictions#chart-8`
- **Fungsi:** Memetakan sarana pada sumbu X (tingkat utilisasi kunjungan) dan sumbu Y (retribusi PAD). Titik median membagi ruang menjadi 4 kuadran: Bintang (*Star*), Berpotensi Bocor (*Leakage Risk*), Niche Spesifik, dan Kurang Pemanfaatan (*Under-utilized*).
- **Sumber Data:** Output dari `LevyInsightService.php`.

#### **Chart 9: Sankey Diagram — Alur Pembiayaan Anggaran ke Capaian Medali**
- **Komponen:** `BudgetAthleteFlowSankeyChart.vue` di `/athletes#chart-9`
- **Fungsi:** Visualisasi aliran relasional multi-tingkat: Kas Daerah $\rightarrow$ Cabor Unggulan $\rightarrow$ Jenjang Pembinaan (Klub, Daerah, Provinsi, Nasional) $\rightarrow$ Capaian Medali (Emas, Perak, Perunggu).
- **Sumber Data:** Tabel `budgets`, `sports`, `athletes`, dan `achievements`.

#### **Chart 10: Area Chart (Cumulative) — Laju Akumulatif PAD vs Target Penerimaan**
- **Komponen:** `LevyCumulativeAreaChart.vue` di `/predictions#chart-10`
- **Fungsi:** Menggambarkan laju pertumbuhan akumulatif perolehan retribusi dari bulan ke bulan sepanjang tahun anggaran disertai arsiran gradien di bawah kurva target realisasi.
- **Sumber Data:** Akumulasi kumulatif `amount` pada `venue_revenue`.

#### **Chart 11: Donut Chart — Komposisi Ragam Jenis Sarana Olahraga**
- **Komponen:** `VenueTypeDonutChart.vue` di `/#chart-11`
- **Fungsi:** Menampilkan persentase proporsi pembagian tipe sarana (Stadion, GOR Indoor, Lapangan Terbuka, Kolam Renang, dsb.) di Kota Palu dengan lubang donat elegan di tengah.
- **Sumber Data:** Tabel `venues` dikelompokkan berdasarkan `venue_type_id`.

#### **Chart 12: Treemap Chart — Peta Alokasi Anggaran Pembinaan Dispora**
- **Komponen:** `BudgetTreemapChart.vue` di `/athletes#chart-12`
- **Fungsi:** Menyajikan luas area persegi proporsional terhadap besaran porsi dana hibah dan pembinaan cabor serta ormawa pemuda dalam APBD Dispora.
- **Sumber Data:** Tabel `budgets` dikelompokkan berdasarkan tahun anggaran terbaru.

#### **Chart 13: 100% Stacked Bar Chart — Komposisi Jenjang Pembinaan Atlet per Cabor**
- **Komponen:** `AthleteLevelStacked100Chart.vue` di `/athletes#chart-13`
- **Fungsi:** Menormalkan total atlet setiap cabor menjadi skala 100%, memperlihatkan proporsi jenjang atlet dari tingkat Klub Pemula, Tingkat Daerah, Tingkat Provinsi, hingga Atlet Pelatnas/Nasional.
- **Sumber Data:** Tabel `athletes` field `level` per `sport_id`.

#### **Chart 14: Funnel Chart — Pipeline Regenerasi & Konversi Atlet Binaan**
- **Komponen:** `AthleteRegenerationFunnelChart.vue` di `/athletes#chart-14`
- **Fungsi:** Memvisualisasikan corong piramida seleksi talenta atlet: Total Atlet Terdata (150) $\rightarrow$ Tingkat Daerah $\rightarrow$ Kontingen Provinsi $\rightarrow$ Kontingen Nasional $\rightarrow$ Peraih Medali Kejuaraan Resmi.
- **Sumber Data:** Tabel `athletes` dan `achievements`.

#### **Chart 15: Calendar Heatmap — Kalender Aktivitas Inspeksi & Pemeliharaan**
- **Komponen:** `InspectionCalendarHeatmap.vue` di `/venues/:id#chart-15`
- **Fungsi:** Matriks grid kalender 365 hari (Januari–Desember) yang menunjukkan intensitas tanggal pelaksanaan audit kelaikan fisik serta servis pemeliharaan berkala sarana.
- **Sumber Data:** Field `inspected_on` dari tabel `inspections` dan `done_on` dari `maintenance_records`.

#### **Chart 16: Choropleth Map — Peta Tematik Poligon Kepadatan Sarana per 10k Jiwa**
- **Komponen:** Leaflet Choropleth Layer di `/map`
- **Fungsi:** Peta batas poligon resmi 8 kecamatan di Kota Palu dengan gradasi warna biru tua (kepadatan tinggi), biru sedang, hingga biru muda (kepadatan rendah) berdasarkan rasio jumlah sarana per 10.000 penduduk.
- **Sumber Data:** Koordinat GeoJSON kecamatan dan agregasi `venues` per `district_id`.

#### **Chart 17: Point Map — Peta Sebaran Titik Koordinat Sarana Olahraga**
- **Komponen:** Leaflet Point Layer di `/#chart-17` dan `/map`
- **Fungsi:** Menempatkan pin penanda GPS presisi untuk 25 sarana olahraga di Kota Palu dengan kode warna status kelaikan fisik (Hijau = Baik, Kuning = Waspada, Merah = Kritis).
- **Sumber Data:** Field `latitude` dan `longitude` tabel `venues`.

#### **Chart 18: Bubble Map — Peta Proporsi Kapasitas Penonton Sarana Olahraga**
- **Komponen:** Leaflet Bubble Layer di `/map`
- **Fungsi:** Menampilkan lingkaran transparan di atas peta di mana radius lingkaran secara proporsional merepresentasikan daya tampung penonton venue (misal Stadion Gawalise berkapasitas terbesar).
- **Sumber Data:** Field `capacity` dari tabel `venues`.

#### **Chart 19: Stacked Bar Chart (Horizontal) — Demografi Kelompok Usia & Gender (Piramida Atlet)**
- **Komponen:** `AthletesPyramidChart.vue` di `/athletes#chart-19`
- **Fungsi:** Memvisualisasikan piramida penduduk atlet dengan bar horizontal ke kiri untuk atlet putra dan ke kanan untuk atlet putri pada kelompok usia <12 thn, 13-15 thn, 16-18 thn, dan >18 thn.
- **Sumber Data:** Kalkulasi tahun lahir atlet terhadap tahun berjalan dipisah berdasarkan gender.

#### **Chart 20: Heatmap — Peta Densitas Konsentrasi Utilisasi Publik**
- **Komponen:** Leaflet Heatmap Layer di `/map`
- **Fungsi:** Peta termal dengan gradien warna biru-kuning-merah yang mengidentifikasi titik-titik pusat konsentrasi pemanfaatan sarana olahraga oleh masyarakat Kota Palu.
- **Sumber Data:** Akumulasi kunjungan pada tabel `venue_usage`.

#### **Chart 21: Bullet Chart — Benchmark Target vs Realisasi PAD Retribusi**
- **Komponen:** `RevenueBulletChart.vue` di `/#chart-21`
- **Fungsi:** Menilai kinerja efisiensi penerimaan retribusi daerah terhadap ambang target minimum, moderat, dan prima untuk setiap kelompok sarana olahraga.
- **Sumber Data:** Realisasi `venue_revenue` dibandingkan target kalkulasi anggaran PAD.

#### **Chart 22: Waterfall Chart — Dekomposisi Penurunan Skor Fisik Fasilitas (Depresiasi)**
- **Komponen:** `ConditionScoreWaterfallChart.vue` di `/venues/:id#chart-22`
- **Fungsi:** Membedah anatomi penurunan mutu aset dari kondisi awal ideal (100) $\rightarrow$ dikurangi faktor penalti usia bangunan $\rightarrow$ dikurangi defisit inspeksi $\rightarrow$ dikurangi insiden kerusakan $\rightarrow$ menghasilkan skor kondisi fisik akhir.
- **Sumber Data:** Model dekomposisi `MaintenanceScoreService.php`.

#### **Chart 23: Line Chart — Tren Garis Perolehan Medali Kontingen Kota Palu**
- **Komponen:** `MedalTrendChart.vue` di `/#chart-23`
- **Fungsi:** Menampilkan grafik garis time-series rekam jejak konsistensi perolehan medali Emas, Perak, dan Perunggu Kota Palu pada berbagai kejuaraan olahraga daerah dan provinsi.
- **Sumber Data:** Riwayat tabel `medal_history`.

#### **Chart 24: Bubble Chart (3D Matrix) — Analisis Multidimensi: Kunjungan, Retribusi & Kapasitas**
- **Komponen:** `Venue3DBubbleChart.vue` di `/predictions#chart-24`
- **Fungsi:** Korelasi 3 dimensi: Sumbu X (Kunjungan ribuan orang), Sumbu Y (Retribusi juta rupiah), dan Ukuran Gelembung (Kapasitas penonton fasilitas).
- **Sumber Data:** Komparasi data terpadu `venues`, `venue_usage`, dan `venue_revenue`.

#### **Chart 25: Error Bar Chart — Interval Keyakinan OLS (Margin of Error Medali)**
- **Komponen:** `MedalErrorBarChart.vue` di `/predictions#chart-25`
- **Fungsi:** Menyajikan estimasi perolehan poin medali Popda 2026 dilengkapi garis galat toleransi ketidakpastian matematis ($\pm 1 \text{ Standard Error}$) dari model regresi linier.
- **Sumber Data:** Parameter $SE$ (Standard Error) yang dihitung oleh `LinearRegression.php`.

---

## 4. STRUKTUR DIREKTORI & PEMETAAN KOMPONEN PROGRAM

Berikut merupakan struktur arsitektur berkas program dalam repositori proyek:

```
satudata-dipora-palu/
├── app/
│   ├── Console/Commands/
│   │   └── RunPredictionsCommand.php       # CLI command untuk menjalankan komputasi analitik
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                      # 14 Controller CRUD & Manajemen Data Internal
│   │   │   │   ├── AthleteController.php
│   │   │   │   ├── AuditController.php
│   │   │   │   ├── BudgetController.php
│   │   │   │   ├── DataQualityController.php
│   │   │   │   ├── DistrictController.php
│   │   │   │   ├── ImportController.php
│   │   │   │   ├── InspectionController.php
│   │   │   │   ├── MaintenanceController.php
│   │   │   │   ├── MedalController.php
│   │   │   │   ├── PredictionController.php
│   │   │   │   ├── RevenueController.php
│   │   │   │   ├── SportController.php
│   │   │   │   ├── TransitionController.php # Workflow approval (draft->published)
│   │   │   │   ├── UsageController.php
│   │   │   │   ├── VenueController.php
│   │   │   │   └── YouthOrgController.php
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php       # Otentikasi Sanctum (Login/Logout/Me)
│   │   │   └── Public/                     # 8 Controller API Publik Terbuka
│   │   │       ├── AthleteStatsController.php
│   │   │       ├── MedalStatsController.php
│   │   │       ├── MethodologyController.php
│   │   │       ├── PredictionController.php
│   │   │       ├── SimulationController.php # Endpoint Sandbox What-If
│   │   │       ├── SummaryController.php
│   │   │       ├── VenueMapController.php
│   │   │       └── YouthOrgStatsController.php
│   │   └── Resources/                      # API Resource Transformer
│   │       ├── VenueDetailResource.php
│   │       └── VenuePublicResource.php
│   ├── Models/                             # 15 Eloquent Models Relasional
│   │   ├── Achievement.php
│   │   ├── Athlete.php                     # Mengandung mutator enkripsi AES-256
│   │   ├── AuditLog.php
│   │   ├── Budget.php
│   │   ├── District.php
│   │   ├── Inspection.php
│   │   ├── MaintenanceRecord.php
│   │   ├── MedalHistory.php
│   │   ├── PredictionResult.php
│   │   ├── PredictionRun.php
│   │   ├── Sport.php
│   │   ├── User.php
│   │   ├── Venue.php
│   │   ├── VenueRevenue.php
│   │   ├── VenueType.php
│   │   ├── VenueUsage.php
│   │   └── YouthOrg.php
│   ├── Observers/
│   │   └── AuditObserver.php               # Pencatat mutasi data otomatis
│   └── Services/
│       ├── Analytics/                      # Mesin Komputasi Analisis & Statistik
│       │   ├── LevyInsightService.php      # Matriks 4 Kuadran Retribusi
│       │   ├── LinearRegression.php        # Algoritma Ordinary Least Squares
│       │   ├── MaintenanceScoreService.php # Penilai Skor Fisik & Jadwal Servis
│       │   ├── MedalProjectionService.php  # Proyeksi Medali & Poin Cabor
│       │   └── SimulationService.php       # Logika What-If Simulator
│       └── Privacy/
│           ├── AggregationService.php
│           └── SmallCellSuppressor.php     # Filter K-Anonymity (k >= 5)
├── database/
│   ├── migrations/                         # 17 Berkas Skema Database DDL
│   └── seeders/                            # Data Seeder (RealData & Synthetic Dummy)
├── public/
│   ├── images/
│   │   ├── logo/logo-palu.png              # Lambang Daerah Resmi Pemkot Palu
│   │   ├── venues/                         # Dokumentasi foto sarana olahraga asli
│   │   ├── athletes/                       # Dokumentasi foto atlet daerah asli
│   │   └── hero/                           # Foto panorama Teluk Palu & fasilitas
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── charts/                     # 22 Komponen Apache ECharts Mandiri
│   │   │   │   ├── AthleteLevelStacked100Chart.vue
│   │   │   │   ├── AthleteRegenerationFunnelChart.vue
│   │   │   │   ├── AthletesBySportChart.vue
│   │   │   │   ├── AthletesPyramidChart.vue
│   │   │   │   ├── BudgetAthleteFlowSankeyChart.vue
│   │   │   │   ├── BudgetTreemapChart.vue
│   │   │   │   ├── ConditionGaugeChart.vue
│   │   │   │   ├── ConditionScoreWaterfallChart.vue
│   │   │   │   ├── DistrictConditionChart.vue
│   │   │   │   ├── InspectionCalendarHeatmap.vue
│   │   │   │   ├── LevyCumulativeAreaChart.vue
│   │   │   │   ├── LevyQuadrantChart.vue
│   │   │   │   ├── MedalErrorBarChart.vue
│   │   │   │   ├── MedalProjectionChart.vue
│   │   │   │   ├── MedalTrendChart.vue
│   │   │   │   ├── RevenueBulletChart.vue
│   │   │   │   ├── SportPerformanceRadarChart.vue
│   │   │   │   ├── Venue3DBubbleChart.vue
│   │   │   │   ├── VenueCapacityLollipopChart.vue
│   │   │   │   ├── VenueMonthlyChart.vue
│   │   │   │   ├── VenueScoreHistogramChart.vue
│   │   │   │   └── VenueTypeDonutChart.vue
│   │   │   └── ui/                         # Komponen Desain Reusable
│   │   │       ├── ChartCatalogModal.vue   # Modal Dialog Interaktif Indeks 25 Chart
│   │   │       ├── ConfidenceTag.vue       # Label Derajat Keyakinan Model
│   │   │       ├── DataTable.vue
│   │   │       ├── DummyBadge.vue          # Label Kejujuran Data Simulasi
│   │   │       ├── EmptyState.vue
│   │   │       ├── ExplainPanel.vue        # Panel Penjelas Rumus Matematis
│   │   │       ├── KpiCard.vue
│   │   │       └── StatusBadge.vue
│   │   ├── layouts/
│   │   │   ├── AdminLayout.vue             # Kerangka Dashboard Administrator
│   │   │   └── PublicLayout.vue            # Kerangka Portal Publik Eksekutif
│   │   ├── router/index.js                 # Konfigurasi Vue Router 4 & Auth Guard
│   │   ├── stores/                         # Pinia Store State Management
│   │   │   ├── auth.js
│   │   │   └── publicData.js
│   │   └── views/
│   │       ├── admin/                      # 9 View Kelola Data Administrator
│   │       └── public/                     # 8 View Portal Analitik Publik
│   └── views/
│       └── app.blade.php                   # Single Entry Blade File
└── routes/
    ├── api.php                             # Daftar Rute Endpoint REST API
    └── web.php                             # Rute Fallback Single Page Application
```

---

## 5. STRUKTUR BASIS DATA & KAMUS DATA (DATABASE SCHEMA)

Sistem menggunakan basis data relasional ternormalisasi dengan **17 tabel utama**:

```
                                  +-------------------+
                                  |     districts     |
                                  +---------+---------+
                                            |
         +----------------------------------+----------------------------------+
         |                                  |                                  | 1:N
+--------v---------+              +---------v---------+              +---------v---------+
|      venues      |              |     athletes      |              |    youth_orgs     |
+--------+---------+              +---------+---------+              +-------------------+
         |                                  |
    +----+----+                             +----+----+
    |         |                             |         |
+---v----+ +--v-------+                +----v----+ +--v-----------+
| inspect| | maint_rec|                | achieve | | medal_history|
+--------+ +----------+                +---------+ +--------------+
    |         |
+---v----+ +--v-------+
| usage  | | revenue  |
+--------+ +----------+
```

### Kamus Data Entitas Pokok:

#### 1. Tabel `districts` (Kecamatan)
Menyimpan wilayah administrasi 8 kecamatan di Kota Palu beserta estimasi populasinya:
- `id` (PK, BigInt)
- `name` (Varchar 100: *Palu Barat, Palu Timur, Palu Selatan, Palu Utara, Tatanga, Ulujadi, Mantikulore, Tawaeli*)
- `population` (Int: data dasar rasio sarana per 10k jiwa)

#### 2. Tabel `venue_types` (Kategori Sarana)
- `id` (PK, BigInt)
- `name` (Varchar: *Stadion, GOR/Gedung Olahraga, Lapangan Olahraga, Kolam Renang, Lapangan Tembak*)
- `design_life_years` (Int: umur teknis konstruksi, misal stadion 30 tahun)
- `base_interval_days` (Int: interval standar inspeksi fisik)

#### 3. Tabel `venues` (Fasilitas & Sarana Olahraga)
- `id` (PK, BigInt)
- `venue_type_id` (FK `venue_types`)
- `district_id` (FK `districts`)
- `name` (Varchar: nama sarana olahraga, misal *GOR Siranindi, Stadion Gawalise*)
- `latitude` & `longitude` (Decimal: koordinat GPS presisi peta)
- `area_m2` (Decimal: luas lantai/lapangan)
- `capacity` (Int: daya tampung penonton)
- `year_built` (Int: tahun konstruksi)
- `state` (Enum: `draft`, `validated`, `published`)
- `is_dummy` (Boolean: penanda data riil vs simulasi)
- `data_source` (Varchar: asal data)

#### 4. Tabel `inspections` (Audit Fisik Lapangan)
- `id` (PK, BigInt)
- `venue_id` (FK `venues`)
- `inspected_on` (Date)
- `inspector_name` (Varchar)
- `score` (Decimal 0 - 100: skor penilaian fisik fasilitas)
- `notes` (Text: uraian temuan teknis kerusakan/kebersihan)

#### 5. Tabel `maintenance_records` (Pemeliharaan & Servis)
- `id` (PK, BigInt)
- `venue_id` (FK `venues`)
- `type` (Varchar: tipe perbaikan, misal renovasi atap, pengecatan lapangan)
- `cost` (Decimal: biaya perbaikan dalam Rupiah)
- `done_on` (Date)
- `is_incident` (Boolean: penanda apakah perbaikan karena kerusakan mendadak/insiden)

#### 6. Tabel `venue_usage` & `venue_revenue` (Waktu Runtun 24 Bulan)
- Menyimpan agregasi bulanan kunjungan masyarakat (`visits`) dan perolehan retribusi resmi kas daerah (`amount`) selama rentang waktu 2 tahun (24 bulan).

#### 7. Tabel `sports` & `athletes` (Keolahragaan & Atlet)
- `sports`: 10 cabang olahraga unggulan binaan Dispora Kota Palu.
- `athletes`: Menyimpan kode samaran (`athlete_code`, misal `ATL-0012`), nama lengkap terenkripsi AES-256, tanggal lahir, gender, dan jenjang prestasi (`club`, `district`, `province`, `national`).

#### 8. Tabel `medal_history` & `achievements`
- `medal_history`: Data perolehan medali Emas, Perak, Perunggu kontingen Kota Palu pada Popda XXII Sulteng 2024 dan Porprov.
- `achievements`: Catatan prestasi individu atlet berprestasi.

#### 9. Tabel `budgets` & `youth_orgs`
- `budgets`: Alokasi belanja daerah APBD Dispora untuk pembinaan cabor dan kepemudaan.
- `youth_orgs`: Sebaran 20 Organisasi Kemasyarakatan Pemuda (OKP) dan Karang Taruna di 8 kecamatan.

#### 10. Tabel `audit_logs` (Rekam Jejak Tata Kelola)
- `id`, `user_id`, `action` (`CREATE`, `UPDATE`, `DELETE`, `TRANSITION`), `auditable_type`, `auditable_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, `created_at`.

---

## 6. ALGORITMA PREDIKTIF & FORMULA MATEMATIS ANALITIK

Sistem menerapkan prinsip *white-box analytics* di mana seluruh visualisasi prediktif dapat dipertanggungjawabkan secara ilmiah:

### 6.1 Model Ekonometrika Proyeksi Medali (OLS Regression)
Model memprediksi perolehan medali pada ajang berikutnya menggunakan regresi linier sederhana terhadap deret historis:

$$\hat{Y}_i = \alpha + \beta X_i + \epsilon_i$$

Di mana:
- $Y$ = Total skor bobot medali kontingen (Emas = 3 poin, Perak = 2 poin, Perunggu = 1 poin).
- $X$ = Urutan tahun kejuaraan ($t$).
- $\beta = \frac{SS_{xy}}{SS_{xx}} = \frac{\sum (X_i - \bar{X})(Y_i - \bar{Y})}{\sum (X_i - \bar{X})^2}$
- $\alpha = \bar{Y} - \beta \bar{X}$

Interval ketidakpastian galat toleransi (*Standard Error*) dihitung dengan:

$$SE = \sqrt{\frac{\sum (Y_i - \hat{Y}_i)^2}{n - 2}}$$

Rentang estimasi perolehan poin dinyatakan dalam batas:

$$\text{Poin Proyeksi} \in [\hat{Y} - SE, \; \hat{Y} + SE]$$

Tingkat keyakinan model (*Confidence Level*):
- **Tinggi (*High*):** Jika sampel data historis $n \ge 5$ periode.
- **Sedang (*Medium*):** Jika $3 \le n < 5$ periode.
- **Rendah (*Low*):** Jika $n < 3$ periode (menggunakan nilai rata-rata historis).

---

### 6.2 Formula Penilaian Kondisi Fisik & Risiko Kerusakan Sarana
Kondisi fisik gedung dan lapangan dihitung dengan formula pembobotan multi-faktor:

$$\text{Skor Risiko} = (0.30 \times \text{Faktor Usia}) + (0.50 \times \text{Defisit Inspeksi}) + (0.20 \times \text{Frekuensi Kerusakan})$$

$$\text{Skor Kondisi Fisik} = 100 - \text{Skor Risiko}$$

Dengan definisi parameter:
1. **Faktor Usia ($0.30$):**
   $$\text{Faktor Usia} = \min\left(100, \; \frac{\text{Tahun Berjalan} - \text{Tahun Konstruksi}}{\text{Umur Rencana Desain}} \times 100\right)$$
2. **Defisit Inspeksi ($0.50$):**
   $$\text{Defisit Inspeksi} = 100 - \text{Skor Audit Lapangan Terakhir}$$
3. **Frekuensi Kerusakan ($0.20$):**
   $$\text{Frekuensi Kerusakan} = \min\left(100, \; \frac{\text{Jumlah Insiden Kerusakan 24 Bulan Terakhir}}{5} \times 100\right)$$

**Klasifikasi Status Fasilitas:**
- 🟢 **Baik (*Good*):** Skor $\ge 75$ (Pemeliharaan preventif reguler).
- 🟡 **Waspada (*Watch*):** Skor $50 - 74.9$ (Perlu perbaikan bagian struktural minor).
- 🔴 **Kritis (*Critical*):** Skor $< 50$ (Prioritas intervensi anggaran renovasi fisik segera).

**Rekomendasi Jadwal Pemeliharaan Berikutnya:**

$$T_{\text{servis}} = T_{\text{terakhir}} + \left(\text{Interval Dasar} \times \frac{\text{Skor Kondisi}}{100}\right) \text{ hari}$$

---

### 6.3 Matriks 4 Kuadran Optimalisasi Retribusi (Levy Quadrant Matrix)
Setiap sarana diposisikan pada diagram kartesius berdasarkan:
- **Sumbu X:** Indeks Utilisasi Kunjungan $=\frac{\text{Total Kunjungan Tahunan}}{\text{Kapasitas} \times 365}$
- **Sumbu Y:** Realisasi Pendapatan Retribusi Kas Daerah Tahunan (Rp)

Titik potong ditentukan secara dinamis menggunakan **Nilai Median** seluruh fasilitas:

```
               RETRIBUSI DAERAH (Y)
                        ^
      KUADRAN III       |       KUADRAN I
     NICHE / KHUSUS     |     BINTANG (STAR)
   (Kunjungan Rendah,   |   (Kunjungan Tinggi,
    Retribusi Tinggi)   |   Retribusi Tinggi)
                        |
------------------------+------------------------> UTILISASI (X)
      KUADRAN IV        |       KUADRAN II
    KURANG UTILISASI    |     POTENSI BOCOR
   (Kunjungan Rendah,   |   (Kunjungan Tinggi,
    Retribusi Rendah)   |   Retribusi Rendah)
                        |
```

- **Kuadran I (Bintang):** Contoh GOR Siranindi, Stadion Gawalise. Aset unggulan, pertahankan standar pelayanan dan digitalisasi e-ticketing.
- **Kuadran II (Potensi Bocor):** Utilisasi tinggi namun setoran retribusi rendah. Rekomendasi audit karcis dan pencegahan kebocoran kasir.
- **Kuadran III (Niche):** Contoh Sirkuit Panggona atau Lapangan Tembak. Pemanfaatan terbatas event tertentu namun bernilai retribusi tinggi.
- **Kuadran IV (Kurang Utilisasi):** Fasilitas kurang diminati. Rekomendasi program aktivasi olahraga masyarakat bersama Karang Taruna.

---

### 6.4 Algoritma Simulator Kebijakan (*What-If Sandbox*)
Memfasilitasi pimpinan daerah melakukan eksperimen skenario sebelum mengambil keputusan:

1. **Skenario 1: Penyesuaian Anggaran Cabor terhadap Medali**
   $$\Delta \text{Poin Medali} = \beta_{\text{anggaran}} \times \Delta \text{Anggaran (Rp)}$$
   $$\text{Estimasi Medali Emas Baru} = \text{Poin Baru} \times \text{Rasio Historis Emas}$$
2. **Skenario 2: Simulasi Renovasi Fasilitas Olahraga**
   Menetapkan target skor inspeksi baru $\rightarrow$ mereset frekuensi insiden menjadi 0 $\rightarrow$ menghitung perpanjangan interval masa pakai $\rightarrow$ menghasilkan proyeksi kenaikan utilisasi masyarakat ($+10\%$ per kenaikan tingkat status).

---

## 7. KEPATUHAN PELINDUNGAN DATA PRIBADI (UU PDP) & ARSITEKTUR DATA HYBRID

### 7.1 Kepatuhan UU No. 27 Tahun 2022 (UU PDP)
Data identitas atlet binaan di bawah umur diklasifikasikan sebagai **Data Pribadi yang Bersifat Spesifik/Sensitif**. Sistem menerapkan prinsip *Privacy by Design*:
1. **Enkripsi At Rest (Database Level):**  
   Nama asli atlet tidak disimpan dalam bentuk teks polos (*plain-text*), melainkan dienkripsi dengan algoritma standar militer `AES-256-CBC`.
2. **Kode Pseudonim:**  
   Pada antarmuka sistem, atlet hanya diidentifikasi dengan kode registrasi terstandarisasi, misalnya `ATL-001` hingga `ATL-150`.
3. **Filter Sensor Sel Kecil (*Small-Cell K-Anonymity Suppression*):**  
   Untuk seluruh agregasi publik (misalnya jumlah atlet per kecamatan atau per cabang olahraga), setiap kelompok yang memiliki data kurang dari ambang batas ($n < 5$) otomatis disamarkan (*masked*) menjadi teks string:
   $$\text{Jika } 0 < n < 5 \implies \text{Tampilan: } "< 5"$$
   Hal ini mencegah pihak luar merekonstruksi identitas anak melalui teknik *cross-referencing* data demografis.

### 7.2 Transparansi Data Hybrid (Prinsip Kejujuran Analitik)
Sistem membedakan secara tegas antara data riil terverifikasi dengan data simulasi:
- 🟢 **Label "Data Riil":** Diterapkan pada data dokumen resmi Pemkot Palu (Hasil Popda Sulteng 2024, data venue utama Stadion Gawalise/GOR Siranindi, poligon 8 kecamatan).
- 🟣 **Label "Data Simulasi" (*Synthetic Dummy*):** Ditandai dengan badge ungu khusus pada data uji matematis untuk memvalidasi algoritma time-series tanpa memanipulasi kenyataan publik.

---

## 8. SPESIFIKASI ANTARMUKA PENGGUNA (PUBLIC PORTAL & ADMIN PANEL)

### 8.1 Antarmuka Publik (Executive Public Portal)
Dapat diakses terbuka oleh pimpinan daerah, atlet, pemerhati olahraga, dan masyarakat umum tanpa login:
1. **Beranda Eksekutif (`/`):** Ringkasan kartu KPI utama, Mini Peta Sebaran Sarana, Prioritas Pemeliharaan Top 5, Sorotan Cabor Unggulan, Chart 23 (Garis Medali), Chart 11 (Donut Fasilitas), Chart 4 (Stacked Kondisi Fasilitas), dan Chart 21 (Bullet PAD).
2. **Peta Interaktif Fasilitas (`/map`):** Peta Geoportal Leaflet layar penuh dengan filter real-time (Kecamatan, Tipe, Status Fisik), serta 4 layer visualisasi yang dapat diganti: Pin Kondisi (Chart 17), Bubble Map Kapasitas (Chart 18), Choropleth Kepadatan (Chart 16), dan Heatmap Pemanfaatan (Chart 20).
3. **Statistik Atlet & Kepemudaan (`/athletes`):** Menampilkan Chart 1 (Bar Cabor), Chart 19 (Piramida Usia), Chart 13 (100% Stacked Bar), Chart 14 (Funnel Regenerasi), Chart 12 (Treemap Anggaran), Chart 9 (Sankey Alur Prestasi), serta sebaran organisasi pemuda di 8 kecamatan.
4. **Prediksi & Wawasan Kebijakan (`/predictions`):** 3 Tab analitik:
   - *Tab 1 (Prestasi):* Chart 2 (Grouped Bar), Chart 25 (Error Bar), Chart 6 (Radar Daya Saing).
   - *Tab 2 (Aset):* Chart 7 (Histogram Skor), Chart 5 (Lollipop Kapasitas), Tabel Rangking Urgensi Perbaikan.
   - *Tab 3 (Retribusi):* Chart 8 (Scatter 4 Kuadran), Chart 24 (Bubble 3D), Chart 10 (Area Kumulatif PAD).
5. **Detail Profil Fasilitas (`/venues/:id`):** Informasi teknis spesifikasi sarana, Speedometer Skor Kondisi, Chart 22 (Waterfall Depresiasi), Chart 15 (Calendar Heatmap Inspeksi), Riwayat Audit Fisik & Servis, serta Chart 3 (Kolom Realisasi Retribusi Bulanan).
6. **Simulator Skenario Kebijakan (`/simulator`):** Fitur interaktif untuk menyimulasikan dampak penyesuaian dana pembinaan terhadap perolehan medali cabor, atau dampak renovasi terhadap umur layanan fasilitas.
7. **Modal Interaktif "Katalog 25 Chart":** Tombol floating `[25 Katalog 25 Chart]` di pojok kanan bawah seluruh halaman publik yang membuka jendela modal daftar lengkap Chart 1–25 dengan pencarian instan dan tombol lompat langsung (*direct jump*).

### 8.2 Antarmuka Administrator (`/admin`)
Dilindungi oleh otentikasi Sanctum dan pengamanan sesi:
1. **Login Admin (`/admin/login`):** Tampilan bersih dan aman tanpa ekspos kredensial bawaan.
2. **Dashboard Administrasi (`/admin`):** Monitoring status verifikasi data (*Draft, Validated, Published*), log aktivitas terakhir, dan audit kualitas data.
3. **Manajemen Master Data (CRUD):** Modul kelola Fasilitas, Atlet, Medali Kejuaraan, Organisasi Pemuda, dan Anggaran.
4. **Alur Kerja Validasi (*Lifecycle Transition*):** Fitur verifikasi berjenjang oleh staf operasional sebelum data dipublikasikan ke portal publik.
5. **Impor CSV Terpadu (`/admin/import`):** Fasilitas unggah berkas CSV massal yang dilengkapi validasi format dan verifikasi sebelum di-commit ke basis data.
6. **Audit Log Perubahan Data (`/admin/audit-logs`):** Rekam jejak seluruh aktivitas penambahan, pengubahan, dan penghapusan data untuk akuntabilitas internal.

---

## 9. TATA CARA PENGOPERASIAN & PANDUAN RILIS (DEPLOYMENT RUNBOOK)

### 9.1 Kebutuhan Lingkungan Server
- **Web Server:** Apache 2.4+ atau Nginx 1.20+
- **PHP:** Versi 8.2 atau lebih tinggi (Ekstensi: `pdo_mysql`, `openssl`, `mbstring`, `curl`, `bcmath`)
- **Basis Data:** MariaDB 10.4+ atau MySQL 8.0+
- **Node.js:** Versi 18.x atau 20.x (NPM 9+)

### 9.2 Langkah Instalasi & Konfigurasi Baru
```bash
# 1. Masuk ke direktori web root
cd d:/xampp8/htdocs/satudata-dipora-palu

# 2. Salin environment configuration file
cp .env.example .env

# 3. Generate Encryption Key aplikasi
php artisan key:generate

# 4. Konfigurasi database pada berkas .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=satu_data_dispora
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan Migrasi Tabel dan Seeding Data Lengkap
php artisan migrate:fresh --seed

# 6. Kompilasi Aset Frontend Vue 3 untuk Produksi
npm install
npm run build
```

### 9.3 Akun Pengelola Sistem Awal (Default Superadmin)
- **Halaman Login:** `/admin/login`
- **Email:** `admin@dispora-palu.go.id`
- **Password:** Sesuai konfigurasi aman di berkas `.env` (atau `Admin@12345` pada seeder awal)

---

## 10. KESIMPULAN & REKOMENDASI PENGEMBANGAN LANJUTAN

### 10.1 Kesimpulan
Proyek sistem dashboard analitik prediktif **Satu Data Dispora Kota Palu** telah berhasil dibangun dan diselesaikan secara menyeluruh dengan pencapaian:
1. **25 Chart Analitik Aktif:** Seluruh visualisasi data dari Chart 1 hingga Chart 25 berfungsi 100% dengan integrasi backend dan badge penomoran yang rapi.
2. **Kepatuhan Privasi Penuh:** Mengamankan data pribadi anak sesuai UU No. 27 Tahun 2022 dengan algoritma *$k$-anonymity*.
3. **Transparansi Kejujuran Data:** Membedakan data riil resmi Popda XXII Sulteng dan sarana olahraga Kota Palu dengan data simulasi melalui badge informatif.
4. **Desain Bersih & Elegan:** Menggunakan identitas resmi Lambang Daerah Pemkot Palu, dokumentasi fotografi asli sarana dan atlet daerah, serta tombol navigasi admin modern.
5. **Kompilasi Sukses:** Seluruh kode JavaScript/Vue dan Tailwind CSS telah terkompilasi dalam bundel produksi tanpa error (`npm run build` sukses).

### 10.2 Rekomendasi Pengembangan Lanjutan
1. **Integrasi Geospasial IoT Lapangan:** Pemasangan sensor arus listrik atau gate sensor untuk mencatat kunjungan masyarakat secara otomatis ke basis data `venue_usage`.
2. **Ekspansi Model Multivariat Medali:** Menambahkan variabel independen baru seperti riwayat jam latihan fisik dan rasio pelatih bersertifikasi nasional pada model regresi OLS.
3. **Penyambungan Single Sign-On (SSO):** Mengintegrasikan autentikasi admin dengan portal SSO Pemerintah Kota Palu (Diskominfo Palu).

---
*Laporan ini disusun secara komprehensif sebagai dokumen dokumentasi teknis resmi, panduan arsitektur sistem, dan laporan pertanggungjawaban pengembangan perangkat lunak Satu Data Dispora Kota Palu.*
