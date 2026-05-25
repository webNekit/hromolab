<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Catalog\Models\Analysis;
use App\Filament\Resources\AnalysisResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AnalysisResource extends Resource
{
    protected static ?string $model = Analysis::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Каталог';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основная информация')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Название')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sku')
                        ->label('Артикул (SKU)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Select::make('category_id')
                        ->label('Категория')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable(),
                ])->columns(2),
            Section::make('Детали')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Описание')
                        ->rows(4)
                        ->nullable(),
                    Forms\Components\Textarea::make('preparation')
                        ->label('Подготовка к анализу')
                        ->rows(4)
                        ->nullable(),
                    Forms\Components\TextInput::make('biomaterial')
                        ->label('Биоматериал')
                        ->required()
                        ->maxLength(255),
                ])->columns(1),
            Section::make('Цена и сроки')
                ->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Цена (₽)')
                        ->required()
                        ->numeric()
                        ->prefix('₽')
                        ->minValue(0),
                    Forms\Components\TextInput::make('lead_time_days')
                        ->label('Срок выполнения (дней)')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(30),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Активен')
                        ->default(true),
                    Forms\Components\Toggle::make('is_popular')
                        ->label('Популярный')
                        ->default(false),
                ])->columns(2),
            Section::make('Референсные значения (JSON)')
                ->schema([
                    Forms\Components\Textarea::make('reference_ranges')
                        ->label('JSON референсов')
                        ->rows(6)
                        ->nullable()
                        ->hint('Формат: {"param": {"min": 0, "max": 100, "unit": "g/L"}}'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Категория')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lead_time_days')
                    ->label('Срок')
                    ->suffix(' дн.')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Популярный')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_popular'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAnalyses::route('/'),
            'create' => Pages\CreateAnalysis::route('/create'),
            'edit' => Pages\EditAnalysis::route('/{record}/edit'),
        ];
    }
}
