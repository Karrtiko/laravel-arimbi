<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\MessageTemplateResource;
use Filament\Resources\Pages\EditRecord;

class EditMessageTemplate extends EditRecord
{
    protected static string $resource = MessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
