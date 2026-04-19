<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSalesData extends Command
{
    protected $signature = 'generate:sales {--days=60 : Number of days to generate}';
    protected $description = 'Generate sample sales data for ML forecasting';

    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("Generating sales data...");
        
        // Delete existing sales
        Sale::truncate();
        $this->warn("Cleared existing sales data");
        
        // Get first 10 products
        $products = Product::limit(10)->get();
        
        if ($products->isEmpty()) {
            $this->error("No products found. Run: php artisan db:seed");
            return 1;
        }
        
        $this->info("Found {$products->count()} products");
        
        $salesCount = 0;
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();
        
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();
        
        foreach ($products as $product) {
            $baseQty = rand(10, 50);
            
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Higher sales on weekdays
                $weekdayMultiplier = $currentDate->isWeekday() ? 1.3 : 0.8;
                
                // Random variation
                $randomFactor = rand(70, 130) / 100;
                
                $quantity = max(1, (int)($baseQty * $weekdayMultiplier * $randomFactor));
                $price = rand(1000, 50000);
                $totalAmount = $quantity * $price;
                
                Sale::create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_amount' => $totalAmount,
                    'sale_date' => $currentDate->toDateString()
                ]);
                
                $salesCount++;
                $currentDate->addDay();
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Generated $salesCount sales records!");
        $this->info("  Date range: {$startDate->toDateString()} to {$endDate->toDateString()}");
        $this->newLine();
        
        // Show sample
        $this->info("Sample data:");
        $this->table(
            ['Product', 'Days', 'Total Qty'],
            Sale::with('product')
                ->selectRaw('product_id, COUNT(DISTINCT sale_date) as days, SUM(quantity) as total_qty')
                ->groupBy('product_id')
                ->limit(5)
                ->get()
                ->map(fn($row) => [
                    $row->product->name,
                    $row->days,
                    $row->total_qty
                ])
        );
        
        $this->newLine();
        $this->info("✓ Ready for ML forecasting!");
        
        return 0;
    }
}
