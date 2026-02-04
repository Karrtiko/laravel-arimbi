<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Bundle;

class WaMessageParser
{
    /**
     * Parse WhatsApp checkout message
     * 
     * Expected format:
     * Halo kak! Aku mau checkout, ini detailnya ya:
     * - Paket Hemat (1x) = Rp 1.350.000
     * Total: Rp 1.350.000
     * Pesanan Atas nama: Kartiko
     * Nomor Telp: 081249895
     * Pengiriman ke:
     * Nama Penerima: Kartiko
     * Nomor Telp: 081249895
     * Alamat: Lengkap kok
     * Catatan: Tolong tulisin "Happy Birthday sayang"
     * Terima Kasih
     */
    public function parse(string $message): array
    {
        $result = [
            'customer_name' => null,
            'customer_phone' => null,
            'whatsapp_number' => null,
            'receiver_name' => null,
            'receiver_phone' => null,
            'receiver_address' => null,
            'notes' => null,
            'total_price' => 0,
            'items' => [],
        ];

        // Normalize line breaks
        $message = str_replace(["\r\n", "\r"], "\n", $message);
        $lines = explode("\n", $message);

        $inPengirimanSection = false;
        $pendingItemName = null; // For multi-line item parsing

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            // Detect "Pengiriman ke:" section
            if (stripos($line, 'Pengiriman ke') !== false) {
                $inPengirimanSection = true;
                continue;
            }

            // Parse items (Single line start with -)
            if (str_starts_with($line, '-')) {
                $item = $this->parseItem($line);
                if ($item) {
                    $result['items'][] = $item;
                }
                $pendingItemName = null;
                continue;
            }

            // Parse items (Multi-line legacy format: 1. Item Name)
            if (preg_match('/^\d+\.\s*(.+)/', $line, $matches)) {
                $pendingItemName = trim($matches[1]);
                continue;
            }

            // Parse details for Multi-line (2x @ Rp 100.000)
            if ($pendingItemName && preg_match('/^(\d+)x\s*@\s*Rp\s*([\d.,]+)/i', $line, $matches)) {
                $quantity = (int) $matches[1];
                $price = $this->parsePrice($matches[2]);
                $unitPrice = $quantity > 0 ? $price / $quantity : $price; // In old format price was unit price? 
                // Wait, old format: "2x @ Rp 100.000". Usually means 2x @ UnitPrice.
                // Or 2x @ TotalPrice?
                // Example: "2x @ Rp 120.000". If item is 60k, then 120k is total?
                // "2x @ Rp 60.000" usually means "2 at 60k each".
                // My old cart.js: item.price (unit price).
                // So matches[2] IS unit price.

                // Let's assume matches[2] is unit price roughly.
                // Wait, logic: "item.price * item.qty" was NOT used in old cart.js?
                // Old cart.js: `item.qty}x @ Rp ${item.price.toLocaleString` => Unit Price.
                // So matches[2] is UNIT PRICE.
                // In newly parsed structure `price_at_purchase` expects UNIT price.

                $itemable = $this->findItem($pendingItemName);

                $result['items'][] = [
                    'itemable_type' => $itemable['type'],
                    'itemable_id' => $itemable['id'],
                    'itemable_name' => $pendingItemName,
                    'quantity' => $quantity,
                    'price_at_purchase' => $price, // Assuming parsed price is unit price
                ];

                $pendingItemName = null;
                continue;
            }

            // Parse Total
            if (preg_match('/^Total:\s*Rp\s*([\d.,]+)/i', $line, $matches)) {
                $result['total_price'] = $this->parsePrice($matches[1]);
                continue;
            }

            // Parse customer info (before Pengiriman section)
            if (!$inPengirimanSection) {
                if (preg_match('/(Pesanan Atas nama|Nama):\s*(.+)/i', $line, $matches)) {
                    $result['customer_name'] = trim($matches[2]);
                    continue;
                }
                if (preg_match('/(Nomor Telp|No\.? HP):\s*(.+)/i', $line, $matches)) {
                    $phone = trim($matches[2]);
                    $result['customer_phone'] = $phone;
                    $result['whatsapp_number'] = $phone;
                    continue;
                }
            }

            // Parse receiver info (after Pengiriman section)
            if ($inPengirimanSection) {
                if (preg_match('/Nama Penerima:\s*(.+)/i', $line, $matches)) {
                    $result['receiver_name'] = trim($matches[1]);
                    continue;
                }
                if (preg_match('/(Nomor Telp|No\.? HP):\s*(.+)/i', $line, $matches)) {
                    $result['receiver_phone'] = trim($matches[2]);
                    continue;
                }
                if (preg_match('/Alamat( Lengkap)?:\s*(.+)/i', $line, $matches)) {
                    $result['receiver_address'] = trim($matches[2]);
                    continue;
                }
            }

            // Parse notes
            if (preg_match('/Catatan:\s*(.+)/i', $line, $matches)) {
                $result['notes'] = trim($matches[1]);
                continue;
            }
        }

        return $result;
    }

    protected function parseItem(string $line): ?array
    {
        // Remove leading dash
        $line = trim(ltrim($line, '-'));

        // Try to match: Product Name (Qtyх) = Rp Price (Total)
        // Regex handles names with parentheses by non-greedy matching until a number pattern
        if (preg_match('/^(.+?)\s*\((\d+)x?\)\s*=\s*Rp\s*([\d.,]+)/i', $line, $matches)) {
            $productName = trim($matches[1]);
            $quantity = (int) $matches[2];
            $price = $this->parsePrice($matches[3]); // This is TOTAL price in new format (qty * unit)

            // Unit price = total price / quantity
            $unitPrice = $quantity > 0 ? $price / $quantity : $price;

            $itemable = $this->findItem($productName);

            return [
                'itemable_type' => $itemable['type'],
                'itemable_id' => $itemable['id'],
                'itemable_name' => $productName,
                'quantity' => $quantity,
                'price_at_purchase' => $unitPrice,
            ];
        }

        return null;
    }

    /**
     * Find product or bundle by name (fuzzy search)
     */
    protected function findItem(string $name): array
    {
        $name = strtolower(trim($name));

        // Try exact match first - Product
        $product = Product::whereRaw('LOWER(name) = ?', [$name])->first();
        if ($product) {
            return ['type' => Product::class, 'id' => $product->id];
        }

        // Try exact match - Bundle
        $bundle = Bundle::whereRaw('LOWER(name) = ?', [$name])->first();
        if ($bundle) {
            return ['type' => Bundle::class, 'id' => $bundle->id];
        }

        // Try partial match - Product
        $product = Product::whereRaw('LOWER(name) LIKE ?', ["%{$name}%"])->first();
        if ($product) {
            return ['type' => Product::class, 'id' => $product->id];
        }

        // Try partial match - Bundle
        $bundle = Bundle::whereRaw('LOWER(name) LIKE ?', ["%{$name}%"])->first();
        if ($bundle) {
            return ['type' => Bundle::class, 'id' => $bundle->id];
        }

        // Default to Product with null ID (user will need to select manually)
        return ['type' => Product::class, 'id' => null];
    }

    protected function parsePrice(string $price): float
    {
        // Remove dots (thousand separator) and replace comma with dot
        $price = str_replace('.', '', $price);
        $price = str_replace(',', '.', $price);
        return (float) $price;
    }
}
