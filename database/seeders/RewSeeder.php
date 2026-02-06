<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RewSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        $settings = [
            'site_name' => 'Rehman Engineering Works',
            'currency' => 'PKR',
            'low_stock_threshold' => 10,
            'factory_address' => 'Industrial Estate, Gujranwala',
            'contact_email' => 'info@rew.com.pk'
        ];
        
        foreach ($settings as $key => $val) {
            Setting::firstOrCreate(['key' => $key], ['value' => $val]);
        }

        // 2. Categories
        $categories = [
            ['name' => 'Home Appliances', 'color' => 'blue', 'desc' => 'Geysers, Kitchen equipment'],
            ['name' => 'Auto Parts', 'color' => 'red', 'desc' => 'Silencers, Exhaust systems'],
            ['name' => 'Power Equipment', 'color' => 'yellow', 'desc' => 'Generators, Turbines'],
            ['name' => 'Raw Material', 'color' => 'gray', 'desc' => 'Steel sheets, Pipes']
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'color' => $cat['color'],
                    'description' => $cat['desc']
                ]
            );
        }

        // 3. Products (REW Specific)
        $products = [
            [
                'name' => 'Gas Geyser 35 Gallon',
                'cat' => 'Home Appliances',
                'sku' => 'GEY-35G-001',
                'price' => 25000,
                'stock' => 15
            ],
            [
                'name' => 'Gas Geyser 55 Gallon (Heavy)',
                'cat' => 'Home Appliances',
                'sku' => 'GEY-55G-HD',
                'price' => 32000,
                'stock' => 8
            ],
            [
                'name' => 'Electric Geyser Instant',
                'cat' => 'Home Appliances',
                'sku' => 'GEY-ELE-INST',
                'price' => 18000,
                'stock' => 45
            ],
            [
                'name' => 'Honda Civic Reborn Silencer',
                'cat' => 'Auto Parts',
                'sku' => 'SIL-CV-REB',
                'price' => 12500,
                'stock' => 20
            ],
            [
                'name' => 'Suzuki Alto Euro II Silencer',
                'cat' => 'Auto Parts',
                'sku' => 'SIL-ALT-EU2',
                'price' => 8500,
                'stock' => 50
            ],
            [
                'name' => 'Toyota Corolla GLI Exhaust',
                'cat' => 'Auto Parts',
                'sku' => 'EXH-COR-GLI',
                'price' => 15000,
                'stock' => 5
            ],
            [
                'name' => '5kVA Petrol Generator',
                'cat' => 'Power Equipment',
                'sku' => 'GEN-5KVA-PET',
                'price' => 125000,
                'stock' => 3
            ],
            [
                'name' => '10kVA Diesel Generator',
                'cat' => 'Power Equipment',
                'sku' => 'GEN-10KVA-DSL',
                'price' => 350000,
                'stock' => 1
            ]
        ];

        foreach ($products as $p) {
            $cat = Category::where('name', $p['cat'])->first();
            Product::updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'name' => $p['name'],
                    'category_id' => $cat->id,
                    'price' => $p['price'],
                    'current_stock' => $p['stock']
                ]
            );
        }
    }
}
