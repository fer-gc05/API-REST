<?php

namespace App\Filament\Widgets;

use App\Models\Alert;
use Filament\Widgets\ChartWidget;

class AlertChart extends ChartWidget
{
    protected static ?string $heading = 'Tipo de alertas con mayor frecuencia';

    protected static ?int $sort = 4;

    protected function getData(): array
    {

        $data = $this->getAlertData();

        return [
            'labels' => $data['types'],
            'datasets' => [
                [
                    'label' => 'Total de alertas',
                    'backgroundColor' => 'rgba(99, 62, 233, 0.2)',
                    'borderColor' => 'rgb(122, 83, 242)',
                    'borderWidth' => 1,
                    'data' => $data['totals'], 
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getAlertData(): array
    {
        $alerts = Alert::selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        return [
            'types' => array_keys($alerts), // Tipos de alerta
            'totals' => array_values($alerts), // Totales por tipo
        ];
    }
}
