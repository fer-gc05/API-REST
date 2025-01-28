<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorReadingResource\Pages;
use App\Models\SensorReading;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SensorReadingResource extends Resource
{
    protected static ?string $model = SensorReading::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Dispositivos';

    /**
     * Define los atributos que se pueden buscar globalmente.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['device.name', 'temperature', 'humidity', 'smoke_level', 'gas_level'];
    }

    /**
     * Configura los detalles que se mostrarán en los resultados de la búsqueda global.
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Dispositivo' => $record->device->name,
            'Temperatura' => $record->temperature . ' °C',
            'Humedad' => $record->humidity . ' %',
            'Nivel de humo' => $record->smoke_level . ' %',
            'Nivel de gas' => $record->gas_level . ' ppm',
            'Fecha de registro' => $record->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('device_id')
                    ->relationship('device', 'name')
                    ->required()
                    ->label('Dispositivo'),
                Forms\Components\TextInput::make('temperature')
                    ->required()
                    ->numeric()
                    ->label('Temperatura (°C)'),
                Forms\Components\TextInput::make('humidity')
                    ->required()
                    ->numeric()
                    ->label('Humedad (%)'),
                Forms\Components\TextInput::make('smoke_level')
                    ->required()
                    ->numeric()
                    ->label('Nivel de humo (%)'),
                Forms\Components\TextInput::make('gas_level')
                    ->required()
                    ->numeric()
                    ->label('Nivel de gas (ppm)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.name')
                    ->label('Dispositivo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->label('Temperatura (°C)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Humedad (%)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('smoke_level')
                    ->label('Nivel de humo (%)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gas_level')
                    ->label('Nivel de gas (ppm)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSensorReadings::route('/'),
            'create' => Pages\CreateSensorReading::route('/create'),
            'edit' => Pages\EditSensorReading::route('/{record}/edit'),
        ];
    }
}
