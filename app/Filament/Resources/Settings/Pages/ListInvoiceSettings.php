<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\InvoiceSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceSettings extends ListRecords
{
    protected static string $resource = InvoiceSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
