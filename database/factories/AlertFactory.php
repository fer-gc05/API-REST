<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alert>
 */
class AlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Seleccionar un dispositivo aleatorio
        $device_id = Device::inRandomOrder()->first()->id;

        // Obtener las lecturas más recientes o relevantes del dispositivo
        $mostCriticalReading = SensorReading::where('device_id', $device_id)
            ->orderByDesc(function ($query) {
                $query->selectRaw('GREATEST(temperature, humidity, smoke_level, gas_level)'); // Máximo de los valores
            })
            ->first();

        // Determinar el tipo de alerta basándose en el valor más alto
        $type = null;
        $maxValue = null;

        if ($mostCriticalReading) {
            $values = [
                'Temperatura' => $mostCriticalReading->temperature,
                'Humedad' => $mostCriticalReading->humidity,
                'Nivel de humo' => $mostCriticalReading->smoke_level,
                'Nivel de gas' => $mostCriticalReading->gas_level,
            ];

            // Encontrar el valor más alto y su tipo
            $type = array_search(max($values), $values); // Devuelve la clave del valor más alto
            $maxValue = max($values);
        } else {
            // Si no hay lecturas, asignar valores predeterminados
            $type = fake()->randomElement(['Temperatura', 'Humedad', 'Nivel de humo', 'Nivel de gas']);
            $maxValue = fake()->randomFloat(2, 0, 100);
        }

        return [
            'device_id' => $device_id,
            'type' => ucfirst(str_replace('_', ' ', $type)), // Capitalizar el tipo y reemplazar guiones bajos
            'status' => fake()->randomElement(['Ejecutada', 'Pendiente']),
            'value' => $maxValue, // El valor crítico
            'max_value' => fake()->randomFloat(2, $maxValue, 100), // Un valor superior basado en el crítico
        ];
    }

}
