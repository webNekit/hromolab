<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Laboratories\Models\Laboratory;
use App\Filament\Resources\LaboratoryResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LaboratoryResource extends Resource
{
    protected static ?string $model = Laboratory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Лаборатории';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основная информация')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Название')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->label('Адрес')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Телефон')
                        ->tel()
                        ->nullable(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->nullable(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Активна')
                        ->default(true),
                ])->columns(2),
            Section::make('Координаты')
                ->schema([
                    Forms\Components\TextInput::make('latitude')
                        ->label('Широта')
                        ->numeric()
                        ->nullable(),
                    Forms\Components\TextInput::make('longitude')
                        ->label('Долгота')
                        ->numeric()
                        ->nullable(),
                ])->columns(2),
            Section::make('Рабочие часы')
                ->relationship('workingHours')
                ->schema([
                    Forms\Components\Select::make('day_of_week')
                        ->label('День недели')
                        ->options([
                            0 => 'Воскресенье',
                            1 => 'Понедельник',
                            2 => 'Вторник',
                            3 => 'Среда',
                            4 => 'Четверг',
                            5 => 'Пятница',
                            6 => 'Суббота',
                        ])
                        ->required(),
                    Forms\Components\TimePicker::make('open_time')
                        ->label('Открытие')
                        ->required(),
                    Forms\Components\TimePicker::make('close_time')
                        ->label('Закрытие')
                        ->required(),
                    Forms\Components\TextInput::make('slot_interval_minutes')
                        ->label('Интервал слотов (мин)')
                        ->numeric()
                        ->default(15),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Заказов')
                    ->counts('orders'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLaboratories::route('/'),
            'create' => Pages\CreateLaboratory::route('/create'),
            'edit' => Pages\EditLaboratory::route('/{record}/edit'),
        ];
    }
}
