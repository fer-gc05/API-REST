<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SensorReading>
 */
class SensorReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $device_id = Device::inRandomOrder()->first()->id;

        return [
            'device_id' => $device_id, // ID de dispositivo aleatorio
            'temperature' => fake()->randomFloat(2, 0, 50), // Temperatura entre 0 y 50 grados
            'humidity' => fake()->randomFloat(2, 0, 100), // Humedad entre 0 y 100%
            'smoke_level' => fake()->randomFloat(2, 0, 100), // Nivel de humo entre 0 y 100
            'gas_level' => fake()->randomFloat(2, 0, 100), // Nivel de gas entre 0 y 100
            'created_at' => fake()->dateTimeBetween('-12 months', 'now') , // Fecha de creacion del registro, rango de 6 meses atras.
        ];
    }
}
