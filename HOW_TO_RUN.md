# 🚀 COMPLETE RUNNING GUIDE - Step by Step

## Prerequisites Check ✓

Before starting, verify:
- [ ] XAMPP is running (MySQL must be active)
- [ ] You're in the project directory: `C:\xampp\htdocs\rew-optimized`
- [ ] Python virtual environment is created (`.venv` folder exists)

## 🎯 METHOD 1: Quick Start (Easiest)

### Open PowerShell in project root and run:

```powershell
# Step 1: Start ML Service in a new window
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD\ml-service'; C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe app.py"

# Step 2: Wait for ML service to start (5 seconds)
Start-Sleep -Seconds 5

# Step 3: Start Laravel in current terminal
php artisan serve
```

Then open another terminal and run:
```powershell
npm run dev
```

**Done!** Open http://localhost:8000

---

## 🎯 METHOD 2: Use Helper Script

```powershell
.\start-all-services.ps1
```

This automatically opens 3 terminals for:
- ML Service (Python/Flask)
- Laravel Backend
- Vite Frontend

---

## 🎯 METHOD 3: Manual Step-by-Step (Most Control)

Follow these steps in order:

### **Terminal 1: Start ML Service**

```powershell
# Navigate to ml-service directory
cd C:\xampp\htdocs\rew-optimized\ml-service

# Activate virtual environment and start Flask
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe app.py
```

**Expected Output:**
```
Importing plotly failed. Interactive plots will not work.
2026-02-07 XX:XX:XX,XXX - models.demand_forecast - INFO - DemandForecaster initialized
2026-02-07 XX:XX:XX,XXX - models.anomaly_detection - INFO - AnomalyDetector initialized
 * Serving Flask app 'app'
 * Debug mode: on
 * Running on http://127.0.0.1:5000
 * Running on http://10.63.254.12:5000
```

✅ **ML Service is running when you see "Running on http://127.0.0.1:5000"**

---

### **Terminal 2: Start Laravel** (Open new PowerShell window)

```powershell
# Navigate to project root
cd C:\xampp\htdocs\rew-optimized

# Start Laravel development server
php artisan serve
```

**Expected Output:**
```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to stop the server
```

✅ **Laravel is running on http://localhost:8000**

---

### **Terminal 3: Start Frontend** (Open another new PowerShell window)

```powershell
# Navigate to project root
cd C:\xampp\htdocs\rew-optimized

# Start Vite development server
npm run dev
```

**Expected Output:**
```
VITE v5.x.x  ready in XXX ms

➜  Local:   http://localhost:5173/
➜  Network: http://10.63.254.12:5173/
```

✅ **Frontend assets compiling on http://localhost:5173**

---

## 🧪 VERIFY EVERYTHING IS WORKING

### 1. Test ML Service (in a new PowerShell window)

```powershell
# Test health endpoint
curl http://localhost:5000/api/health

# Or using Invoke-RestMethod
Invoke-RestMethod -Uri http://localhost:5000/api/health
```

**Expected Response:**
```json
{
  "status": "healthy",
  "service": "ml-forecasting",
  "timestamp": "2026-02-07 15:30:45.123456"
}
```

---

### 2. Test Laravel

```powershell
curl http://localhost:8000
```

Should return HTML of your homepage.

---

### 3. Test Full Integration

```powershell
php artisan tinker
```

Then in tinker:
```php
// Test ML service connection
$response = \Illuminate\Support\Facades\Http::get('http://localhost:5000/api/health');
echo $response->json()['status']; // Should print: healthy

// Generate ML forecasts
$service = new \App\Services\ForecastingService();
$service->generateForecasts();
echo "✓ Forecasts generated!\n";
exit
```

---

## 🌐 ACCESS YOUR APPLICATION

Once all 3 terminals are running, open your browser:

**Main Application:** http://localhost:8000

**Default Routes:**
- Dashboard: http://localhost:8000/dashboard
- Analytics: http://localhost:8000/analytics
- Forecasts: http://localhost:8000/analytics/forecast
- Products: http://localhost:8000/products

---

## 🎨 USING THE FORECAST FEATURE

1. Go to: **http://localhost:8000/analytics/forecast**
2. Click **"Generate New Forecasts"** button
3. Wait 10-30 seconds (Prophet model training takes time)
4. View forecast charts and predictions

---

## 🔧 TROUBLESHOOTING

### Problem: ML Service Won't Start

**Error:** "Port 5000 already in use"

**Solution:**
```powershell
# Find and kill process on port 5000
netstat -ano | findstr :5000
taskkill /PID <PID_NUMBER> /F

# Or use different port - edit ml-service/.env:
PORT=5001

# Then update Laravel .env:
ML_SERVICE_URL=http://localhost:5001
```

---

### Problem: "ModuleNotFoundError" in Python

