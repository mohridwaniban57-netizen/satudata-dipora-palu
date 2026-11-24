<?php

namespace Database\Seeders;

use App\Models\MedalHistory;
use App\Models\Sport;
use Illuminate\Database\Seeder;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        $event = 'Popda XXII Sulteng 2024';
        $year = 2024;
        $dataSource = 'luwuktimes.id';

        $medals = [
            'Atletik'      => ['gold' => 5, 'silver' => 3, 'bronze' => 2],
            'Renang'       => ['gold' => 4, 'silver' => 2, 'bronze' => 1],
            'Bulu Tangkis' => ['gold' => 3, 'silver' => 2, 'bronze' => 2],
            'Pencak Silat' => ['gold' => 3, 'silver' => 2, 'bronze' => 2],
            'Tinju'        => ['gold' => 2, 'silver' => 2, 'bronze' => 1],
            'Sepak Bola'   => ['gold' => 1, 'silver' => 0, 'bronze' => 1],
            'Bola Voli'    => ['gold' => 1, 'silver' => 1, 'bronze' => 0],
            'Bola Basket'  => ['gold' => 1, 'silver' => 0, 'bronze' => 0],
            'Tenis Meja'   => ['gold' => 1, 'silver' => 1, 'bronze' => 0],
            'Panahan'      => ['gold' => 1, 'silver' => 0, 'bronze' => 1],
        ];

        foreach ($medals as $sportName => $counts) {
            $sport = Sport::where('name', $sportName)->first();
            if ($sport) {
                MedalHistory::updateOrCreate(
                    [
                        'sport_id' => $sport->id,
                        'event'    => $event,
                        'year'     => $year,
                    ],
                    [
                        'gold'        => $counts['gold'],
                        'silver'      => $counts['silver'],
                        'bronze'      => $counts['bronze'],
                        'state'       => 'published',
                        'is_dummy'    => false,
                        'data_source' => $dataSource,
                    ]
                );
            }
        }
    }
}
