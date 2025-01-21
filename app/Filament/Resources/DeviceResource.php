<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';

    protected static ?string $navigationGroup = 'Dispositivos';

    /**
     * Define los atributos que se pueden buscar globalmente.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'location', 'token', 'status'];
    }

    /**
     * Configura los detalles que se mostrarán en los resultados de la búsqueda global.
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nombre del dispositivo' => $record->name,
            'Ubicación' => $record->location,
            'Token' => $record->token,
            'Estado' => $record->status,
            'Fecha de registro' => $record->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre del dispositivo'),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->label('Ubicación'),
                Forms\Components\TextInput::make('token')
                    ->default(fn() => bin2hex(random_bytes(32)))
                    ->required()
                    ->maxLength(255)
                    ->label('Token'),
                Forms\Components\Select::make('status')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
                    ])
                    ->required()
                    ->default('Inactivo')
                    ->label('Estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del dispositivo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
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
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
