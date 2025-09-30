<?php

namespace Database\Seeders;

use App\Models\TreatmentType;
use Illuminate\Database\Seeder;

class TreatmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TreatmentType::upsert([
            [
                'id' => 1,
                'name' => 'ATV Tattoo',
                'color' => '#b85119',
            ],
            [
                'id' => 2,
                'name' => 'Pico',
                'color' => '#9c4911',
            ],
            [
                'id' => 3,
                'name' => 'Alexandrit',
                'color' => '#009999',
            ],
            [
                'id' => 4,
                'name' => 'Microneedle',
                'color' => '#007373',
            ],
            [
                'id' => 5,
                'name' => 'CO2',
                'color' => '#007373',
            ],
            [
                'id' => 6,
                'name' => 'Nordlys',
                'color' => '#007373',
            ],
            [
                'id' => 7,
                'name' => 'Yag Laser',
                'color' => '#fc5a03',
            ],
            [
                'id' => 15,
                'name' => 'Hydrafacial',
                'color' => '#009999',
            ],
            [
                'id' => 16,
                'name' => 'Fett-Weg-Spritze',
                'color' => '#f78d8d',
            ],
            [
                'id' => 17,
                'name' => 'Kriolypolyse',
                'color' => '#30de0d',
            ],
            [
                'id' => 18,
                'name' => 'Vanqish Me',
                'color' => '#5ddfa7',
            ],
            [
                'id' => 19,
                'name' => 'Botox',
                'color' => '#f66598',
            ],
            [
                'id' => 20,
                'name' => 'Hyaluron',
                'color' => '#f06a6a',
            ],
        ], ['id']);
    }
}
