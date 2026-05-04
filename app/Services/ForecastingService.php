<?php

namespace App\Services;

use App\Models\Product;
use App\Models\DemandForecast;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForecastingService
{
    private $mlServiceUrl;
    private $mlServiceEnabled;

    public function __construct()
    {
        $this->mlServiceUrl = env('ML_SERVICE_URL', 'http://localhost:5000');
        // Cast to boolean explicitly — env() returns string "true"/"false" which PHP treats as truthy
        $this->mlServiceEnabled = filter_var(env('ML_SERVICE_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Generate forecasts for all products using Python ML Service (Prophet Model)
     * Falls back to simple statistical method if ML service is unavailable
     */
    public function generateForecasts()
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            try {
                if ($this->mlServiceEnabled && $this->isMLServiceAvailable()) {
                    // Use Python ML Service with Prophet model
                    $this->generateMLForecasts($product);
                } else {
                    // Fallback to simple statistical method
                    Log::info("ML Service unavailable, using fallback for product {$product->id}");
                    $this->generateSimpleForecasts($product);
                }
            } catch (\Exception $e) {
                Log::error("Error forecasting product {$product->id}: " . $e->getMessage());
                // Use fallback on error
                $this->generateSimpleForecasts($product);
            }
        }
    }

    /**
     * Generate forecasts using Python ML Service (Prophet)
     */
    public function generateMLForecasts(Product $product, int $days = 30)
    {
        try {
            $response = Http::timeout(60)->post("{$this->mlServiceUrl}/api/forecast", [
                'product_id' => $product->id,
                'days' => $days
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    $forecasts = $data['forecasts'] ?? [];
                    
                    foreach ($forecasts as $forecast) {
                        DemandForecast::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'forecast_date' => $forecast['date'],
                            ],
                            [
                                'predicted_quantity' => $forecast['predicted_quantity'],
                                'model_used' => 'Prophet ML',
                                'confidence_score' => round($forecast['confidence_score'] * 100)
                            ]
                        );
                    }
                    
                    Log::info("Generated ML forecasts for product {$product->id}");
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("ML Service error for product {$product->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback: Simple statistical forecasting (Moving Average)
     * Used when ML service is unavailable
     */
    private function generateSimpleForecasts(Product $product, int $days = 30)
    {
        // Get Sales History
        $history = Sale::where('product_id', $product->id)
            ->where('sale_date', '>=', Carbon::now()->subDays(30))
            ->get();

        $dailyAvg = 10; // Default baseline
        
        if ($history->count() > 0) {
            $totalQty = $history->sum('quantity');
            $uniqueDays = $history->groupBy('sale_date')->count();
            if($uniqueDays > 0) {
                $dailyAvg = $totalQty / max($uniqueDays, 1); 
            }
        }

        // Generate forecasts
        for ($i = 1; $i <= $days; $i++) {
            $futureDate = Carbon::now()->addDays($i);
            
            // Simple logic: Moving Average + Seasonality
            $noise = rand(-2, 5);
            $seasonality = $futureDate->dayOfWeek > 4 ? 1.2 : 1.0;
            
            $predictedQty = round(($dailyAvg + $noise) * $seasonality);
            $predictedQty = max((int)$predictedQty, 0);
            
            DemandForecast::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'forecast_date' => $futureDate->toDateString(),
                ],
                [
                    'predicted_quantity' => $predictedQty,
                    'model_used' => 'Simple Moving Average',
                    'confidence_score' => rand(70, 85)
                ]
            );
        }
    }

    /**
     * Check if ML Service is available
     */
    private function isMLServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->mlServiceUrl}/api/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * FR4: Inventory Optimization
     * Returns ideal stock levels
     */
    public function optimizeInventory(Product $product)
    {
        // Safety Stock = (Max Daily Usage * Max Lead Time) - (Avg Daily Usage * Avg Lead Time)
        // This is a standard supply chain formula.
        
        $avgDailyUsage = DemandForecast::where('product_id', $product->id)->avg('predicted_quantity') ?? 10;
        $maxDailyUsage = DemandForecast::where('product_id', $product->id)->max('predicted_quantity') ?? 20;
        
        $supplier = $product->supplier; // Assuming relationship
        $leadTime = $supplier ? $supplier->lead_time_days : 7;
        
        $safetyStock = ($maxDailyUsage * ($leadTime + 2)) - ($avgDailyUsage * $leadTime);
        $reorderPoint = ($avgDailyUsage * $leadTime) + $safetyStock;
        
        return [
            'safety_stock' => round($safetyStock),
            'reorder_point' => round($reorderPoint),
            'status' => $product->current_stock < $reorderPoint ? 'Reorder Now' : 'Optimal',
            'recommended_order_qty' => round($reorderPoint * 2), // EOQ simplified
            'eoq' => round($reorderPoint * 2) // Alias for UI
        ];
    }
}
