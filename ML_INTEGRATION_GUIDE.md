# Machine Learning Integration Guide

## Overview

This project now includes an advanced Machine Learning microservice that provides:

- ✅ **Demand Forecasting** using Facebook Prophet
- ✅ **Anomaly Detection** using multiple ML algorithms
- ✅ **RESTful API** for easy integration
- ✅ **Automatic Fallback** when ML service is unavailable

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                      │
│                                                             │
│  ┌─────────────────┐         ┌────────────────────────┐   │
│  │ ForecastingService│────────▶│  ML Service (Python)  │   │
│  │     (PHP)        │  HTTP   │                        │   │
│  └─────────────────┘         │  - Prophet Model       │   │
│                               │  - Anomaly Detection   │   │
│                               └────────────────────────┘   │
│                                         │                   │
│                                         ▼                   │
│                               ┌────────────────┐           │
│                               │  MySQL Database│           │
│                               │  (sales data)  │           │
│                               └────────────────┘           │
└─────────────────────────────────────────────────────────────┘
```

## Quick Start

### 1. Install Python Dependencies

```bash
cd ml-service
pip install -r requirements.txt
```

### 2. Configure Environment

The ML service is pre-configured to connect to your database.
Check `ml-service/.env` if you need to adjust settings.

### 3. Start Services

**Terminal 1 - ML Service:**
```bash
cd ml-service
python app.py
```

**Terminal 2 - Laravel:**
```bash
php artisan serve
```

**Terminal 3 - Frontend:**
```bash
npm run dev
```

### 4. Test the Integration

Run the test suite:
```bash
cd ml-service
python test_service.py
```

## How It Works

### Demand Forecasting Flow

1. User clicks "Generate Forecasts" in the Analytics Dashboard
2. Laravel's `ForecastingService` checks if ML service is available
3. If available, sends request to Python ML service
4. Prophet model trains on historical sales data
5. Generates 30-day forecast with confidence intervals
6. Results saved to `demand_forecasts` table
7. Frontend displays interactive forecast charts

### Prophet Model Benefits

**Why Prophet is ideal for supply chain forecasting:**

- 📊 **Handles Seasonality**: Detects weekly/monthly/yearly patterns
- 🔄 **Adapts to Trends**: Identifies growth or decline patterns
- 🎯 **Confidence Intervals**: Provides uncertainty quantification
- 🚀 **Works with Limited Data**: Can forecast with as few as 2 data points
- 🛡️ **Robust to Outliers**: Handles anomalies gracefully
- 📅 **Holiday Effects**: Can incorporate special events

### Example Forecast Output

```json
{
  "date": "2026-02-08",
  "predicted_quantity": 25,
  "confidence_lower": 20,
  "confidence_upper": 30,
  "confidence_score": 0.85,
  "model_used": "Prophet ML"
}
```

## API Endpoints

### 1. Generate Forecast

```bash
POST http://localhost:5000/api/forecast

{
  "product_id": 1,
  "days": 30
}
```

### 2. Batch Forecast

```bash
POST http://localhost:5000/api/forecast/batch

{
  "product_ids": [1, 2, 3],
  "days": 30
}
```

### 3. Detect Anomalies

```bash
POST http://localhost:5000/api/anomalies

{
  "product_id": 1,
  "days": 30
}
```

## Laravel Integration

### ForecastingService.php

The service automatically detects ML service availability:

```php
<?php

namespace App\Services;

class ForecastingService
{
    public function generateForecasts()
    {
        if ($this->isMLServiceAvailable()) {
            // Use Python ML Service (Prophet)
            $this->generateMLForecasts($product);
        } else {
            // Fallback to simple moving average
            $this->generateSimpleForecasts($product);
        }
    }
}
```

### Configuration

In your Laravel `.env`:

```env
# ML Service Configuration
ML_SERVICE_URL=http://localhost:5000
ML_SERVICE_ENABLED=true
```

Set `ML_SERVICE_ENABLED=false` to always use simple forecasting.

## Anomaly Detection

The ML service can detect unusual sales patterns:

### Types of Anomalies Detected

1. **Spikes**: Sudden increases in sales
   - Could indicate: viral product, successful promotion, stockouts ending
   
2. **Drops**: Sudden decreases in sales
   - Could indicate: quality issues, competitor actions, stockouts

3. **Pattern Changes**: Shifts in typical behavior
   - Could indicate: seasonal changes, market shifts

### Detection Methods

The service uses three complementary methods:

1. **Z-Score**: Statistical deviation from mean
2. **Isolation Forest**: ML-based pattern detection
3. **Moving Average**: Trend-based detection

### Example Usage in Laravel

```php
// In your controller or service
$response = Http::post('http://localhost:5000/api/anomalies', [
    'product_id' => 1,
    'days' => 30
]);

$anomalies = $response->json()['anomalies'];

foreach ($anomalies as $anomaly) {
    // Create anomaly record
    Anomaly::create([
        'product_id' => 1,
        'detected_date' => $anomaly['date'],
        'anomaly_type' => $anomaly['type'], // 'spike' or 'drop'
        'severity' => $anomaly['severity'], // 'high', 'medium', 'low'
        'description' => $anomaly['description']
    ]);
}
```

## Performance Considerations

### Response Times

- **First Forecast**: ~2-5 seconds (model training)
- **Subsequent Forecasts**: ~500ms-1s
- **Batch Forecasting**: Linear scaling (3 products ≈ 3-6 seconds)
- **Anomaly Detection**: ~500ms-2s

### Optimization Tips

1. **Cache Forecasts**: Don't regenerate constantly
   ```php
   // Generate once per day
   if ($lastGenerated < Carbon::now()->subHours(24)) {
       $service->generateForecasts();
   }
   ```

2. **Use Batch Endpoints**: More efficient than individual requests
   ```php
   $productIds = [1, 2, 3, 4, 5];
   Http::post('/api/forecast/batch', ['product_ids' => $productIds]);
   ```

3. **Background Jobs**: Use Laravel queues
   ```php
   dispatch(new GenerateForecastsJob());
   ```

## Troubleshooting

### ML Service Won't Start

```bash
# Check Python version (need 3.8+)
python --version

