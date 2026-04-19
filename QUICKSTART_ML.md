# 🚀 ML Forecasting - Quick Start Guide

## ✅ Installation Complete!

All Python dependencies have been installed and the ML service is configured.

## 📋 What's Installed

- ✅ **Flask** - Web framework for ML API
- ✅ **Prophet** - Facebook's forecasting model  
- ✅ **pandas & numpy** - Data processing
- ✅ **scikit-learn** - Machine learning utilities
- ✅ **MySQL Connector** - Database integration

## 🎯 Quick Start (3 Steps)

### 1. Start ML Service

```powershell
cd ml-service
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe app.py
```

**Or use the helper script:**
```powershell
.\start-ml-service.ps1
```

### 2. Start Laravel

```powershell
php artisan serve
```

### 3. Start Frontend

```powershell
npm run dev
```

## 🧪 Test Everything

Run the automated test script:

```powershell
.\setup-and-test.ps1
```

This will:
- ✓ Verify Python environment
- ✓ Check database connection
- ✓ Start ML service
- ✓ Test API endpoints
- ✓ Verify Laravel integration

## 📊 Generate Sample Data

If you need more sales data for better forecasts:

```powershell
php artisan generate:sales --days=90
```

This creates 90 days of historical sales data.

## 🔍 Manual Testing

### Test ML Service Health

```powershell
curl http://localhost:5000/api/health
```

### Generate Forecast for Product

```powershell
$body = @{product_id = 2; days = 14} | ConvertTo-Json
Invoke-RestMethod -Uri http://localhost:5000/api/forecast -Method Post -Body $body -ContentType "application/json"
```

### Test from Laravel

```powershell
php artisan tinker
```

```php
$service = new \App\Services\ForecastingService();
$service->generateForecasts();

// Check results
$forecasts = \App\Models\DemandForecast::where('model_used', 'Prophet ML')->get();
echo $forecasts->count() . " ML forecasts generated\n";
```

## 📁 Project Structure

```
rew-optimized/
├── ml-service/                  # Python ML Backend
│   ├── app.py                   # Flask API server
│   ├── models/
│   │   ├── demand_forecast.py   # Prophet model
│   │   └── anomaly_detection.py # Anomaly detection
│   ├── requirements.txt         # Dependencies
│   ├── .env                     # Configuration
│   └── test_service.py          # Test suite
│
├── app/Services/
│   └── ForecastingService.php   # Laravel integration
│
└── .venv/                       # Python virtual environment
```

## 🌐 API Endpoints

- `GET  /api/health` - Service health check
- `POST /api/forecast` - Generate forecast for one product
- `POST /api/forecast/batch` - Generate for multiple products
- `POST /api/anomalies` - Detect sales anomalies

## ⚙️ Configuration

### Laravel (.env)
```env
ML_SERVICE_URL=http://localhost:5000
ML_SERVICE_ENABLED=true
```

### ML Service (ml-service/.env)
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=rew_optimized
```

## 🔧 Troubleshooting

### ML Service Won't Start

```powershell
# Check Python version (need 3.8+)
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe --version

# Reinstall dependencies
cd ml-service
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe -m pip install -r requirements.txt
```

### Database Connection Error

```powershell
# Test MySQL connection
C:\xampp\mysql\bin\mysql.exe -u root -e "USE rew_optimized; SELECT COUNT(*) FROM products;"
```

### Laravel Can't Connect to ML Service

1. Verify ML service is running: `curl http://localhost:5000/api/health`
2. Check Laravel logs: `Get-Content storage\logs\laravel.log -Tail 20`
3. Clear Laravel cache: `php artisan config:clear`

### Port 5000 Already in Use

Edit `ml-service/.env` and change:
```env
PORT=5001
```

Then update Laravel `.env`:
```env
ML_SERVICE_URL=http://localhost:5001
```

## 📚 Documentation

- **Full Guide**: `ML_INTEGRATION_GUIDE.md`
- **Implementation Details**: `IMPLEMENTATION_SUMMARY.md`  
- **ML Service Docs**: `ml-service/README.md`
- **Setup Guide**: `ml-service/SETUP.md`

## 🎓 How It Works

1. **User Request**: Admin clicks "Generate Forecasts" in dashboard
2. **Laravel Check**: `ForecastingService` checks if ML service is available
3. **API Call**: Sends product ID to Python ML service 
4. **Prophet Training**: ML service trains Prophet model on historical sales
5. **Forecast Generation**: Generates 30-day predictions with confidence intervals
6. **Database Save**: Laravel saves forecasts to `demand_forecasts` table
7. **Display**: Frontend shows interactive forecast charts

## ✨ Features

- ✅ **Prophet ML Model** - Industry-standard forecasting
- ✅ **Confidence Intervals** - Upper/lower bounds for each prediction
- ✅ **Seasonality Detection** - Automatic weekly/monthly patterns
- ✅ **Anomaly Detection** - Identifies unusual sales spikes/drops
- ✅ **Fallback Logic** - Uses simple moving average if ML unavailable
- ✅ **RESTful API** - Standard HTTP interface
- ✅ **Batch Processing** - Generate multiple forecasts at once

## 🚦 System Status

Check if everything is running:

```powershell
# ML Service
curl http://localhost:5000/api/health

# Laravel
curl http://localhost:8000

# Database
C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT 1"
```

## 📞 Need Help?

1. Check logs:
   - ML Service: Console output where you ran `python app.py`
   - Laravel: `storage/logs/laravel.log`
   - Browser: F12 Developer Console

2. Run diagnostic script:
   ```powershell
   cd ml-service
   C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe test_service.py
   ```

3. Review documentation in `ML_INTEGRATION_GUIDE.md`

## 🎉 You're Ready!

Your ML forecasting system is fully operational. Start the services and begin generating accurate demand forecasts!

```powershell
# Option 1: Use automated script
.\setup-and-test.ps1

# Option 2: Start manually
.\start-all-services.ps1
```

Then visit: **http://localhost:8000**
