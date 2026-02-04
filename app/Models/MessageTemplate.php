<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_key',
        'status_label',
        'template',
        'description',
    ];

    /**
     * Get template by status key
     */
    public static function getByStatus(string $statusKey): ?self
    {
        return static::where('status_key', $statusKey)->first();
    }

    /**
     * Parse template with order data
     * Available placeholders:
     * - [Nama] = customer_name
     * - [NomorOrder] = invoice_number  
     * - [Total] = total_price (formatted)
     * - [NomorResi] = tracking_number
     * - [ListBarang] = items list
     */
    public function parseForOrder(Order $order): string
    {
        $message = $this->template;

        // Build items list
        $itemsList = '';
        foreach ($order->items as $item) {
            $itemName = $item->itemable?->name ?? 'Unknown Item';
            $qty = $item->quantity;
            $price = number_format($item->price_at_purchase, 0, ',', '.');
            $itemsList .= "• {$itemName} x{$qty} - Rp {$price}\n";
        }

        // Replace placeholders
        $replacements = [
            '[Nama]' => $order->customer_name,
            '[NomorOrder]' => $order->invoice_number,
            '[Total]' => 'Rp ' . number_format((float) $order->total_price, 0, ',', '.'),
            '[NomorResi]' => $order->tracking_number ?? '-',
            '[ListBarang]' => trim($itemsList) ?: '-',
        ];

        foreach ($replacements as $placeholder => $value) {
            $message = str_replace($placeholder, $value, $message);
        }

        return $message;
    }
}