**Solution:**
```powershell
# Reinstall packages
cd ml-service
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe -m pip install -r requirements.txt
```

---

### Problem: Database Connection Failed

**Solution:**
```powershell
# 1. Check MySQL is running in XAMPP
# 2. Test connection:
C:\xampp\mysql\bin\mysql.exe -u root -e "USE rew_optimized; SELECT 1;"

# 3. Verify credentials in both .env files:
#    - Root .env (Laravel)
#    - ml-service/.env (Python)
```

---

### Problem: Laravel Can't Connect to ML Service

**Check:**
```powershell
# 1. Is ML service actually running?
curl http://localhost:5000/api/health

# 2. Check Laravel .env
Get-Content .env | Select-String "ML_SERVICE"

# Should show:
# ML_SERVICE_URL=http://localhost:5000
# ML_SERVICE_ENABLED=true

# 3. Clear Laravel config cache
php artisan config:clear
```

---

### Problem: "No forecasts generated"

**Solution:**
```powershell
# Generate sample sales data first
php artisan generate:sales --days=60

# Verify data exists
C:\xampp\mysql\bin\mysql.exe -u root -e "USE rew_optimized; SELECT COUNT(*) FROM sales;"

# Then try forecasting again
```

---

## 📊 GENERATE MORE SAMPLE DATA

If you need more historical sales data for better predictions:

```powershell
# Generate 90 days of sales
php artisan generate:sales --days=90

# Check what was created
C:\xampp\mysql\bin\mysql.exe -u root -e "USE rew_optimized; SELECT product_id, COUNT(*) as days, SUM(quantity) as total_qty FROM sales GROUP BY product_id LIMIT 5;"
```

---

## 🛑 STOPPING THE SERVICES

### Stop ML Service
Press **Ctrl+C** in the terminal running Python

### Stop Laravel
Press **Ctrl+C** in the terminal running `php artisan serve`

### Stop Frontend
Press **Ctrl+C** in the terminal running `npm run dev`

---

## 💡 TIPS FOR SMOOTH OPERATION

### 1. Start in Correct Order
Always start ML Service → Laravel → Frontend

### 2. Keep Terminals Open
Don't close the terminal windows while using the app

### 3. Watch for Errors
Monitor the terminal outputs for any error messages

### 4. Use Logs
```powershell
# Laravel logs
Get-Content storage\logs\laravel.log -Tail 50 -Wait

# ML Service logs
# Check the terminal where app.py is running
```

---

## 🚀 RECOMMENDED WORKFLOW

### Daily Development Routine:

**Morning Start:**
```powershell
# Terminal 1
cd C:\xampp\htdocs\rew-optimized\ml-service
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe app.py

# Terminal 2
cd C:\xampp\htdocs\rew-optimized
php artisan serve

# Terminal 3
cd C:\xampp\htdocs\rew-optimized
npm run dev
```

**Evening Shutdown:**
- Press Ctrl+C in all 3 terminals
- Stop XAMPP MySQL if not needed

---

## 📞 QUICK REFERENCE COMMANDS

```powershell
# Check if services are running
curl http://localhost:5000/api/health  # ML Service
curl http://localhost:8000             # Laravel
curl http://localhost:5173             # Vite

# Restart ML service
# Ctrl+C in Terminal 1, then:
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe app.py

# Restart Laravel
# Ctrl+C in Terminal 2, then:
php artisan serve

# Clear Laravel cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Generate fresh data
php artisan generate:sales --days=60

# Run tests
cd ml-service
C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe test_service.py
```

---

## ✅ CHECKLIST BEFORE YOU START

- [ ] XAMPP MySQL is running (green light in XAMPP Control Panel)
- [ ] You're in project directory: `C:\xampp\htdocs\rew-optimized`
- [ ] `.venv` folder exists
- [ ] Database `rew_optimized` exists and has data
- [ ] Port 5000 is free (ML Service)
- [ ] Port 8000 is free (Laravel)
- [ ] Port 5173 is free (Vite)

---

## 🎯 SIMPLIFIED ONE-LINER (For Quick Start)

Open PowerShell as Administrator and run:

```powershell
cd C:\xampp\htdocs\rew-optimized; Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD\ml-service'; C:/xampp/htdocs/rew-optimized/.venv/Scripts/python.exe app.py"; Start-Sleep 3; Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; php artisan serve"; Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; npm run dev"
```

This opens all 3 services in separate windows automatically!

---

## 📚 NEED MORE HELP?

- **Full Documentation:** `ML_INTEGRATION_GUIDE.md`
- **API Reference:** `ml-service/README.md`
- **Setup Details:** `ml-service/SETUP.md`
- **Implementation:** `IMPLEMENTATION_SUMMARY.md`

---

## 🎉 YOU'RE READY!

Once you see all three services running without errors, visit:

**http://localhost:8000**

And start using your ML-powered demand forecasting system! 🚀
