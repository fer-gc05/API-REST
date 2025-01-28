<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Device;
use App\Models\SensorReading;
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
        Device::factory(100)->create();
        SensorReading::factory(2000)->create();
        Alert::factory(1000)->create();


        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
