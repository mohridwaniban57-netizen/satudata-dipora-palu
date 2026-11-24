<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MethodologyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'title' => 'Metodologi & Parameter Prediksi Satu Data Dispora Palu',
                'version' => '1.0',
                'models' => [
                    'medal_projection' => [
                        'name' => 'Proyeksi Perolehan Medali',
                        'method' => 'Ordinary Least Squares (OLS) Linear Trend',
                        'formula' => 'Points = 3*Emas + 2*Perak + 1*Perunggu; Trend = a + b * Tahun',
                        'uncertainty' => 'Rentang proyeksi ± 1 Standard Error of Estimate',
                        'fallback' => 'Rata-rata historis jika data per cabang olahraga kurang dari 3 event (Label: Low Confidence)',
                        'confidence_tiers' => [
                            'Low' => 'n < 3 event',
                            'Medium' => 'n = 3 - 4 event',
                            'High' => 'n >= 5 event',
                        ],
                    ],
                    'predictive_maintenance' => [
                        'name' => 'Skor Kondisi & Jadwal Pemeliharaan Fasilitas',
                        'method' => 'Multi-Criteria Risk Scoring (PRD §7.2)',
                        'formula' => [
                            'age_factor' => 'min(Usia / Masa Pakai Desain, 1.0) * 100 (Bobot 30%)',
                            'inspection_deficit' => '(100 - Skor Inspeksi Terakhir) (Bobot 50%)',
                            'damage_frequency' => 'min(Insiden 24 Bulan Terakhir / 5, 1.0) * 100 (Bobot 20%)',
                            'risk_score' => '0.30 * age_factor + 0.50 * inspection_deficit + 0.20 * damage_frequency',
                            'condition_score' => '100 - risk_score',
                        ],
                        'thresholds' => [
                            'Good' => 'Skor Kondisi >= 75 (Fasilitas Prima)',
                            'Watch' => 'Skor Kondisi 50 - 74.9 (Perlu Pengawasan)',
                            'Critical' => 'Skor Kondisi < 50 (Prioritas Perbaikan Mendesak)',
                        ],
                        'maintenance_interval' => 'Interval Hari = Base Interval * (Skor Kondisi / 100)',
                    ],
                    'levy_optimization' => [
                        'name' => 'Matriks Optimalisasi Retribusi & Pemanfaatan',
                        'method' => '2x2 Median Matrix & OLS Revenue Trend',
                        'formula' => [
                            'usage_index' => 'Total Kunjungan / (Kapasitas * Hari Operasional)',
                            'revenue_per_m2' => 'Total Retribusi / Luas Area (m²)',
                            'cost_recovery' => 'Total Retribusi / Total Biaya Pemeliharaan',
                        ],
                        'quadrants' => [
                            'Star' => 'Utilisasi Tinggi, Pendapatan Tinggi (Pertahankan kualitas)',
                            'Under-priced / leakage' => 'Utilisasi Tinggi, Pendapatan Rendah (Evaluasi tarif & pengawasan penarikan)',
                            'Niche / event-driven' => 'Utilisasi Rendah, Pendapatan Tinggi (Pertahankan tarif, buat program berkala)',
                            'Under-utilized' => 'Utilisasi Rendah, Pendapatan Rendah (Promosi penggunaan & evaluasi fungsi)',
                        ],
                    ],
                    'policy_simulation' => [
                        'coaching_budget' => 'Projected Points = Baseline + β * ΔAnggaran. Default β = 0.0000005 per Rp 1.',
                        'venue_renovation' => 'Skor kondisi dihitung ulang dengan target skor inspeksi; kenaikan estimasi kunjungan +10% per tingkat status yang naik.',
                    ],
                ],
                'privacy_framework' => [
                    'regulation' => 'UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)',
                    'principles' => [
                        'Data Minimization' => 'Tidak menyimpan NIK, nomor telepon, atau alamat rumah atlet.',
                        'Pseudonymization' => 'Nama lengkap atlet dienkripsi di database; publik hanya melihat agregat.',
                        'Small-Cell Suppression' => 'Semua sel agregat publik dengan jumlah kurang dari 5 disupresi menjadi "< 5".',
                    ],
                ],
                'data_sources' => [
                    ['name' => 'OpenStreetMap (OSM)', 'type' => 'Real', 'description' => 'Geolokasi koordinat fasilitas olahraga di Kota Palu'],
                    ['name' => 'BPS Kota Palu', 'type' => 'Real', 'description' => 'Data kependudukan 8 kecamatan di Kota Palu'],
                    ['name' => 'Popda XXII Sulteng 2024', 'type' => 'Real', 'description' => 'Hasil perolehan medali resmi kontingen Kota Palu (Juara Umum: 22 Emas, 13 Perak, 10 Perunggu)'],
                    ['name' => 'Dispora Simulation Dataset', 'type' => 'Dummy/Synthetic', 'description' => 'Data historis retribusi bulanan, jadwal inspeksi, dan profil atlet sintetis untuk keperluan analisis prediktif.'],
                ],
            ],
        ]);
    }
}
