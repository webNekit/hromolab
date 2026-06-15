<?php

declare(strict_types=1);

namespace App\Filament\LabPanel\Resources;

use App\Domains\Orders\Models\Order;
use App\Filament\LabPanel\Resources\OrderResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Лаборатория';

    protected static ?string $label = 'Заказы на анализ';

    protected static ?string $pluralLabel = 'Заказы на анализ';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('current_status', ['ready_for_lab', 'analyzing', 'completed'])
            ->with(['items.analysis', 'items.medicalResult', 'user.profile', 'laboratory']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Информация о заказе')
                ->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->label('Номер заказа')
                        ->disabled(),
                    Forms\Components\TextInput::make('user.email')
                        ->label('Email пациента')
                        ->disabled(),
                    Forms\Components\TextInput::make('user.profile.first_name')
                        ->label('Имя пациента')
                        ->disabled(),
                    Forms\Components\TextInput::make('user.profile.last_name')
                        ->label('Фамилия пациента')
                        ->disabled(),
                    Forms\Components\TextInput::make('laboratory.name')
                        ->label('Лаборатория')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('appointment_datetime')
                        ->label('Дата и время приёма')
                        ->disabled(),
                    Forms\Components\Select::make('current_status')
                        ->label('Текущий статус')
                        ->options([
                            'ready_for_lab' => 'Готов к лаборатории',
                            'analyzing' => 'Анализируется',
                            'completed' => 'Завершён',
                        ])
                        ->disabled(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('№ заказа')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.profile.full_name')
                    ->label('Пациент')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('laboratory.name')
                    ->label('Лаборатория')
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment_datetime')
                    ->label('Дата приёма')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ready_for_lab' => 'warning',
                        'analyzing' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ready_for_lab' => 'Готов к лаб.',
                        'analyzing' => 'Анализируется',
                        'completed' => 'Завершён',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Позиций')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('current_status')
                    ->label('Статус')
                    ->options([
                        'ready_for_lab' => 'Готов к лаборатории',
                        'analyzing' => 'Анализируется',
                        'completed' => 'Завершён',
                    ]),
                Tables\Filters\SelectFilter::make('laboratory')
                    ->relationship('laboratory', 'name')
                    ->label('Лаборатория'),
            ])
            ->recordActions([
                Actions\ViewAction::make()
                    ->label('Просмотр'),
            ])
            ->defaultSort('appointment_datetime', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
