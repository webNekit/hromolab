<?php

declare(strict_types=1);

namespace App\Filament\LabPanel\Resources;

use App\Domains\Results\Models\MedicalResult;
use App\Filament\LabPanel\Resources\MedicalResultResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicalResultResource extends Resource
{
    protected static ?string $model = MedicalResult::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Лаборатория';

    protected static ?string $label = 'Результат анализа';

    protected static ?string $pluralLabel = 'Результаты анализов';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['orderItem.analysis.category', 'orderItem.order.user.profile', 'orderItem.order.laboratory', 'labAssistant']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Информация о пациенте и заказе')
                ->schema([
                    Forms\Components\Placeholder::make('order_number')
                        ->label('Номер заказа')
                        ->content(fn (?MedicalResult $record): string => $record?->orderItem->order->order_number ?? ''),
                    Forms\Components\Placeholder::make('patient_name')
                        ->label('Пациент')
                        ->content(function (?MedicalResult $record): string {
                            $profile = $record?->orderItem->order->user->profile;

                            if ($profile === null) {
                                return '—';
                            }

                            return trim("{$profile->last_name} {$profile->first_name} {$profile->middle_name}");
                        }),
                    Forms\Components\Placeholder::make('analysis_name')
                        ->label('Анализ')
                        ->content(fn (?MedicalResult $record): string => $record?->orderItem->analysis->name ?? ''),
                    Forms\Components\Placeholder::make('biomaterial')
                        ->label('Биоматериал')
                        ->content(fn (?MedicalResult $record): string => $record?->orderItem->analysis->biomaterial ?? ''),
                    Forms\Components\Placeholder::make('laboratory')
                        ->label('Лаборатория')
                        ->content(fn (?MedicalResult $record): string => $record?->orderItem->order->laboratory->name ?? ''),
                ])->columns(2),

            Section::make('Ввод результатов')
                ->schema([
                    Forms\Components\Placeholder::make('reference_info')
                        ->label('Референсные значения')
                        ->content(function (?MedicalResult $record): string {
                            if ($record === null || $record->orderItem === null) {
                                return 'Нет данных';
                            }

                            $ranges = $record->orderItem->analysis->reference_ranges ?? [];

                            if (empty($ranges)) {
                                return 'Референсные значения не заданы для данного анализа';
                            }

                            $lines = [];

                            foreach ($ranges as $param => $range) {
                                $min = $range['min'] ?? '—';
                                $max = $range['max'] ?? '—';
                                $unit = $range['unit'] ?? '';
                                $lines[] = "• {$param}: {$min} – {$max} {$unit}";
                            }

                            return implode("\n", $lines);
                        }),
                    Forms\Components\KeyValue::make('parameter_values')
                        ->label('Показатели')
                        ->keyLabel('Параметр')
                        ->valueLabel('Значение')
                        ->addActionLabel('Добавить показатель')
                        ->hint('Введите {value}, {unit}, {reference}')
                        ->helperText('Формат значения: {"value": 145, "unit": "g/L", "reference": "120-160"}')
                        ->disabled(fn (?MedicalResult $record): bool => $record?->isVerified() ?? false)
                        ->visible(fn (Get $get): bool => ! $get('use_dynamic_form')),
                    Forms\Components\Toggle::make('use_dynamic_form')
                        ->label('Использовать динамическую форму')
                        ->helperText('Форма с полями по референсным значениям')
                        ->live()
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orderItem.order.order_number')
                    ->label('№ заказа')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('orderItem.analysis.name')
                    ->label('Анализ')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('orderItem.order.user.profile.full_name')
                    ->label('Пациент')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('orderItem.order.laboratory.name')
                    ->label('Лаборатория'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Верифицирован')
                    ->state(fn (MedicalResult $record): bool => $record->isVerified())
                    ->boolean(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Проверен')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('labAssistant.name')
                    ->label('Лаборант'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('verified')
                    ->label('Верифицирован')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('verified_at'),
                        false: fn (Builder $query): Builder => $query->whereNull('verified_at'),
                    ),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->label('Ввод результатов'),
                Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (MedicalResult $record): bool => $record->pdf_path !== null)
                    ->action(function (MedicalResult $record): void {
                        $url = $record->temporaryDownloadUrl;

                        if ($url !== null) {
                            redirect()->to($url);
                        }
                    }),
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
            'index' => Pages\ListMedicalResults::route('/'),
            'edit' => Pages\EditMedicalResult::route('/{record}/edit'),
        ];
    }
}
