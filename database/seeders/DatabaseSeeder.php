<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Arimbi',
            'email' => 'admin@arimbi.store',
            'password' => bcrypt('admin'),
            'email_verified_at' => now(),
        ]);

        // Create sample categories
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik'],
            ['name' => 'Fashion', 'slug' => 'fashion'],
            ['name' => 'Makanan', 'slug' => 'makanan'],
            ['name' => 'Minuman', 'slug' => 'minuman'],
            ['name' => 'Kecantikan', 'slug' => 'kecantikan'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample countries
        $countries = [
            ['name' => 'Indonesia', 'slug' => 'indonesia', 'emoji' => '🇮🇩'],
            ['name' => 'Jepang', 'slug' => 'jepang', 'emoji' => '🇯🇵'],
            ['name' => 'Korea Selatan', 'slug' => 'korea-selatan', 'emoji' => '🇰🇷'],
            ['name' => 'China', 'slug' => 'china', 'emoji' => '🇨🇳'],
            ['name' => 'Amerika Serikat', 'slug' => 'amerika-serikat', 'emoji' => '🇺🇸'],
            ['name' => 'Thailand', 'slug' => 'thailand', 'emoji' => '🇹🇭'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }

        // Call other seeders
        $this->call([
            GeneralSettingSeeder::class,
            InvoiceSettingSeeder::class,
            MessageTemplateSeeder::class,
        ]);
    }
}
