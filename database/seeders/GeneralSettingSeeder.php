<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Contact
            [
                'key' => 'whatsapp_number',
                'value' => '6281234567890',
                'label' => 'Nomor WhatsApp Admin',
                'type' => 'text',
                'group' => 'contact',
                'sort_order' => 1,
            ],
            [
                'key' => 'whatsapp_message',
                'value' => 'Halo kak! Saya mau tanya tentang produk di ArimbiStore',
                'label' => 'Pesan Default Chat Admin',
                'type' => 'textarea',
                'group' => 'contact',
                'sort_order' => 2,
            ],

            // Stock Settings
            [
                'key' => 'low_stock_threshold',
                'value' => '5',
                'label' => 'Low Stock Threshold',
                'type' => 'number',
                'group' => 'stock',
                'sort_order' => 10,
            ],

            // Display Settings
            [
                'key' => 'products_per_page',
                'value' => '12',
                'label' => 'Produk per Halaman (Katalog)',
                'type' => 'number',
                'group' => 'display',
                'sort_order' => 20,
            ],
            [
                'key' => 'products_home_count',
                'value' => '8',
                'label' => 'Produk per Kategori (Home)',
                'type' => 'number',
                'group' => 'display',
                'sort_order' => 21,
            ],
            [
                'key' => 'bundles_home_count',
                'value' => '3',
                'label' => 'Jumlah Paket di Home',
                'type' => 'number',
                'group' => 'display',
                'sort_order' => 22,
            ],
            [
                'key' => 'related_products_count',
                'value' => '4',
                'label' => 'Jumlah Produk Terkait',
                'type' => 'number',
                'group' => 'display',
                'sort_order' => 23,
            ],
            // Home Content
            [
                'key' => 'home_hero_title',
                'value' => 'Jajanan yang Kamu Kangenin, Skincare yang Kamu Butuhin.',
                'label' => 'Judul Hero Home',
                'type' => 'text',
                'group' => 'home_content',
                'sort_order' => 30,
            ],
            [
                'key' => 'home_hero_subtitle',
                'value' => 'Pengen snack enak-enak hits dari luar negeri nggak perlu tunggu temen, apalagi jastip. Plus, ada koleksi skincare pilihan buat kulit harian kamu. Semua ready, tinggal angkut!',
                'label' => 'Subjudul Hero Home',
                'type' => 'textarea',
                'group' => 'home_content',
                'sort_order' => 31,
            ],
            [
                'key' => 'home_hero_image',
                'value' => 'settings/home_hero.png',
                'label' => 'Gambar Hero Home',
                'type' => 'image',
                'group' => 'home_content',
                'sort_order' => 32,
            ],

            // About Content
            [
                'key' => 'about_hero_image',
                'value' => 'settings/about_hero.png',
                'label' => 'Gambar Hero About',
                'type' => 'image',
                'group' => 'about_content',
                'sort_order' => 40,
            ],
        ];

        foreach ($settings as $setting) {
            GeneralSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
