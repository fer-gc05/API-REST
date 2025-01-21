<?php

namespace App\Filament\Widgets;

use App\Models\Alert;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Dispositivos registrados', Device::count())
                ->icon('heroicon-o-device-tablet')
                ->label('Dispositivos')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('success')
                ->chart([17, 23, 40, 30, 20, 35, 40, 30, 20, 35, 30, 20])
                ->url(route('filament.admin.resources.devices.index')),
            Stat::make('Total de lecturas registradas', SensorReading::count())
                ->icon('heroicon-o-document-chart-bar')
                ->label('Lecturas')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('danger')
                ->chart([17, 23, 40, 30, 20, 8, 40, 30, 20, 35, 30, 10])
                ->url(route('filament.admin.resources.sensor-readings.index')),
            Stat::make('Usuarios registrados', User::count())
                ->icon('heroicon-o-users')
                ->label('Usuarios')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('primary')
                ->chart([17, 23, 40, 30, 20, 35, 40, 50, 40, 35, 30, 60])
                ->url(route('filament.admin.resources.users.index')),
            Stat::make('Total de alertas registradas', Alert::count())
                ->icon('heroicon-o-exclamation-circle')
                ->label('Alertas')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('warning')
                ->chart([27, 23, 10, 30, 20, 35, 40, 30, 20, 35, 30, 30])
                ->url(route('filament.admin.resources.alerts.index')),
        ];
    }
}
