# Quick Setup Guide - ML Service

## Installation Steps

### 1. Install Python (if not already installed)

Download Python 3.11 from: https://www.python.org/downloads/

Make sure to check "Add Python to PATH" during installation.

### 2. Install Dependencies

Open PowerShell or Command Prompt in the project root:

```powershell
cd ml-service
pip install -r requirements.txt
```

**Note**: Installation may take 5-10 minutes as Prophet has many dependencies.

### 3. Verify Installation

```powershell
python -c "import prophet; print('Prophet installed successfully')"
python -c "import flask; print('Flask installed successfully')"
python -c "import pandas; print('Pandas installed successfully')"
```

### 4. Start ML Service

```powershell
python app.py
```

You should see:

```
 * Running on http://0.0.0.0:5000
 * Debug mode: on
INFO:__main__:Starting ML Forecasting Service on port 5000
```

### 5. Test ML Service

Open another terminal and test:

```powershell
curl http://localhost:5000/api/health
```

Or open in browser: http://localhost:5000

### 6. Start Laravel Application

In a separate terminal:

```powershell
cd ..
php artisan serve
```

### 7. Test Integration

1. Open your application: http://localhost:8000
2. Navigate to Analytics Dashboard
3. Click "Generate Forecasts"
4. Check the logs to see "Generated ML forecasts for product X"

## Running Both Services Together

### Terminal 1: ML Service
```powershell
cd ml-service
python app.py
```

### Terminal 2: Laravel
```powershell
php artisan serve
```

### Terminal 3: Frontend (if using Vite)
```powershell
npm run dev
```

## Testing the Forecast API

### Using PowerShell:

```powershell
$body = @{
    product_id = 1
    days = 14
} | ConvertTo-Json

Invoke-RestMethod -Uri http://localhost:5000/api/forecast -Method Post -Body $body -ContentType "application/json"
```

### Using curl (Git Bash or WSL):

```bash
curl -X POST http://localhost:5000/api/forecast \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "days": 14}'
```

## Common Issues

### Issue 1: "pip is not recognized"

**Solution**: Add Python to your PATH or use:
```powershell
python -m pip install -r requirements.txt
```

### Issue 2: Prophet installation fails

**Solution**: Install Visual C++ Build Tools from:
https://visualstudio.microsoft.com/visual-cpp-build-tools/

Then retry:
```powershell
pip install pystan
pip install prophet
```

### Issue 3: Port 5000 already in use

**Solution**: Change port in `ml-service/.env`:
```
PORT=5001
```

Then update Laravel `.env`:
```
ML_SERVICE_URL=http://localhost:5001
```

### Issue 4: Database connection error

**Solution**: Check `ml-service/.env` matches your MySQL settings:
```
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=your_password
DB_DATABASE=rew_optimized
```

## Verify Everything is Working

1. ML Service health: http://localhost:5000/api/health ✓
2. Laravel running: http://localhost:8000 ✓
3. Generate forecasts and check `demand_forecasts` table ✓

```sql
-- Check forecasts in database
SELECT * FROM demand_forecasts 
WHERE model_used = 'Prophet ML' 
ORDER BY created_at DESC 
LIMIT 10;
```

## Next Steps

- Read the full README.md for API documentation
- Test anomaly detection endpoint
- Explore batch forecasting for multiple products
- Check Laravel logs: storage/logs/laravel.log

## Performance Tips

- First forecast generation takes longer (model training)
- Subsequent forecasts for same product are faster
- Consider running ML service as a background service
- For production, use gunicorn or uwsgi instead of Flask dev server

## Need Help?

Check the logs:
- ML Service: Console output where you ran `python app.py`
- Laravel: `storage/logs/laravel.log`
- Browser Console: F12 in your browser
