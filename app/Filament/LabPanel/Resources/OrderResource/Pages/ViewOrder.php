<?php

declare(strict_types=1);

namespace App\Filament\LabPanel\Resources\OrderResource\Pages;

use App\Filament\LabPanel\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('startAnalyzing')
                ->label('Начать анализ')
                ->color('info')
                ->icon('heroicon-o-play')
                ->action(function (): void {
                    $this->record->updateStatus('analyzing', auth()->user(), 'Начат лабораторный анализ');
                    $this->refreshFormData(['current_status']);
                })
                ->visible(fn (): bool => $this->record->current_status === 'ready_for_lab'),
        ];
    }
}
