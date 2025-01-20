<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorReadingResource\Pages;
use App\Filament\Resources\SensorReadingResource\RelationManagers;
use App\Models\SensorReading;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SensorReadingResource extends Resource
{
    protected static ?string $model = SensorReading::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('device_id')
                    ->relationship('device', 'name')
                    ->required(),
                Forms\Components\TextInput::make('temperature')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('humidity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('smoke_level')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('gas_level')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('smoke_level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gas_level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
        return [
            //
        ];
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
