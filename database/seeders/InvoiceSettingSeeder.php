<?php

namespace Database\Seeders;

use App\Models\InvoiceSetting;
use Illuminate\Database\Seeder;

class InvoiceSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Store Info
            ['key' => 'store_name', 'value' => 'Arimbi Store', 'label' => 'Nama Toko', 'type' => 'text', 'sort_order' => 1],
            ['key' => 'store_tagline', 'value' => 'Skincare & Snacks Terpercaya', 'label' => 'Tagline Toko', 'type' => 'text', 'sort_order' => 2],
            ['key' => 'store_address', 'value' => 'Jl. Contoh No. 123, Jakarta', 'label' => 'Alamat Toko', 'type' => 'textarea', 'sort_order' => 3],
            ['key' => 'store_phone', 'value' => '081234567890', 'label' => 'No. Telepon Toko', 'type' => 'text', 'sort_order' => 4],
            ['key' => 'store_email', 'value' => 'hello@arimbistore.com', 'label' => 'Email Toko', 'type' => 'text', 'sort_order' => 5],

            // Invoice Text
            ['key' => 'invoice_title', 'value' => 'INVOICE', 'label' => 'Judul Invoice', 'type' => 'text', 'sort_order' => 10],
            ['key' => 'invoice_footer', 'value' => 'Terima kasih telah berbelanja di Arimbi Store! ✨', 'label' => 'Footer Invoice', 'type' => 'textarea', 'sort_order' => 11],
            ['key' => 'invoice_note', 'value' => 'Barang yang sudah dibeli tidak dapat dikembalikan kecuali cacat produksi.', 'label' => 'Catatan Invoice', 'type' => 'textarea', 'sort_order' => 12],

            // Payment Info
            ['key' => 'bank_name', 'value' => 'BCA', 'label' => 'Nama Bank', 'type' => 'text', 'sort_order' => 20],
            ['key' => 'bank_account', 'value' => '1234567890', 'label' => 'Nomor Rekening', 'type' => 'text', 'sort_order' => 21],
            ['key' => 'bank_holder', 'value' => 'Arimbi Store', 'label' => 'Atas Nama Rekening', 'type' => 'text', 'sort_order' => 22],

            // Shipping Label
            ['key' => 'shipping_sender_name', 'value' => 'Arimbi Store', 'label' => 'Nama Pengirim (Label)', 'type' => 'text', 'sort_order' => 30],
            ['key' => 'shipping_sender_address', 'value' => 'Jl. Contoh No. 123, Jakarta', 'label' => 'Alamat Pengirim (Label)', 'type' => 'textarea', 'sort_order' => 31],
            ['key' => 'shipping_sender_phone', 'value' => '081234567890', 'label' => 'Telp Pengirim (Label)', 'type' => 'text', 'sort_order' => 32],
            ['key' => 'package_content', 'value' => 'Produk Kecantikan / Makanan', 'label' => 'Isi Paket (Label)', 'type' => 'text', 'sort_order' => 33],

            // Colors
            ['key' => 'primary_color', 'value' => '#2563eb', 'label' => 'Warna Utama', 'type' => 'color', 'sort_order' => 40],
            ['key' => 'secondary_color', 'value' => '#1e40af', 'label' => 'Warna Sekunder', 'type' => 'color', 'sort_order' => 41],
        ];

        foreach ($settings as $setting) {
            InvoiceSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
