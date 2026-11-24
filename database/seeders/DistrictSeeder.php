<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            ['name' => 'Palu Barat', 'population' => 45000],
            ['name' => 'Palu Timur', 'population' => 55000],
            ['name' => 'Palu Selatan', 'population' => 60000],
            ['name' => 'Palu Utara', 'population' => 35000],
            ['name' => 'Tatanga', 'population' => 40000],
            ['name' => 'Ulujadi', 'population' => 25000],
            ['name' => 'Mantikulore', 'population' => 70000],
            ['name' => 'Tawaeli', 'population' => 30000],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['name' => $district['name']],
                ['population' => $district['population']]
            );
        }
    }
}
