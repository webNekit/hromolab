<?php

declare(strict_types=1);

namespace App\Filament\LabPanel\Resources\MedicalResultResource\Pages;

use App\Filament\LabPanel\Resources\MedicalResultResource;
use Filament\Resources\Pages\EditRecord;

class EditMedicalResult extends EditRecord
{
    protected static string $resource = MedicalResultResource::class;
}
