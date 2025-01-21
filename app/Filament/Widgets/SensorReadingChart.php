<?php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SensorReadingChart extends ChartWidget
{
    protected static ?string $heading = 'Lectura de sensores por mes';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getReadingPerMonth();
        return [
            'labels' => $data['months'],
            'datasets' => [
                [
                    'label' => 'Lectura de sensores',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                    'data' => $data['readingsPerMonth'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getReadingPerMonth(): array
    {
        $now = Carbon::now();
        $readingsPerMonth = [];

        $months = collect(range(1, 12))->map(function ($month) use ($now, &$readingsPerMonth) {
            $count = SensorReading::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))->count();
            $readingsPerMonth[] = $count;

            return $now->month($month)->format('M');
        })->toArray();

        return [
            'readingsPerMonth' => $readingsPerMonth,
            'months' => $months
        ];
    }
}
