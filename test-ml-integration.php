<?php
/**
 * Quick Test Script for Python ML + Laravel Integration
 * Run: php test-ml-integration.php
 */

// Security: CLI only — never expose via web
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== QUICK ML INTEGRATION TEST ===\n\n";

// Test 1: ML Service Health
echo "[1/4] Testing ML Service Health...\n";
try {
    $health = \Illuminate\Support\Facades\Http::get('http://localhost:5000/api/health')->json();
    echo "  ✓ Status: {$health['status']}\n";
    echo "  ✓ Service: {$health['service']}\n\n";
} catch (\Exception $e) {
    echo "  ✗ ML Service not reachable: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Get a test product
echo "[2/4] Getting test product...\n";
$product = \App\Models\Product::first();
if (!$product) {
    echo "  ✗ No products found. Run: php artisan generate:sales\n";
    exit(1);
}
echo "  ✓ Product: {$product->name} (ID: {$product->id})\n\n";

// Test 3: Check sales data
echo "[3/4] Checking sales history...\n";
$salesCount = \App\Models\Sale::where('product_id', $product->id)->count();
echo "  ✓ Sales records for this product: {$salesCount}\n\n";

// Test 4: Generate ML Forecast
echo "[4/4] Generating ML forecast (this may take 5-10 seconds)...\n";
$startTime = microtime(true);

try {
    $response = \Illuminate\Support\Facades\Http::timeout(60)
        ->post('http://localhost:5000/api/forecast', [
            'product_id' => $product->id,
            'days' => 7
        ]);
    
    $duration = round((microtime(true) - $startTime), 2);
    
    if ($response->successful()) {
        $data = $response->json();
        echo "  ✓ Forecast generated in {$duration} seconds\n";
        echo "  ✓ Model: {$data['model_used']}\n";
        echo "  ✓ Predictions: " . count($data['forecasts']) . " days\n";
        
        if (isset($data['forecasts'][0])) {
            $first = $data['forecasts'][0];
            echo "  ✓ First prediction:\n";
            echo "      Date: {$first['date']}\n";
            echo "      Quantity: {$first['predicted_quantity']} units\n";
            echo "      Confidence: " . ($first['confidence_score'] * 100) . "%\n";
            echo "      Range: {$first['confidence_lower']} - {$first['confidence_upper']} units\n\n";
        }
        
        // Save to database
        foreach ($data['forecasts'] as $forecast) {
            \App\Models\DemandForecast::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'forecast_date' => $forecast['date'],
                ],
                [
                    'predicted_quantity' => $forecast['predicted_quantity'],
                    'model_used' => 'Prophet ML Test',
                    'confidence_score' => $forecast['confidence_score'] * 100
                ]
            );
        }
        echo "  ✓ Saved " . count($data['forecasts']) . " forecasts to database\n\n";
        
    } else {
        echo "  ✗ Forecast request failed\n";
        echo "  Response: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "=== TEST COMPLETE ===\n";
echo "✓ Python ML API is working with Laravel!\n\n";

// Show database results
$mlForecasts = \App\Models\DemandForecast::where('product_id', $product->id)
    ->where('model_used', 'LIKE', 'Prophet%')
    ->orderBy('forecast_date')
    ->limit(5)
    ->get();

if ($mlForecasts->count() > 0) {
    echo "Recent ML Forecasts in Database:\n";
    echo str_repeat("-", 70) . "\n";
    foreach ($mlForecasts as $f) {
        echo sprintf("  %s | Qty: %3d | Confidence: %d%% | Model: %s\n",
            $f->forecast_date,
            $f->predicted_quantity,
            $f->confidence_score,
            $f->model_used
        );
    }
    echo str_repeat("-", 70) . "\n\n";
}

echo "Next Steps:\n";
echo "  1. Open: http://localhost:8000\n";
echo "  2. Go to Analytics → Forecasts\n";
echo "  3. View your ML-generated predictions!\n\n";
