<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use const Adminer\DB;

class SessionDetailSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.88000000, 'longitude' => 111.46000000, 'sequence' => 1],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87998204, 'longitude' => 111.46000000, 'sequence' => 2],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87996408, 'longitude' => 111.46000000, 'sequence' => 3],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87994612, 'longitude' => 111.46000000, 'sequence' => 4],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87992816, 'longitude' => 111.46000000, 'sequence' => 5],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87991020, 'longitude' => 111.46000000, 'sequence' => 6],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87991020, 'longitude' => 111.45998187, 'sequence' => 7],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87991020, 'longitude' => 111.45996374, 'sequence' => 8],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87992816, 'longitude' => 111.45996374, 'sequence' => 9],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87994612, 'longitude' => 111.45996374, 'sequence' => 10],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87996408, 'longitude' => 111.45996374, 'sequence' => 11],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.87998204, 'longitude' => 111.45996374, 'sequence' => 12],
            ['session_id' => 2, 'recorded_at' => Carbon::now(), 'latitude' => -7.88000000, 'longitude' => 111.45996374, 'sequence' => 13],
        ];

        DB::table('session_detail')->insert($data);
    }
}
