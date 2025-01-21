<?php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SensorReadingChartPerDay extends ChartWidget
{
    protected static ?string $heading = 'Lectura de sensores por día';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getReadingPerDay();

        return [
            'labels' => $data['days'],
            'datasets' => [
                [
                    'label' => 'Lectura de sensores',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                    'data' => $data['readings'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getReadingPerDay(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Obtener solo los días con lecturas y sus totales
        $readings = SensorReading::selectRaw('DAY(created_at) as day, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        return [
            'days' => array_keys($readings), // Solo los días con registros
            'readings' => array_values($readings), // Totales correspondientes
        ];
    }
}
