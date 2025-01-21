<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use App\Models\SensorReading;
use Filament\Widgets\ChartWidget;

class TopDevicesChart extends ChartWidget
{
    protected static ?string $heading = 'Dispositivos con más lecturas';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $data = $this->getTopDevicesData();

        return [
            'labels' => $data['devices'],
            'datasets' => [
                [
                    'label' => 'Total de lecturas',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgb(75, 192, 192)',
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

    protected function getTopDevicesData(): array
    {
        $topDevices = SensorReading::selectRaw('device_id, COUNT(*) as total')
            ->groupBy('device_id')
            ->orderByDesc('total')
            ->limit(10) 
            ->get();

        $devices = $topDevices->map(function ($reading) {
            $device = Device::find($reading->device_id);
            return $device ? $device->name : 'Desconocido';
        })->toArray();

        $readings = $topDevices->pluck('total')->toArray();

        return [
            'devices' => $devices,
            'readings' => $readings,
        ];
    }
}
