<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\GeneralSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListGeneralSettings extends ListRecords
{
    protected static string $resource = GeneralSettingResource::class;
}
