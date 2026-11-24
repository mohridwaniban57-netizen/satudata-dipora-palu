<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Athlete;
use App\Models\Budget;
use App\Models\District;
use App\Models\Inspection;
use App\Models\MaintenanceRecord;
use App\Models\Sport;
use App\Models\Venue;
use App\Models\VenueRevenue;
use App\Models\VenueType;
use App\Models\VenueUsage;
use App\Models\YouthOrg;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $faker->seed(42);

        $districts = District::all()->keyBy('name');
        $venueTypes = VenueType::all()->keyBy('name');
        $sports = Sport::all();

        // 1. 25 Venues
        $venueTemplates = [
            // Palu Barat
            ['name' => 'Stadion Mini Palu Barat', 'type' => 'Stadion', 'district' => 'Palu Barat', 'lat' => -0.865200, 'lng' => 119.851000, 'area' => 8500, 'cap' => 3000, 'year' => 2005],
            ['name' => 'GOR Siranindi Palu Barat', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Palu Barat', 'lat' => -0.862100, 'lng' => 119.854200, 'area' => 2400, 'cap' => 1200, 'year' => 2010],
            ['name' => 'Lapangan Olahraga Kamonji', 'type' => 'Lapangan Olahraga', 'district' => 'Palu Barat', 'lat' => -0.868500, 'lng' => 119.857300, 'area' => 1500, 'cap' => 300, 'year' => 2012],

            // Palu Timur
            ['name' => 'Kolam Renang Madani', 'type' => 'Kolam Renang', 'district' => 'Palu Timur', 'lat' => -0.874100, 'lng' => 119.882100, 'area' => 3200, 'cap' => 800, 'year' => 2015],
            ['name' => 'GOR Gelora Palu Timur', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Palu Timur', 'lat' => -0.871500, 'lng' => 119.879500, 'area' => 2100, 'cap' => 1000, 'year' => 2008],
            ['name' => 'Lapangan Tenis Besusu', 'type' => 'Lapangan Olahraga', 'district' => 'Palu Timur', 'lat' => -0.878200, 'lng' => 119.885600, 'area' => 1800, 'cap' => 400, 'year' => 2011],

            // Palu Selatan
            ['name' => 'Stadion Mini Petobo', 'type' => 'Stadion', 'district' => 'Palu Selatan', 'lat' => -0.905400, 'lng' => 119.873200, 'area' => 7800, 'cap' => 2500, 'year' => 2016],
            ['name' => 'Lapangan Olahraga Birobuli', 'type' => 'Lapangan Olahraga', 'district' => 'Palu Selatan', 'lat' => -0.899200, 'lng' => 119.871500, 'area' => 1600, 'cap' => 500, 'year' => 2014],
            ['name' => 'GOR Bulutangkis Palu Selatan', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Palu Selatan', 'lat' => -0.902300, 'lng' => 119.876800, 'area' => 1900, 'cap' => 600, 'year' => 2018],

            // Palu Utara
            ['name' => 'Lapangan Sepak Bola Kayumalue', 'type' => 'Lapangan Olahraga', 'district' => 'Palu Utara', 'lat' => -0.824100, 'lng' => 119.872500, 'area' => 6500, 'cap' => 1500, 'year' => 2009],
            ['name' => 'GOR Mamboro', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Palu Utara', 'lat' => -0.828500, 'lng' => 119.876200, 'area' => 2000, 'cap' => 800, 'year' => 2017],
            ['name' => 'Kolam Renang Taipa', 'type' => 'Kolam Renang', 'district' => 'Palu Utara', 'lat' => -0.819200, 'lng' => 119.869800, 'area' => 2800, 'cap' => 500, 'year' => 2019],

            // Tatanga
            ['name' => 'Lapangan Sepak Bola Duyu', 'type' => 'Lapangan Olahraga', 'district' => 'Tatanga', 'lat' => -0.923400, 'lng' => 119.861200, 'area' => 6200, 'cap' => 1200, 'year' => 2013],
            ['name' => 'GOR Serbaguna Tatanga', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Tatanga', 'lat' => -0.919800, 'lng' => 119.858700, 'area' => 2200, 'cap' => 900, 'year' => 2016],
            ['name' => 'Lapangan Voli Palupi', 'type' => 'Lapangan Olahraga', 'district' => 'Tatanga', 'lat' => -0.926500, 'lng' => 119.864300, 'area' => 1200, 'cap' => 350, 'year' => 2015],

            // Ulujadi
            ['name' => 'Lapangan Tembak Perbakin Donggala Kodi', 'type' => 'Lapangan Tembak', 'district' => 'Ulujadi', 'lat' => -0.854300, 'lng' => 119.832100, 'area' => 5500, 'cap' => 400, 'year' => 2010],
            ['name' => 'GOR Tipo Ulujadi', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Ulujadi', 'lat' => -0.849200, 'lng' => 119.835400, 'area' => 1800, 'cap' => 700, 'year' => 2018],
            ['name' => 'Lapangan Olahraga Silae', 'type' => 'Lapangan Olahraga', 'district' => 'Ulujadi', 'lat' => -0.858700, 'lng' => 119.839800, 'area' => 1400, 'cap' => 300, 'year' => 2012],

            // Mantikulore
            ['name' => 'Stadion Utama Kawatuna', 'type' => 'Stadion', 'district' => 'Mantikulore', 'lat' => -0.891200, 'lng' => 119.914500, 'area' => 12500, 'cap' => 8000, 'year' => 2004],
            ['name' => 'Kolam Renang Prestasi Talise', 'type' => 'Kolam Renang', 'district' => 'Mantikulore', 'lat' => -0.884500, 'lng' => 119.908200, 'area' => 3500, 'cap' => 1200, 'year' => 2016],
            ['name' => 'GOR Panahan Tondo', 'type' => 'Lapangan Olahraga', 'district' => 'Mantikulore', 'lat' => -0.876300, 'lng' => 119.912400, 'area' => 4500, 'cap' => 600, 'year' => 2019],
            ['name' => 'Lapangan Olahraga Lasoani', 'type' => 'Lapangan Olahraga', 'district' => 'Mantikulore', 'lat' => -0.898400, 'lng' => 119.919800, 'area' => 2000, 'cap' => 450, 'year' => 2014],

            // Tawaeli
            ['name' => 'Lapangan Sepak Bola Pantoloan', 'type' => 'Lapangan Olahraga', 'district' => 'Tawaeli', 'lat' => -0.774500, 'lng' => 119.892100, 'area' => 7000, 'cap' => 1800, 'year' => 2007],
            ['name' => 'GOR Tawaeli Mandiri', 'type' => 'GOR/Gedung Olahraga', 'district' => 'Tawaeli', 'lat' => -0.771200, 'lng' => 119.889400, 'area' => 2100, 'cap' => 750, 'year' => 2017],
            ['name' => 'Lapangan Voli Baiya', 'type' => 'Lapangan Olahraga', 'district' => 'Tawaeli', 'lat' => -0.778900, 'lng' => 119.895300, 'area' => 1300, 'cap' => 300, 'year' => 2015],
        ];

        $createdVenues = [];
        foreach ($venueTemplates as $vt) {
            $districtId = $districts[$vt['district']]->id;
            $venueTypeId = $venueTypes[$vt['type']]->id;

            $venue = Venue::updateOrCreate(
                ['name' => $vt['name']],
                [
                    'venue_type_id' => $venueTypeId,
                    'district_id'   => $districtId,
                    'address'       => 'Jl. Olahraga No. ' . $faker->numberBetween(1, 100) . ', Kec. ' . $vt['district'] . ', Kota Palu',
                    'latitude'      => $vt['lat'],
                    'longitude'     => $vt['lng'],
                    'area_m2'       => $vt['area'],
                    'capacity'      => $vt['cap'],
                    'year_built'    => $vt['year'],
                    'manager'       => $faker->randomElement(['Dispora Kota Palu', 'KONI Kota Palu', 'Pengelola UPTD Olahraga']),
                    'state'         => 'published',
                    'is_dummy'      => true,
                    'data_source'   => 'dummy',
                ]
            );

            $createdVenues[] = $venue;

            // 1-3 Inspeksi per venue
            $inspectionCount = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $inspectionCount; $i++) {
                $daysAgo = $faker->numberBetween(15, 600);
                Inspection::create([
                    'venue_id'     => $venue->id,
                    'inspected_on' => Carbon::now()->subDays($daysAgo)->toDateString(),
                    'score'        => $faker->numberBetween(45, 95),
                    'notes'        => $faker->randomElement([
                        'Kondisi lapangan dan fasilitas terawat baik.',
                        'Perlu perbaikan ringan pada toilet dan lampu penerangan.',
                        'Lantai lapangan memerlukan perbaikan lapisan dan pengecatan ulang.',
                        'Struktur atap dan tribun dalam kondisi aman dan prima.',
                        'Drainase sekitar lapangan perlu pembersihan berkala.',
                    ]),
                ]);
            }

            // 1-2 Maintenance records per venue
            $maintCount = $faker->numberBetween(1, 2);
            for ($m = 0; $m < $maintCount; $m++) {
                $maintDaysAgo = $faker->numberBetween(30, 500);
                MaintenanceRecord::create([
                    'venue_id'    => $venue->id,
                    'done_on'     => Carbon::now()->subDays($maintDaysAgo)->toDateString(),
                    'type'        => $faker->randomElement(['Perbaikan Rutin', 'Pengecatan', 'Perbaikan Lampu/Listrik', 'Perbaikan Pipa/Air', 'Renovasi Ringan']),
                    'cost'        => $faker->numberBetween(5, 50) * 500000,
                    'is_incident' => $faker->boolean(20),
                    'description' => $faker->randomElement([
                        'Penggantian lampu penerangan dan instalasi kabel.',
                        'Pengecatan garis lapangan dan tribun penonton.',
                        'Perbaikan pompa dan sistem filtrasi air.',
                        'Perbaikan atap bocor dan talang air.',
                        'Pemeliharaan berkala rumput dan permukaan lapangan.',
                    ]),
                ]);
            }

            // 24 bulan Usage + Revenue (2023-01 to 2024-12)
            $startDate = Carbon::create(2023, 1, 1);
            for ($m = 0; $m < 24; $m++) {
                $monthDate = $startDate->copy()->addMonths($m)->startOfMonth()->toDateString();

                $visits = $faker->numberBetween(120, 1800);
                VenueUsage::updateOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'month'    => $monthDate,
                    ],
                    [
                        'visits' => $visits,
                    ]
                );

                $revenueAmount = $faker->numberBetween(800, 15000) * 1000;
                VenueRevenue::updateOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'month'    => $monthDate,
                    ],
                    [
                        'amount' => $revenueAmount,
                    ]
                );
            }
        }

        // 2. 150 Atlet (ATL-0001 s/d ATL-0150)
        $districtList = $districts->values();
        $sportsList = $sports->values();
        $levels = ['club', 'district', 'province', 'national'];

        $createdAthletes = [];
        for ($i = 1; $i <= 150; $i++) {
            $code = sprintf('ATL-%04d', $i);
            $gender = $faker->randomElement(['m', 'f']);
            $firstName = $gender === 'm' ? $faker->firstNameMale : $faker->firstNameFemale;
            $lastName = $faker->lastName;
            $fullName = $firstName . ' ' . $lastName;

            $birthYear = $faker->numberBetween(1998, 2011);
            $isMinor = ($birthYear >= 2007); // < 18 in 2024

            $sport = $sportsList->random();
            $district = $districtList->random();

            $athlete = Athlete::updateOrCreate(
                ['code' => $code],
                [
                    'name_encrypted'   => $fullName,
                    'birth_year'       => $birthYear,
                    'gender'           => $gender,
                    'sport_id'         => $sport->id,
                    'district_id'      => $district->id,
                    'level'            => $faker->randomElement($levels),
                    'is_minor'         => $isMinor,
                    'consent_recorded' => true,
                    'active'           => true,
                    'state'            => 'published',
                    'is_dummy'         => true,
                    'data_source'      => 'dummy',
                ]
            );

            $createdAthletes[] = $athlete;

            // Sebanyak 50 atlet memiliki 1-2 prestasi/achievements
            if ($i <= 50) {
                $achieveCount = $faker->numberBetween(1, 2);
                for ($a = 0; $a < $achieveCount; $a++) {
                    Achievement::create([
                        'athlete_id' => $athlete->id,
                        'event'      => $faker->randomElement([
                            'Kejuaraan Pelajar Sulteng',
                            'Piala Walikota Palu',
                            'Porprov Sulteng IX',
                            'O2SN Kota Palu',
                            'Kejurda Atlet Muda Sulteng',
                        ]),
                        'medal'      => $faker->randomElement(['gold', 'silver', 'bronze']),
                        'year'       => $faker->numberBetween(2022, 2024),
                    ]);
                }
            }
        }

        // 3. Budgets per sport (2023 - 2024)
        foreach ($sports as $sport) {
            foreach ([2023, 2024] as $year) {
                Budget::updateOrCreate(
                    [
                        'sport_id' => $sport->id,
                        'year'     => $year,
                    ],
                    [
                        'amount'   => $faker->numberBetween(75, 450) * 1000000,
                        'state'    => 'published',
                        'is_dummy' => true,
                    ]
                );
            }
        }

        // 4. 20 Youth Organizations (OKP & Karang Taruna)
        $youthNames = [
            'Karang Taruna Tunas Muda Palu Barat',
            'OKP Pemuda Mandiri Palu Barat',
            'Karang Taruna Cipta Karya Palu Timur',
            'OKP Pelajar Berkarya Palu Timur',
            'Karang Taruna Bina Remaja Palu Selatan',
            'OKP Generasi Sportif Palu Selatan',
            'Karang Taruna Harapan Jaya Palu Utara',
            'OKP Pemuda Bahari Palu Utara',
            'Karang Taruna Karsa Remaja Tatanga',
            'OKP Sinergi Tatanga',
            'Karang Taruna Pelita Mandiri Ulujadi',
            'OKP Pemuda Garda Ulujadi',
            'Karang Taruna Karya Bersama Mantikulore',
            'OKP Pemuda Tadulako Mantikulore',
            'Karang Taruna Bunga Bangsa Mantikulore',
            'Karang Taruna Mekar Jaya Tawaeli',
            'OKP Pemuda Pelabuhan Tawaeli',
            'Karang Taruna Bina Prestasi Palu Selatan',
            'OKP Kreatif Muda Mantikulore',
            'Karang Taruna Bintang Timur Palu Timur',
        ];

        foreach ($youthNames as $idx => $name) {
            $district = $districtList[$idx % $districtList->count()];
            $type = str_contains($name, 'Karang Taruna') ? 'KarangTaruna' : 'OKP';

            YouthOrg::updateOrCreate(
                ['name' => $name],
                [
                    'type'           => $type,
                    'district_id'    => $district->id,
                    'members'        => $faker->numberBetween(25, 200),
                    'activity_level' => $faker->randomElement(['low', 'medium', 'high']),
                    'state'          => 'published',
                    'is_dummy'       => true,
                    'data_source'    => 'dummy',
                ]
            );
        }
    }
}
