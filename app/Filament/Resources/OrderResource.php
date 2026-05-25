<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Orders\Models\Order;
use App\Filament\Resources\OrderResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Заказы';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Информация о заказе')
                ->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->label('Номер заказа')
                        ->disabled()
                        ->dehydrated(),
                    Forms\Components\Select::make('user_id')
                        ->label('Пациент')
                        ->relationship('user', 'name')
                        ->nullable()
                        ->searchable(),
                    Forms\Components\Select::make('laboratory_id')
                        ->label('Лаборатория')
                        ->relationship('laboratory', 'name')
                        ->required()
                        ->searchable(),
                    Forms\Components\DateTimePicker::make('appointment_datetime')
                        ->label('Дата и время записи')
                        ->required(),
                ])->columns(2),
            Section::make('Финансы и статус')
                ->schema([
                    Forms\Components\TextInput::make('total_price')
                        ->label('Сумма')
                        ->numeric()
                        ->prefix('₽'),
                    Forms\Components\Select::make('payment_status')
                        ->label('Статус оплаты')
                        ->options([
                            'pending' => 'Ожидает',
                            'paid' => 'Оплачен',
                            'refunded' => 'Возврат',
                        ])
                        ->default('pending'),
                    Forms\Components\Select::make('current_status')
                        ->label('Статус заказа')
                        ->options([
                            'new' => 'Новый',
                            'processing' => 'В обработке',
                            'ready_for_lab' => 'Готов к лаборатории',
                            'analyzing' => 'Анализируется',
                            'completed' => 'Завершён',
                            'cancelled' => 'Отменён',
                        ])
                        ->default('new'),
                    Forms\Components\TextInput::make('payment_id')
                        ->label('ID платежа')
                        ->nullable(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Номер')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пациент')
                    ->placeholder('Гость')
                    ->searchable(),
                Tables\Columns\TextColumn::make('laboratory.name')
                    ->label('Лаборатория')
                    ->searchable(),
                Tables\Columns\TextColumn::make('appointment_datetime')
                    ->label('Дата записи')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Сумма')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Оплата')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'paid' => 'Оплачен',
                        'refunded' => 'Возврат',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('current_status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Новый',
                        'processing' => 'В обработке',
                        'ready_for_lab' => 'Готов к лаборатории',
                        'analyzing' => 'Анализируется',
                        'completed' => 'Завершён',
                        'cancelled' => 'Отменён',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'ready_for_lab' => 'warning',
                        'analyzing' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('current_status')
                    ->options([
                        'new' => 'Новый',
                        'processing' => 'В обработке',
                        'ready_for_lab' => 'Готов к лаборатории',
                        'analyzing' => 'Анализируется',
                        'completed' => 'Завершён',
                        'cancelled' => 'Отменён',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Ожидает',
                        'paid' => 'Оплачен',
                        'refunded' => 'Возврат',
                    ]),
                Tables\Filters\Filter::make('appointment_datetime')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('С даты'),
                        Forms\Components\DatePicker::make('until')->label('По дату'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when($data['from'], fn ($q) => $q->whereDate('appointment_datetime', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('appointment_datetime', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
