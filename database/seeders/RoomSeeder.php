<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run()
    {
        Room::create([
            'room_number' => '101',
            'status' => 'available'
        ]);

        Room::create([
            'room_number' => '102',
            'status' => 'occupied'
        ]);

        Room::create([
            'room_number' => '103',
            'status' => 'available'
        ]);

        Room::create([
            'room_number' => '104',
            'status' => 'available'
        ]);

        Room::create([
            'room_number' => '105',
            'status' => 'occupied'
        ]);
    }
}
