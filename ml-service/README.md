# ML Service - Demand Forecasting & Anomaly Detection

This microservice provides machine learning capabilities for the REW Supply Chain Management System.

## 🎯 Features

- **Demand Forecasting**: Uses Facebook Prophet for accurate time-series forecasting
- **Anomaly Detection**: Identifies unusual sales patterns using multiple ML algorithms
- **RESTful API**: Easy integration with Laravel backend
- **Fallback Support**: Graceful degradation when service is unavailable

## 🚀 Quick Start

### 1. Install Python Dependencies

```bash
cd ml-service
pip install -r requirements.txt
```

**Note**: Prophet requires additional dependencies. On Windows, you may need:

```bash
pip install pystan
pip install prophet
```

### 2. Configure Environment

Copy the `.env` file and update database credentials if needed:

```bash
# ml-service/.env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=rew_optimized
```

### 3. Start the ML Service

```bash
python app.py
```

The service will start on `http://localhost:5000`

### 4. Verify Service is Running

```bash
curl http://localhost:5000/api/health
```

Expected response:
```json
{
  "status": "healthy",
  "service": "ml-forecasting",
  "timestamp": "2026-02-07 12:00:00"
}
```

## 📡 API Endpoints

### Health Check

```
GET /api/health
```

### Single Product Forecast

```
POST /api/forecast
Content-Type: application/json

{
  "product_id": 1,
  "days": 30
}
```

**Response:**
```json
{
  "success": true,
  "product_id": 1,
  "days": 30,
  "model_used": "Prophet",
  "forecasts": [
    {
      "date": "2026-02-08",
      "predicted_quantity": 25,
      "confidence_lower": 20,
      "confidence_upper": 30,
      "confidence_score": 0.85
    }
  ]
}
```

### Batch Forecasting

```
POST /api/forecast/batch
Content-Type: application/json

{
  "product_ids": [1, 2, 3],
  "days": 30
}
```

### Anomaly Detection

```
POST /api/anomalies
Content-Type: application/json

{
  "product_id": 1,
  "days": 30
}
```

**Response:**
```json
{
  "success": true,
  "product_id": 1,
  "anomalies": [
    {
      "date": "2026-02-05",
      "quantity": 100,
      "expected_quantity": 25,
      "deviation": 3.2,
      "type": "spike",
      "severity": "high",
      "method": "z-score",
      "description": "Sales spike: 100 units (3.2 std deviations from mean)"
    }
  ]
}
```

## 🧠 ML Models

### 1. Prophet (Demand Forecasting)

**Why Prophet?**
- Designed for business forecasting with strong seasonality
- Handles missing data and outliers well
- Works with limited historical data (as few as 2 data points)
- Provides uncertainty intervals

**How it works:**
1. Fetches historical sales data from database
2. Trains Prophet model with weekly/yearly seasonality
3. Generates predictions with confidence intervals
4. Returns forecasts to Laravel application

**Fallback:**
If no historical data exists, uses baseline forecasting based on category averages.

### 2. Anomaly Detection (Multi-Method)

Uses three complementary methods:

**a) Z-Score Method**
- Statistical approach
- Detects points > 2.5 standard deviations from mean
- Fast and interpretable

**b) Isolation Forest**
- Machine learning approach
- Detects patterns that differ from normal behavior
- Robust to noise

**c) Moving Average Deviation**
- Trend-based detection
- Compares current sales to 7-day moving average
- Catches sudden changes

## 🔄 Integration with Laravel

The Laravel application automatically detects and uses the ML service:

1. **ForecastingService.php** checks if ML service is available
2. If available, sends forecast requests to Python service
3. If unavailable, falls back to simple moving average method
4. Stores predictions in `demand_forecasts` table

### Testing Laravel Integration

```bash
# In Laravel root directory
php artisan tinker

# Test forecast generation
>>> $service = app(\App\Services\ForecastingService::class);
>>> $service->generateForecasts();
```

## 🧪 Testing the ML Service

### Test Single Product Forecast

```bash
curl -X POST http://localhost:5000/api/forecast \
  -H "Content-Type: application/json" \
  -d "{\"product_id\": 1, \"days\": 14}"
```

### Test with Python

```python
import requests

response = requests.post('http://localhost:5000/api/forecast', json={
    'product_id': 1,
    'days': 30
})

print(response.json())
```

### Test with Postman

1. Create a POST request to `http://localhost:5000/api/forecast`
2. Set header: `Content-Type: application/json`
3. Body (raw JSON):
```json
{
  "product_id": 1,
  "days": 30
}
```

## 📊 Performance & Scalability

### Current Configuration
- **Latency**: ~500ms for 30-day forecast
- **Throughput**: ~10 requests/second
- **Model Training**: On-demand (trains for each request)

### Optimization Tips

1. **Caching**: Cache trained models for frequently requested products
2. **Async Processing**: Use Celery for background processing
3. **Batch Processing**: Process multiple products simultaneously
4. **Model Persistence**: Save trained Prophet models to disk

### Example: Add Caching

```python
import pickle
from functools import lru_cache

@lru_cache(maxsize=100)
def get_cached_model(product_id):
    # Cache trained models in memory
    pass
```

## 🐛 Troubleshooting

### Issue: "Module 'prophet' not found"

**Solution:**
```bash
pip install pystan
pip install prophet
```

On Windows, you may need Visual C++ Build Tools.

### Issue: "Database connection failed"

**Solution:**
Check `.env` file credentials match your MySQL configuration.

```bash
# Test database connection
mysql -u root -p rew_optimized
```

### Issue: "Service returns 500 error"

**Solution:**
Check Python logs for detailed error messages:

```bash
python app.py
# Watch console output for errors
```

### Issue: Laravel can't connect to ML Service

**Solution:**
1. Verify ML service is running: `curl http://localhost:5000/api/health`
2. Check `.env` in Laravel root:
   ```
   ML_SERVICE_URL=http://localhost:5000
   ML_SERVICE_ENABLED=true
   ```
3. Restart Laravel: `php artisan config:clear`

## 🔐 Security Considerations

### Production Deployment

1. **Add Authentication**: Use API keys or JWT tokens
2. **HTTPS**: Use SSL/TLS for encrypted communication
3. **Rate Limiting**: Prevent abuse with request limits
4. **Input Validation**: Sanitize all user inputs

### Example: Add API Key Authentication

```python
@app.before_request
def check_api_key():
    api_key = request.headers.get('X-API-Key')
    if api_key != os.getenv('ML_API_KEY'):
        return jsonify({'error': 'Unauthorized'}), 401
```

## 📈 Future Enhancements

1. **LSTM Neural Networks**: For complex patterns with large datasets
2. **Route Optimization**: Using OR-Tools for delivery optimization
3. **Inventory Optimization**: Multi-echelon inventory planning
4. **Price Optimization**: Dynamic pricing recommendations
5. **Supplier Selection**: ML-based supplier scoring

## 🐳 Docker Deployment (Optional)

Create `Dockerfile`:

```dockerfile
FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["python", "app.py"]
```

Build and run:

```bash
docker build -t ml-service .
docker run -p 5000:5000 ml-service
```

## 📝 Logging

Logs are output to console. For production, configure file logging:

```python
logging.basicConfig(
    filename='ml-service.log',
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
```

## 🤝 Contributing

To add new ML models:

1. Create new file in `models/` directory
2. Implement the model class
3. Add endpoint in `app.py`
4. Update this README

## 📞 Support

For issues or questions:
- Check logs in console output
- Review API response messages
- Consult Laravel logs: `storage/logs/laravel.log`
