<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::upsert([
            [
                'id' => 1,
                'name' => 'Haarentfernung',
                'short_code' => 'HR',
                'color' => '#009999',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 3,
                'name' => 'Haut Laser',
                'short_code' => 'HA',
                'color' => '#a26d7f',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 4,
                'name' => 'Tattoo',
                'short_code' => 'TA',
                'color' => '#b85119',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 5,
                'name' => 'Unterspritzung',
                'short_code' => 'UG',
                'color' => '#8619a4',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 6,
                'name' => 'Pigment/Altersflecken',
                'short_code' => 'PI',
                'color' => '#e2eb60',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 7,
                'name' => 'Kosmetik',
                'short_code' => 'FO',
                'color' => '#7494ff',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 8,
                'name' => 'Fettentfernung',
                'short_code' => 'FE',
                'color' => '#98a333',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 9,
                'name' => 'Äderchen',
                'short_code' => 'ADE',
                'color' => '#fd77a6',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 17,
                'name' => 'Besenreiser',
                'short_code' => 'BES',
                'color' => '#f07538',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 18,
                'name' => 'Adminisstration',
                'short_code' => 'ADM',
                'color' => '#c8bcbc',
                'text_color' => '#ffffff',
            ],
            [
                'id' => 22,
                'name' => 'Dehnungsstreifen Nordlys',
                'short_code' => 'DEHNO',
                'color' => '#825959',
                'text_color' => '#ffffff',
            ],
        ], ['id']);
    }
}
