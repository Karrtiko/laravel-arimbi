<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\GeneralSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditGeneralSetting extends EditRecord
{
    protected static string $resource = GeneralSettingResource::class;
}
