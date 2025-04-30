<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Machine;
use App\Models\Session;
use App\Models\SessionDetail;
use App\Models\Settings;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin1234'), // Password: password
        ]);

        // Dummy Drivers
        $drivers = Driver::factory()->count(3)->create();

        // Dummy Machines
        $machines = Machine::factory()->count(3)->create();


        // Generate Sessions with Details
        $session = Session::create([
            'date' => now()->toDateString(),
            'latitude' => -7.8654170,
            'longitude' => 111.4661170,
            'total_area' => 0,
            'average_speed' => rand(3, 10),
            'machine_id' => 1,
            'driver_id' => 1,
            'start_time' => now()->toDateTimeString(),
            'end_time' => now()->toDateTimeString(),
            'last_update_at' => now()->toDateTimeString(),
        ]);
        $session = Session::create([
            'date' => now()->toDateString(),
            'latitude' => -7.8800000,
            'longitude' => 111.4600000,
            'total_area' => 0,
            'average_speed' => rand(3, 10),
            'machine_id' => 2,
            'driver_id' => 1,
            'start_time' => now()->toDateTimeString(),
            'end_time' => now()->toDateTimeString(),
            'last_update_at' => now()->toDateTimeString(),
        ]);

        Settings::create([
            'name' => 'hargaPerMeter',
            'value' => 2000,
        ]);
    }
}
