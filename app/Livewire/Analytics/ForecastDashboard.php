<?php

namespace App\Livewire\Analytics;

use App\Models\DemandForecast;
use App\Models\Product;
use App\Models\Sale;
use App\Services\ForecastingService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.admin')]
class ForecastDashboard extends Component
{
    public $selectedProductId;
    public $forecastData = [];
    public $labels = [];
    public $isGenerating = false;
    public $forecastDays = 90;
    public string $forecastPeriod = '3months'; // 3months | 6months | 12months | custom
    public int $customDays = 90;
    public $errorMessage = null;
    public $successMessage = null;
    public $mlServiceStatus = 'unknown'; // unknown, available, unavailable
    
    public function mount(ForecastingService $service)
    {
        try {
            // Check ML service availability
            $this->checkMLServiceStatus($service);
            
            // Auto-select first product
            $firstProduct = Product::first();
            
            if (!$firstProduct) {
                $this->errorMessage = 'No products found in database. Please add products first.';
                Log::warning('ForecastDashboard: No products found');
                return;
            }
            
            $this->selectedProductId = $firstProduct->id;
            
            // Load existing data or generate if empty
            $hasData = DemandForecast::where('product_id', $this->selectedProductId)
                ->where('forecast_date', '>=', now())
                ->exists();
                
            if (!$hasData) {
                Log::info("ForecastDashboard: No forecast data found for product {$this->selectedProductId}, auto-generating...");
                $this->generateForecast();
            }
            
            $this->loadData();
            
        } catch (\Exception $e) {
            Log::error('ForecastDashboard mount error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->errorMessage = 'Error initializing forecast dashboard. Please refresh the page.';
        }
    }
    
    /**
     * Map period key to number of days
     */
    private function periodToDays(): int
    {
        return match ($this->forecastPeriod) {
            '3months'  => 90,
            '6months'  => 180,
            '12months' => 365,
            'custom'   => max(1, min(730, $this->customDays)),
            default    => 90,
        };
    }

    public function updatedForecastPeriod(): void
    {
        $this->forecastDays = $this->periodToDays();
        $this->errorMessage = null;
        $this->successMessage = null;
        $this->loadData();
    }

    public function updatedCustomDays(): void
    {
        if ($this->forecastPeriod === 'custom') {
            $this->forecastDays = $this->periodToDays();
            $this->errorMessage = null;
            $this->successMessage = null;
            $this->loadData();
        }
    }

    public function updatedSelectedProductId()
    {
        try {
            if (!$this->selectedProductId) {
                return;
            }
            
            // Clear previous messages
            $this->errorMessage = null;
            $this->successMessage = null;
            
            // Simply load data for the selected product
            // If no forecast exists, empty state will be shown (not an error)
            Log::info("Product changed to {$this->selectedProductId}");
            $this->loadData();
            
        } catch (\Exception $e) {
            Log::error('Error updating selected product: ' . $e->getMessage());
            $this->errorMessage = 'Error loading product data. Please try again.';
        }
    }
    
    /**
     * Check if ML service is available
     */
    private function checkMLServiceStatus(ForecastingService $service)
    {
        try {
            $response = Http::timeout(3)->get(env('ML_SERVICE_URL', 'http://localhost:5000') . '/api/health');
            
            if ($response->successful()) {
                $this->mlServiceStatus = 'available';
                Log::info('ML Service is available');
            } else {
                $this->mlServiceStatus = 'unavailable';
                Log::warning('ML Service returned non-successful status');
            }
        } catch (\Exception $e) {
            $this->mlServiceStatus = 'unavailable';
            Log::warning('ML Service is not reachable: ' . $e->getMessage());
        }
    }
    
    public function generateForecast()
    {
        if (!$this->selectedProductId) {
            $this->errorMessage = 'Please select a product first';
            return;
        }
        
        $this->isGenerating = true;
        $this->errorMessage = null;
        $this->successMessage = null;
        
        try {
            $service = new ForecastingService();
            $product = Product::find($this->selectedProductId);
            
            if (!$product) {
                throw new \Exception('Product not found with ID: ' . $this->selectedProductId);
            }
            
            Log::info("Generating forecast for product: {$product->name} (ID: {$product->id})");
            
            // Check if we have sales data for this product
            $salesCount = Sale::where('product_id', $product->id)->count();
            
            if ($salesCount < 5) {
                Log::warning("Insufficient sales data for product {$product->id}: only {$salesCount} records");
                $this->errorMessage = "Warning: Only {$salesCount} sales records found. Forecasts may be less accurate. Consider adding more historical sales data.";
            }
            
            // Call ML service to generate forecasts
            $success = $service->generateMLForecasts($product, $this->forecastDays);
            
            if ($success) {
                Log::info("Successfully generated forecast for product {$product->id}");
                $this->successMessage = 'Forecast generated successfully! Check the predictions below.';
                $this->loadData();
            } else {
                // Check if fallback data was created
                $fallbackCount = DemandForecast::where('product_id', $product->id)
                    ->where('forecast_date', '>=', now())
                    ->where('model_used', 'Simple Moving Average')
                    ->count();
                    
                if ($fallbackCount > 0) {
                    Log::info("Used fallback forecasting for product {$product->id}");
                    $this->successMessage = 'Forecast generated successfully! Review your inventory plan.';
                    $this->loadData();
                } else {
                    throw new \Exception('Failed to generate forecast with both ML and fallback methods');
                }
            }
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML Service connection error: ' . $e->getMessage());
            $this->errorMessage = 'Unable to generate forecast. Please contact system administrator.';
            
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('ML Service request error: ' . $e->getMessage());
            $this->errorMessage = 'Forecast generation failed. Please try again or contact support.';
            
        } catch (\Exception $e) {
            Log::error('Forecast generation error: ' . $e->getMessage(), [
                'product_id' => $this->selectedProductId,
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->errorMessage = 'Error generating forecast. Please try again later.';
            
        } finally {
            $this->isGenerating = false;
        }
    }
    
    public function loadData()
    {
        try {
            if (!$this->selectedProductId) {
                $this->labels = [];
                $this->forecastData = [];
                return;
            }
            
            // Get forecasts for next 90 days
            $forecasts = DemandForecast::where('product_id', $this->selectedProductId)
                ->where('forecast_date', '>=', now()->startOfDay())
                ->where('forecast_date', '<=', now()->addDays($this->forecastDays))
                ->orderBy('forecast_date')
                ->get();
            
            if ($forecasts->isEmpty()) {
                Log::info("No forecast data found for product {$this->selectedProductId}");
                $this->labels = [];
                $this->forecastData = [];
                $this->dispatch('update-chart', [
                    'labels' => [],
                    'data' => []
                ]);
                return;
            }
            
            // Group by month for better visualization
            $monthlyData = [];
            
            foreach ($forecasts as $forecast) {
                $monthKey = $forecast->forecast_date->format('Y-m');
                $monthLabel = $forecast->forecast_date->format('M Y');
                
                if (!isset($monthlyData[$monthKey])) {
                    $monthlyData[$monthKey] = [
                        'label' => $monthLabel,
                        'total' => 0,
                        'count' => 0
                    ];
                }
                
                $monthlyData[$monthKey]['total'] += $forecast->predicted_quantity;
                $monthlyData[$monthKey]['count']++;
            }
            
            // Calculate monthly totals
            $this->labels = [];
            $this->forecastData = [];
            
            foreach ($monthlyData as $data) {
                $this->labels[] = $data['label'];
                $this->forecastData[] = round($data['total']); // Total demand per month
            }
            
            Log::info("Loaded {$forecasts->count()} forecasts grouped into " . count($monthlyData) . " months");
            
            // Dispatch event for Chart.js update
            $this->dispatch('update-chart', [
                'labels' => $this->labels,
                'data' => $this->forecastData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading forecast data: ' . $e->getMessage(), [
                'product_id' => $this->selectedProductId,
                'trace' => $e->getTraceAsString()
            ]);
            $this->errorMessage = 'Error loading forecast data. Please try again.';
        }
    }

    public function downloadReport()
    {
        $data = DemandForecast::where('product_id', $this->selectedProductId)
            ->where('forecast_date', '>=', now())
            ->orderBy('forecast_date')
            ->get();
            
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Predicted Quantity', 'Confidence Score']);
            
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->forecast_date->format('Y-m-d'),
                    $row->predicted_quantity,
                    $row->confidence_score
                ]);
            }
            
            fclose($handle);
        }, 'forecast-report-' . now()->format('Y-m-d') . '.csv');
    }
    
    public function render()
    {
        try {
            $product = $this->selectedProductId ? Product::find($this->selectedProductId) : null;
            
            // Get total forecast summary for selected product
            $totalForecast = 0;
            $avgDaily = 0;
            $modelUsed = 'N/A';
            $salesCount = 0;
            
            if ($this->selectedProductId) {
                $forecasts = DemandForecast::where('product_id', $this->selectedProductId)
                    ->where('forecast_date', '>=', now())
                    ->where('forecast_date', '<=', now()->addDays(30))
                    ->get();
                    
                $totalForecast = $forecasts->sum('predicted_quantity');
                $avgDaily = $forecasts->count() > 0 ? round($totalForecast / $forecasts->count(), 1) : 0;
                $modelUsed = $forecasts->first()->model_used ?? 'N/A';
                
                // Get sales count for this product
                $salesCount = Sale::where('product_id', $this->selectedProductId)->count();
            }
            
            // Get optimization data with error handling
            $optimization = null;
            if ($product) {
                try {
                    $service = new ForecastingService();
                    $optimization = $service->optimizeInventory($product);
                } catch (\Exception $e) {
                    Log::warning('Error calculating optimization: ' . $e->getMessage());
                }
            }
            
            $periodLabel = match ($this->forecastPeriod) {
                '3months'  => 'Next 3 Months',
                '6months'  => 'Next 6 Months',
                '12months' => 'Next 12 Months',
                'custom'   => "Next {$this->forecastDays} Days",
                default    => 'Next 3 Months',
            };

            return view('livewire.analytics.forecast-dashboard', [
                'products'     => Product::all(),
                'product'      => $product,
                'optimization' => $optimization,
                'totalForecast' => $totalForecast,
                'avgDaily'     => $avgDaily,
                'modelUsed'    => $modelUsed,
                'salesCount'   => $salesCount,
                'periodLabel'  => $periodLabel,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error rendering forecast dashboard: ' . $e->getMessage());
            
            // Return minimal view on error
            return view('livewire.analytics.forecast-dashboard', [
                'products' => Product::all(),
                'product' => null,
                'optimization' => null,
                'totalForecast' => 0,
                'avgDaily' => 0,
                'modelUsed' => 'Error',
                'salesCount' => 0,
            ]);
        }
    }
}
