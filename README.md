# Satu Data Dispora — Kota Palu
**Dashboard Analitik Prediktif & Sistem Informasi Sarana Olahraga, Atlet, dan Retribusi Daerah**
*Proyek Terpadu (Single Integrated Laravel + Vue 3 Monolith)*

Platform web terpadu Satu Data Dispora Kota Palu menggabungkan:
1. **Infrastruktur & Sarana Olahraga** terpetakan secara geospasial (GIS) di 8 kecamatan Kota Palu.
2. **Rekam Jejak Atlet & Kepemudaan** (OKP / Karang Taruna) dengan perlindungan privasi data pribadi (*UU No. 27/2022 tentang PDP*).
3. **Keuangan & Operasional** (realisasi retribusi sewa aset, biaya pemeliharaan berkala, dan anggaran pembinaan cabang olahraga).
4. **Mesin Analitik Prediktif Ringan & Explainable**:
   - **Proyeksi Perolehan Medali** per cabor (metode *Ordinary Least Squares / OLS*).
   - **Skor Risiko & Prioritas Pemeliharaan Aset** berbasis kondisi fisik, usia teknis, dan riwayat kerusakan.
   - **Matriks Optimalisasi Retribusi 4 Kuadran** (*Star, Leakage, Niche, Under-utilized*) beserta rekomendasi kebijakan.
   - **Simulator Kebijakan (What-If)**: Simulasi live penyesuaian anggaran pembinaan cabor & simulasi perpanjangan usia pakai aset pasca renovasi.

---

## 📁 Struktur 1 Folder Utuh (Tanpa Terpisah Backend & Frontend)

Seluruh sistem berada dalam **1 folder tunggal** di `d:\xampp8\htdocs\satudata-dipora-palu`:

```
satudata-dipora-palu/
├── app/                  # Controller, Models, Observers, & Services Analitik Prediktif
├── bootstrap/            # Bootstrap Laravel & registrasi Sanctum
├── config/               # analytics.php, database.php, cors.php, dll.
├── database/             # 17 Migrations & Seeders (data riil Popda 2024 & dummy Palu)
├── public/               # Entrypoint web & hasil build aset SPA (public/build)
├── resources/
│   ├── css/app.css       # Tailwind CSS & custom leaflet styles
│   ├── js/               # Seluruh kode frontend Vue 3 (Components, Views, Stores, Router)
│   └── views/app.blade.php # Single Blade view yang me-render Vue 3 SPA
├── routes/
│   ├── api.php           # 89 API routes (Public & Admin terproteksi Sanctum)
│   └── web.php           # Catch-all route yang mengarahkan ke Vue 3 SPA
├── artisan               # CLI Laravel
├── composer.json         # Dependensi backend PHP
├── package.json          # Dependensi frontend Vue 3, ECharts, Leaflet, Tailwind
├── run.bat               # 1-Click Server Runner
└── README.md             # Dokumentasi proyek
```

---

## 🚀 Cara Menjalankan Aplikasi

Cukup **1 server saja**! Pastikan **Apache & MySQL di XAMPP** sudah aktif.

### Opsi 1 — Menggunakan Runner Batch (1 Klik)
Klik ganda file:
```
d:\xampp8\htdocs\satudata-dipora-palu\run.bat
```
Aplikasi langsung dapat diakses di browser pada:
👉 **`http://localhost:8000`**

### Opsi 2 — Jalankan via Terminal
Buka PowerShell atau Command Prompt di folder ini:
```powershell
d:\xampp8\php\php.exe artisan serve --port=8000
```
Buka browser: `http://localhost:8000`

### Opsi 3 — Langsung via Apache XAMPP
Karena proyek berada di dalam folder `htdocs`, Anda juga dapat membukanya langsung via URL Apache:
👉 **`http://localhost/satudata-dipora-palu/public/`**

---

## 🔐 Kredensial Akun Administrator

Untuk masuk ke Panel Admin:
- **URL Login:** `http://localhost:8000/admin/login`
- **Email:** `admin@dispora-palu.go.id`
- **Password:** `Admin@12345`

---

## 🗺️ Panduan Navigasi & Fitur

1. **Beranda (`/`):** Ringkasan Eksekutif, KPI Cards, Mini Map Palu, Top 5 Fasilitas Prioritas Pemeliharaan, Grafik Tren Medali, Komposisi Tipe Sarana, Distribusi Kondisi per Kecamatan.
2. **Peta Fasilitas (`/map`):** Peta Interaktif Leaflet (Kota Palu `-0.8917, 119.8707`), Switcher Layer Pin Marker & Heatmap Pemanfaatan Fasilitas, Filter Dinamis (Kecamatan, Jenis Fasilitas, Status Kondisi), Sinkronisasi klik pin peta dengan daftar venue (*flyTo* & *popup*).
3. **Detail Fasilitas (`/venues/:id`):** Profil aset, gauge skor kondisi teknis, riwayat inspeksi & pemeliharaan, grafik tren bulanan kunjungan & retribusi.
4. **Statistik Atlet & Pemuda (`/athletes`):** Agregat atlet per cabor, sebaran per kecamatan, piramida demografi usia & gender, sebaran OKP & Karang Taruna. Dilengkapi **Supresi Sel Kecil ($< 5$)** sesuai UU PDP.
5. **Prediksi Analitik (`/predictions`):**
   - *Tab Proyeksi Medali:* Estimasi poin & medali event berikutnya, interval kepercayaan ($\pm 1$ SE), label confidence (*Tinggi/Sedang/Rendah*).
   - *Tab Prioritas Pemeliharaan:* Peringkat risiko kerusakan aset dan estimasi tanggal pemeliharaan berikutnya.
   - *Tab Optimalisasi Retribusi:* Scatter plot 4 kuadran pemanfaatan vs pendapatan serta rekomendasi teks otomatis.
   - Setiap tab dilengkapi tombol **"Bagaimana ini dihitung?"** (*ExplainPanel*).
6. **Simulator Kebijakan (`/simulator`):**
   - Slider penyesuaian anggaran pembinaan cabor ($\pm\%$) $\rightarrow$ update live proyeksi medali.
   - Simulasi target skor renovasi aset $\rightarrow$ rekalkulasi live skor kondisi & perkiraan tanggal pemeliharaan.
7. **Metodologi & Sumber Data (`/methodology`):** Transparansi rumus OLS, pembobotan skor kondisi, dan pelabelan data *hybrid* (riil vs simulasi).
8. **Notis Privasi (`/privacy`):** Komitmen kepatuhan UU No. 27/2022.
9. **Panel Admin (`/admin`):** Dashboard Kualitas Data, CRUD Sarana/Venue (lengkap dengan *interactive map pin picker* koordinat Palu), CRUD Atlet (nama asli terdekripsi khusus admin), Impor CSV, dan Log Audit.

---

## 🛠️ Perintah Tambahan (Opsional)

- **Kompilasi Ulang Frontend (jika ada perubahan kode Vue):**
  ```powershell
  npm run build
  ```
- **Menjalankan Vite Live HMR saat pengembangan:**
  ```powershell
  npm run dev
  ```
- **Menjalankan Unit & Feature Test:**
  ```powershell
  d:\xampp8\php\php.exe artisan test
  ```
