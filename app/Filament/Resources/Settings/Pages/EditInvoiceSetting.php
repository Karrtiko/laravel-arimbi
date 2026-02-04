<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\InvoiceSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceSetting extends EditRecord
{
    protected static string $resource = InvoiceSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
