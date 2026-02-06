<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\DemandForecast;
use Carbon\Carbon;
use App\Models\User;

class AnomalySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Anomaly Data (High Demand Spike)
        $product = Product::first();
        
        if ($product) {
            // Normal demand
            for ($i = 0; $i < 5; $i++) {
                DemandForecast::create([
                    'product_id' => $product->id,
                    'forecast_date' => Carbon::now()->addDays($i),
                    'predicted_quantity' => rand(10, 20),
                    'confidence_score' => 0.95
                ]);
            }

            // ANOMALY: Massive spike
            DemandForecast::create([
                'product_id' => $product->id,
                'forecast_date' => Carbon::now()->addDays(5),
                'predicted_quantity' => 150, // Spike!
                'confidence_score' => 0.40 // Low confidence
            ]);
            
            // 2. Create Notifications
            $user = User::first();
            if ($user) {
                // Insert raw notification to save creating a class for this one-off
                DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\Notifications\SystemAlert',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'title' => 'Anomaly Detected',
                        'message' => 'Unusual demand spike detected for ' . $product->name,
                        'level' => 'critical'
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                 DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\Notifications\LowStockAlert',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'title' => 'Low Stock Warning',
                        'message' => 'Inventory levels for Generator X500 are critical (3 units left).',
                        'level' => 'warning'
                    ]),
                    'read_at' => null,
                    'created_at' => now()->subMinutes(10),
                    'updated_at' => now()->subMinutes(10),
                ]);
            }
        }
    }
}
