<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesDataSeeder extends Seeder
{
    /**
     * Generate realistic sales data for ML forecasting
     * Creates 90 days of historical sales with patterns
     */
    public function run(): void
    {
        $this->command->info('Generating sales data for ML forecasting...');
        
        // Clear existing sales
        Sale::truncate();
        
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->error('No products found! Run RewSeeder first.');
            return;
        }
        
        $startDate = Carbon::now()->subDays(90);
        $endDate = Carbon::now()->subDays(1);
        
        $totalSales = 0;
        
        foreach ($products as $product) {
            // Determine base sales pattern based on product category
            $baseDemand = $this->getBaseDemand($product);
            $variance = $baseDemand * 0.3; // 30% variance
            
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Skip some days randomly (not all products sell daily)
                if (rand(1, 100) > 70) { // 70% chance of sale
                    $currentDate->addDay();
                    continue;
                }
                
                // Calculate quantity with patterns
                $quantity = $this->calculateQuantity(
                    $baseDemand,
                    $variance,
                    $currentDate,
                    $product
                );
                
                if ($quantity > 0) {
                    Sale::create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'total_amount' => $quantity * $product->price,
                        'sale_date' => $currentDate->toDateString(),
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]);
                    
                    $totalSales++;
                }
                
                $currentDate->addDay();
            }
            
            $this->command->info("  ✓ {$product->name}: Generated sales data");
        }
        
        $this->command->info("✅ Generated {$totalSales} sales records for {$products->count()} products");
    }
    
    /**
     * Get base demand based on product characteristics
     */
    private function getBaseDemand(Product $product): int
    {
        // Higher price = lower demand typically
        if ($product->price > 100000) {
            return rand(1, 3); // Generators, expensive items
        } elseif ($product->price > 20000) {
            return rand(3, 8); // Geysers, mid-range
        } else {
            return rand(8, 20); // Auto parts, common items
        }
    }
    
    /**
     * Calculate quantity with realistic patterns
     */
    private function calculateQuantity(
        int $baseDemand,
        float $variance,
        Carbon $date,
        Product $product
    ): int {
        $quantity = $baseDemand;
        
        // Weekly pattern: Higher sales on weekdays
        if ($date->isWeekend()) {
            $quantity = (int)($quantity * 0.7); // 30% less on weekends
        }
        
        // Monthly pattern: Higher sales at month end (salary days)
        if ($date->day >= 25) {
            $quantity = (int)($quantity * 1.3); // 30% more at month end
        }
        
        // Trend: Gradual increase over time (business growth)
        $daysFromStart = 90 - Carbon::now()->diffInDays($date);
        $trendFactor = 1 + ($daysFromStart * 0.002); // 0.2% growth per day
        $quantity = (int)($quantity * $trendFactor);
        
        // Random variance
        $randomVariance = rand(-$variance, $variance);
        $quantity += (int)$randomVariance;
        
        // Seasonal spikes (simulate promotion/festival)
        if (rand(1, 100) > 95) { // 5% chance of spike
            $quantity = (int)($quantity * 2);
        }
        
        return max(1, $quantity); // At least 1
    }
}
