<?php

declare(strict_types=1);

namespace App\Filament\LabPanel\Resources\MedicalResultResource\Pages;

use App\Filament\LabPanel\Resources\MedicalResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicalResults extends ListRecords
{
    protected static string $resource = MedicalResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
