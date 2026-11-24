<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            'Atletik',
            'Sepak Bola',
            'Bulu Tangkis',
            'Renang',
            'Tinju',
            'Pencak Silat',
            'Bola Voli',
            'Bola Basket',
            'Tenis Meja',
            'Panahan',
        ];

        foreach ($sports as $name) {
            Sport::updateOrCreate(['name' => $name]);
        }
    }
}
