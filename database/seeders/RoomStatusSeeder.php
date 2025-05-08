<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all rooms
        $rooms = DB::table('room_details')->get();
        
        foreach ($rooms as $room) {
            // If room is available, set status to vacant, otherwise occupied
            $status = $room->available ? 'vacant' : 'occupied';
            
            // Update the status
            DB::table('room_details')
                ->where('room_id', $room->room_id)
                ->update(['status' => $status]);
        }
    }
} 