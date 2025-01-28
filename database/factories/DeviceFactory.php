<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->macAddress(),
            'location' => fake()->randomElement([
                'cocina',
                'Lavadero',
                'Primer pasillo',
                'Entrada principal'
            ]),
            'token' => bin2hex(random_bytes(16)),
            'status' => fake()->randomElement([
                'Activo',
                'Inactivo']),
        ];
    }
}
