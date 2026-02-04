<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\WaMessageParser;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public array $parsedData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure invoice number is set if somehow empty
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->generateInvoiceNumber();
        }
        return $data;
    }

    protected function generateInvoiceNumber(): string
    {
        $today = now();
        $prefix = 'INV-' . $today->format('dmy');
        $todayCount = Order::whereDate('created_at', $today->toDateString())->count();
        $sequence = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
        return $prefix . $sequence;
    }

    public function fillFromParsedData(array $parsedData): void
    {
        // Get current invoice
        $currentState = $this->form->getRawState();
        $invoiceNumber = !empty($currentState['invoice_number'])
            ? $currentState['invoice_number']
            : $this->generateInvoiceNumber();

        // Build data to fill
        $fillData = [
            'invoice_number' => $invoiceNumber,
            'customer_name' => $parsedData['customer_name'] ?? '',
            'customer_phone' => $parsedData['customer_phone'] ?? '',
            'whatsapp_number' => $parsedData['whatsapp_number'] ?? '',
            'receiver_name' => $parsedData['receiver_name'] ?? '',
            'receiver_phone' => $parsedData['receiver_phone'] ?? '',
            'receiver_address' => $parsedData['receiver_address'] ?? '',
            'notes' => $parsedData['notes'] ?? '',
            'total_price' => $parsedData['total_price'] ?? 0,
            'status' => 'pending',
            'items' => $parsedData['items'] ?? [],
        ];

        // Fill form using Filament's method
        $this->form->fill($fillData);

        // Force Livewire refresh
        $this->dispatch('$refresh');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('parseWa')
                ->label('📱 Parse WA')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info')
                ->form([
                    Textarea::make('wa_message')
                        ->label('Paste Chat WhatsApp')
                        ->placeholder('Paste pesan checkout dari WhatsApp di sini...')
                        ->rows(15)
                        ->required()
                        ->helperText('Contoh format: "Halo kak! Aku mau checkout..." dst'),
                ])
                ->action(function (array $data): void {
                    $parser = new WaMessageParser();
                    $result = $parser->parse($data['wa_message']);

                    // Fill form
                    $this->fillFromParsedData($result);

                    // Show notification
                    $itemCount = count($result['items']);
                    $notFoundItems = collect($result['items'])
                        ->filter(fn($item) => $item['itemable_id'] === null)
                        ->pluck('itemable_name')
                        ->toArray();

                    if (empty($notFoundItems)) {
                        Notification::make()
                            ->title('Berhasil Parse!')
                            ->body("Data customer dan {$itemCount} item berhasil diisi.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Parse Selesai (Ada Item Tidak Ditemukan)')
                            ->body("Item tidak ditemukan: " . implode(', ', $notFoundItems) . ". Silahkan pilih manual.")
                            ->warning()
                            ->send();
                    }
                })
                ->modalHeading('Parse Pesan WhatsApp')
                ->modalDescription('Paste pesan checkout dari customer untuk mengisi form secara otomatis.')
                ->modalSubmitActionLabel('Parse & Isi Form')
                ->modalWidth('lg'),
        ];
    }
}
