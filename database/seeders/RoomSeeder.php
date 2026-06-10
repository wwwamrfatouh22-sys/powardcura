<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('rooms')) {
            return;
        }

        $rooms = [
            ['room_number' => '101', 'type' => 'Consultation', 'floor' => 1, 'capacity' => 2, 'status' => 'available'],
            ['room_number' => '102', 'type' => 'Consultation', 'floor' => 1, 'capacity' => 2, 'status' => 'occupied'],
            ['room_number' => '103', 'type' => 'Examination', 'floor' => 1, 'capacity' => 3, 'status' => 'available'],
            ['room_number' => '104', 'type' => 'Treatment', 'floor' => 1, 'capacity' => 2, 'status' => 'available'],
            ['room_number' => '105', 'type' => 'Observation', 'floor' => 1, 'capacity' => 4, 'status' => 'occupied'],
            ['room_number' => '201', 'type' => 'Consultation', 'floor' => 2, 'capacity' => 2, 'status' => 'available'],
            ['room_number' => '202', 'type' => 'Examination', 'floor' => 2, 'capacity' => 3, 'status' => 'available'],
            ['room_number' => '203', 'type' => 'Procedure', 'floor' => 2, 'capacity' => 4, 'status' => 'occupied'],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['room_number' => $room['room_number']], $room);
        }
    }
}
