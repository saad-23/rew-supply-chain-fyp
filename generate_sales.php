<?php

// Security: this script must only run from the command line, never via web
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;

// Delete existing sales
Sale::truncate();

// Get first 10 products
$products = Product::limit(10)->get();

echo "Generating sales data for " . $products->count() . " products...\n";

$salesCount = 0;
$startDate = Carbon::now()->subDays(60);
$endDate = Carbon::now();

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
        $revenue = $quantity * $price;
        
        Sale::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'revenue' => $revenue,
            'sale_date' => $currentDate->toDateString()
        ]);
        
        $salesCount++;
        $currentDate->addDay();
    }
}

echo "✓ Generated $salesCount sales records!\n";
echo "  Date range: " . $startDate->toDateString() . " to " . $endDate->toDateString() . "\n";
echo "\nSample data:\n";

$sample = Sale::with('product')
    ->selectRaw('product_id, COUNT(*) as days, SUM(quantity) as total_qty')
    ->groupBy('product_id')
    ->limit(5)
    ->get();

foreach ($sample as $row) {
    echo "  " . $row->product->name . ": " . $row->days . " days, " . $row->total_qty . " units\n";
}

echo "\n✓ Done! Ready for ML forecasting.\n";
