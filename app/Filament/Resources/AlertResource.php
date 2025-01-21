<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertResource\Pages;
use App\Models\Alert;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Alertas';

    protected static ?string $recordTitleAttribute = 'device.name';

    /**
     * Define los atributos que se pueden buscar globalmente.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['device.name', 'type', 'status', 'value', 'max_value'];
    }

    /**
     * Configura los detalles que se mostrarán en los resultados de la búsqueda global.
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Dispositivo' => $record->device->name ?? 'N/A',
            'Tipo de Alerta' => $record->type,
            'Estado' => $record->status,
            'Valor Actual' => $record->value,
            'Máximo Permitido' => $record->max_value,
        ];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('device_id')
                    ->relationship('device', 'name')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'Temperatura' => 'Temperatura',
                        'Humedad' => 'Humedad',
                        'Nivel de humo' => 'Nivel de humo',
                        'Nivel de gas' => 'Nivel de gas',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('max_value')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.name')
                    ->label('Dispositivo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_value')
                    ->label('Máximo Permitido')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlerts::route('/'),
            'create' => Pages\CreateAlert::route('/create'),
            'edit' => Pages\EditAlert::route('/{record}/edit'),
        ];
    }
}