# Reinstall dependencies
cd ml-service
pip install -r requirements.txt --upgrade
```

### Database Connection Errors

```bash
# Test MySQL connection
mysql -u root -p rew_optimized

# Verify .env settings in ml-service/.env
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=rew_optimized
```

### Prophet Installation Issues

**Windows**: Install Visual C++ Build Tools
- Download: https://visualstudio.microsoft.com/visual-cpp-build-tools/

**macOS**: 
```bash
brew install cmake
pip install pystan
pip install prophet
```

**Linux**:
```bash
sudo apt-get install python3-dev
pip install pystan
pip install prophet
```

### Laravel Can't Connect to ML Service

1. Check ML service is running: `curl http://localhost:5000/api/health`
2. Verify `.env` has correct URL: `ML_SERVICE_URL=http://localhost:5000`
3. Clear config cache: `php artisan config:clear`
4. Check firewall/antivirus isn't blocking port 5000

### Forecasts Show Low Accuracy

1. **Insufficient Data**: Prophet needs at least 2 data points
   - Solution: Run seeders to generate sample data
   
2. **Erratic Sales Pattern**: Highly irregular sales are hard to predict
   - Solution: Consider using longer historical periods
   
3. **Recent Changes**: Model may not capture very recent shifts
   - Solution: Retrain models more frequently

## Advanced Usage

### Custom Forecast Periods

```php
// Forecast for specific number of days
$forecasts = $forecaster->predict($productId, days: 60);
```

### Adding Holiday Effects

Modify `ml-service/models/demand_forecast.py`:

```python
from prophet.make_holidays import make_holidays_df

# Add holidays
holidays = make_holidays_df(
    year_list=[2026],
    country='US'
)

model = Prophet(holidays=holidays)
```

### Model Persistence (Cache Trained Models)

```python
import pickle

# Save model
with open(f'models/cache/product_{product_id}.pkl', 'wb') as f:
    pickle.dump(model, f)

# Load model
with open(f'models/cache/product_{product_id}.pkl', 'rb') as f:
    model = pickle.load(f)
```

## Monitoring & Logging

### Check ML Service Logs

```bash
# Console output shows all requests
python app.py

# Example log output:
# INFO:__main__:Generating forecast for product 1 for 30 days
# INFO:models.demand_forecast:Retrieved 45 sales records
# INFO:models.demand_forecast:Training Prophet model
# INFO:__main__:Successfully generated 30 forecasts
```

### Check Laravel Logs

```bash
tail -f storage/logs/laravel.log

# Look for:
# [2026-02-07 12:00:00] local.INFO: Generated ML forecasts for product 1
# [2026-02-07 12:00:01] local.ERROR: ML Service unavailable, using fallback
```

## Production Deployment

### Option 1: Same Server

```nginx
# Nginx reverse proxy
location /ml/ {
    proxy_pass http://127.0.0.1:5000/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
}
```

### Option 2: Separate Server

```bash
# Deploy ML service to separate server
scp -r ml-service/ user@ml-server:/var/www/ml-service/

# Update Laravel .env
ML_SERVICE_URL=http://ml-server.yourdomain.com:5000
```

### Option 3: Docker

```bash
# Build and run ML service in Docker
cd ml-service
docker build -t ml-service .
docker run -d -p 5000:5000 ml-service
```

### Production Server (Gunicorn)

```bash
# Install gunicorn
pip install gunicorn

# Run with gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

## Future Enhancements

Potential additions to the ML service:

1. **LSTM Neural Networks**: For complex patterns with large datasets
2. **Route Optimization**: Use OR-Tools for delivery route planning
3. **Price Optimization**: Dynamic pricing recommendations
4. **Supplier Scoring**: ML-based supplier reliability prediction
5. **Inventory Optimization**: Multi-echelon inventory planning
6. **Image Recognition**: Product categorization from images
7. **NLP**: Customer review sentiment analysis

## Testing

Run comprehensive tests:

```bash
cd ml-service
python test_service.py
```

Test from Laravel:

```php
php artisan tinker

>>> $service = app(\App\Services\ForecastingService::class);
>>> $service->generateForecasts();
>>> $forecasts = \App\Models\DemandForecast::where('model_used', 'Prophet ML')->get();
>>> $forecasts->count(); // Should show generated forecasts
```

## Support & Documentation

- **ML Service README**: `ml-service/README.md`
- **Setup Guide**: `ml-service/SETUP.md`
- **Test Suite**: `ml-service/test_service.py`
- **Prophet Docs**: https://facebook.github.io/prophet/
- **Scikit-learn**: https://scikit-learn.org/

## Summary

You now have a production-ready ML forecasting system that:

✅ Uses industry-standard Prophet model  
✅ Provides accurate demand forecasts  
✅ Detects anomalies automatically  
✅ Integrates seamlessly with Laravel  
✅ Falls back gracefully when unavailable  
✅ Scales for production workloads  

The system is designed to improve inventory planning, reduce stockouts, and optimize supply chain operations.
