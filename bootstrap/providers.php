<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\LabPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    LabPanelProvider::class,
];
