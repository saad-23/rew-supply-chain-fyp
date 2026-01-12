<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Steel Rods (Grade 60)', 'sku' => 'ST-60-001', 'price' => 2500.00],
            ['name' => 'Steel Rods (Grade 40)', 'sku' => 'ST-40-002', 'price' => 2200.00],
            ['name' => 'PVC Pipe 4-inch (Per ft)', 'sku' => 'PVC-04-001', 'price' => 450.00],
            ['name' => 'PVC Pipe 6-inch (Per ft)', 'sku' => 'PVC-06-002', 'price' => 750.00],
            ['name' => 'Cement Bag (Ordinary)', 'sku' => 'CMT-ORD-001', 'price' => 1250.00],
            ['name' => 'Cement Bag (Sulphate Res)', 'sku' => 'CMT-SRC-002', 'price' => 1400.00],
            ['name' => 'Red Bricks (Per 1000)', 'sku' => 'BRK-RED-001', 'price' => 15000.00],
            ['name' => 'Sand (Ravi) - Per Truck', 'sku' => 'SND-RVI-001', 'price' => 8000.00],
            ['name' => 'Crush (Margalla) - Per ft', 'sku' => 'CRSH-MRG-001', 'price' => 120.00],
            ['name' => 'Angle Iron 2x2', 'sku' => 'STL-ANG-001', 'price' => 3000.00],
            ['name' => 'Welding Electrodes (Pkt)', 'sku' => 'WLD-ELC-001', 'price' => 850.00],
            ['name' => 'Industrial Valve 2"', 'sku' => 'VLV-IND-002', 'price' => 4500.00],
            ['name' => 'Ball Bearing 6204', 'sku' => 'BRG-6204', 'price' => 350.00],
            ['name' => 'Nut Bolt 4mm (kg)', 'sku' => 'NUT-04-KG', 'price' => 600.00],
            ['name' => 'Paint Drum (White)', 'sku' => 'PNT-WHT-001', 'price' => 8500.00],
            ['name' => 'Safety Helmets (Yellow)', 'sku' => 'SFT-HLM-001', 'price' => 450.00],
            ['name' => 'Safety Gloves (Pair)', 'sku' => 'SFT-GLV-001', 'price' => 150.00],
            ['name' => 'Grinding Disc 4"', 'sku' => 'DSC-GRD-004', 'price' => 120.00],
            ['name' => 'Cutting Disc 14"', 'sku' => 'DSC-CUT-014', 'price' => 850.00],
            ['name' => 'Measuring Tape 50m', 'sku' => 'TOOL-MT-050', 'price' => 1200.00],
            ['name' => 'Hammer Drill Machine', 'sku' => 'TOOL-DRL-001', 'price' => 12500.00],
            ['name' => 'Generator 5kVA', 'sku' => 'GEN-005-KVA', 'price' => 145000.00],
            ['name' => 'Electrical Cable 7/29 (Coil)', 'sku' => 'ELC-CBL-729', 'price' => 9500.00],
            ['name' => 'Switch Board (8 Gang)', 'sku' => 'ELC-SWB-008', 'price' => 450.00],
            ['name' => 'LED Flood Light 100W', 'sku' => 'LGT-FLD-100', 'price' => 3500.00],
            ['name' => 'Copper Wire (kg)', 'sku' => 'MTL-CPR-KG', 'price' => 2800.00],
            ['name' => 'Aluminum Sheet 4x8', 'sku' => 'MTL-ALM-048', 'price' => 18000.00],
            ['name' => 'Rubber Gasket Sheet', 'sku' => 'RUB-GSK-001', 'price' => 1200.00],
            ['name' => 'Hydraulic Oil (Drum)', 'sku' => 'OIL-HYD-001', 'price' => 65000.00],
            ['name' => 'Grease Bucket (10kg)', 'sku' => 'LUB-GRS-010', 'price' => 8000.00],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'current_stock' => rand(5, 500), // Random stock between 5 and 500
                'price' => $product['price'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}