<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionDetailSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.865417, 'longitude' => 111.466117, 'sequence' => 1],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86539904, 'longitude' => 111.466117, 'sequence' => 2],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86538108, 'longitude' => 111.466117, 'sequence' => 3],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86536312, 'longitude' => 111.466117, 'sequence' => 4],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86534516, 'longitude' => 111.466117, 'sequence' => 5],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.8653272, 'longitude' => 111.466117, 'sequence' => 6],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.8653272, 'longitude' => 111.46609887, 'sequence' => 7],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.8653272, 'longitude' => 111.46608074, 'sequence' => 8],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86534516, 'longitude' => 111.46608074, 'sequence' => 9],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86536312, 'longitude' => 111.46608074, 'sequence' => 10],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86538108, 'longitude' => 111.46608074, 'sequence' => 11],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.86539904, 'longitude' => 111.46608074, 'sequence' => 12],
            ['session_id' => 1, 'speed' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.865417, 'longitude' => 111.46608074, 'sequence' => 13],
        ];

        DB::table('session_detail')->insert($data);
    }
}
