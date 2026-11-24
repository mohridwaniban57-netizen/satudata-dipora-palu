  <?php

namespace Database\Seeders;

use App\Models\VenueType;
use Illuminate\Database\Seeder;

class VenueTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Stadion', 'design_life_years' => 30, 'base_interval_days' => 180],
            ['name' => 'Lapangan Olahraga', 'design_life_years' => 20, 'base_interval_days' => 90],
            ['name' => 'Kolam Renang', 'design_life_years' => 25, 'base_interval_days' => 120],
            ['name' => 'GOR/Gedung Olahraga', 'design_life_years' => 25, 'base_interval_days' => 120],
            ['name' => 'Lapangan Tembak', 'design_life_years' => 20, 'base_interval_days' => 90],
        ];

        foreach ($types as $type) {
            VenueType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'design_life_years' => $type['design_life_years'],
                    'base_interval_days' => $type['base_interval_days'],
                ]
            );
        }
    }
}
