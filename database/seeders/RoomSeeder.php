<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::upsert([
            [
                'id' => 1,
                'name' => 'Raum 1 Haut Laser',
                'short_code' => 'F1',
                'branch_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Raum 2 Haut',
                'short_code' => 'F2',
                'branch_id' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Raum 3 Laser',
                'short_code' => 'F3',
                'branch_id' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Raum 4 Laser',
                'short_code' => 'F4',
                'branch_id' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Raum 5 Laser',
                'short_code' => 'F5',
                'branch_id' => 1,
            ],
            [
                'id' => 6,
                'name' => 'Raum 6 Laser',
                'short_code' => 'F6',
                'branch_id' => 1,
            ],
            [
                'id' => 7,
                'name' => 'Raum 7 WI Alle',
                'short_code' => 'W1',
                'branch_id' => 2,
            ],
            [
                'id' => 8,
                'name' => 'Raum 7 Laser',
                'short_code' => 'F7',
                'branch_id' => 1,
            ],
            [
                'id' => 9,
                'name' => 'Kosmetik',
                'short_code' => 'V1',
                'branch_id' => 3,
            ],
            [
                'id' => 10,
                'name' => 'Raum 2 WI',
                'short_code' => 'W2',
                'branch_id' => 2,
            ],
            [
                'id' => 11,
                'name' => 'Raum 8',
                'short_code' => 'F8',
                'branch_id' => 1,
            ],
            [
                'id' => 12,
                'name' => 'Raum 3 WI',
                'short_code' => 'W3',
                'branch_id' => 2,
            ],
        ], ['id']);
    }
}
