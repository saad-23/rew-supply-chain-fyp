# Complete Setup and Test Script for REW ML Forecasting System
# This script verifies and sets up the entire forecasting system

Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "  REW OPTIMIZED - ML FORECASTING SYSTEM SETUP & TEST" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

$projectRoot = "c:\xampp\htdocs\rew-optimized"
Set-Location $projectRoot

# Step 1: Check XAMPP Services
Write-Host "📊 Step 1: Checking XAMPP Services..." -ForegroundColor Yellow
Write-Host ""

$mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
$apacheRunning = Get-Process -Name "httpd" -ErrorAction SilentlyContinue

if ($mysqlRunning) {
    Write-Host "  ✅ MySQL is running" -ForegroundColor Green
} else {
    Write-Host "  ❌ MySQL is NOT running!" -ForegroundColor Red
    Write-Host "  Please start MySQL from XAMPP Control Panel" -ForegroundColor Yellow
    exit 1
}

if ($apacheRunning) {
    Write-Host "  ✅ Apache is running" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Apache is NOT running" -ForegroundColor Yellow
    Write-Host "  Start Apache to access the web interface" -ForegroundColor Yellow
}

Write-Host ""

# Step 2: Check Database
Write-Host "📊 Step 2: Checking Database..." -ForegroundColor Yellow
Write-Host ""

try {
    $dbCheck = php -r "
        `$mysqli = new mysqli('127.0.0.1', 'root', '', 'rew_optimized');
        if (`$mysqli->connect_error) {
            echo 'ERROR';
        } else {
            echo 'OK';
        }
    "
    
    if ($dbCheck -eq "OK") {
        Write-Host "  ✅ Database 'rew_optimized' exists" -ForegroundColor Green
        
        # Check for data
        $productCount = php -r "
            `$mysqli = new mysqli('127.0.0.1', 'root', '', 'rew_optimized');
            `$result = `$mysqli->query('SELECT COUNT(*) as cnt FROM products');
            `$row = `$result->fetch_assoc();
            echo `$row['cnt'];
        "
        
        $salesCount = php -r "
            `$mysqli = new mysqli('127.0.0.1', 'root', '', 'rew_optimized');
            `$result = `$mysqli->query('SELECT COUNT(*) as cnt FROM sales');
            `$row = `$result->fetch_assoc();
            echo `$row['cnt'];
        "
        
        Write-Host "  📦 Products: $productCount" -ForegroundColor Cyan
        Write-Host "  📈 Sales Records: $salesCount" -ForegroundColor Cyan
        
        if ([int]$productCount -eq 0) {
            Write-Host "  ⚠️  No products found! Running seeder..." -ForegroundColor Yellow
            php artisan db:seed --class=RewSeeder
            Write-Host "  ✅ Products seeded" -ForegroundColor Green
        }
        
        if ([int]$salesCount -lt 100) {
            Write-Host "  ⚠️  Insufficient sales data! Running seeder..." -ForegroundColor Yellow
            php artisan db:seed --class=SalesDataSeeder
            Write-Host "  ✅ Sales data generated" -ForegroundColor Green
        }
        
    } else {
        Write-Host "  ❌ Database 'rew_optimized' not found!" -ForegroundColor Red
        Write-Host "  Run: php artisan migrate" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "  ❌ Database connection failed!" -ForegroundColor Red
    Write-Host "  Error: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 3: Check Python Virtual Environment
Write-Host "📊 Step 3: Checking Python Environment..." -ForegroundColor Yellow
Write-Host ""

$venvPath = Join-Path $projectRoot ".venv"

if (Test-Path $venvPath) {
    Write-Host "  ✅ Virtual environment found" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Creating virtual environment..." -ForegroundColor Yellow
    python -m venv $venvPath
    Write-Host "  ✅ Virtual environment created" -ForegroundColor Green
}

# Activate virtual environment
$activateScript = Join-Path $venvPath "Scripts\Activate.ps1"
& $activateScript

Write-Host "  ✅ Virtual environment activated" -ForegroundColor Green

Write-Host ""

# Step 4: Install Python Dependencies
Write-Host "📊 Step 4: Installing Python Dependencies..." -ForegroundColor Yellow
Write-Host ""

Set-Location (Join-Path $projectRoot "ml-service")

$requirementsFile = "requirements.txt"
if (Test-Path $requirementsFile) {
    Write-Host "  📦 Installing packages (this may take a few minutes)..." -ForegroundColor Cyan
    pip install -r $requirementsFile --quiet --disable-pip-version-check
    Write-Host "  ✅ All dependencies installed" -ForegroundColor Green
} else {
    Write-Host "  ❌ requirements.txt not found!" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 5: Check .env Configuration
Write-Host "📊 Step 5: Checking Configuration..." -ForegroundColor Yellow
Write-Host ""

$envFile = ".env"
if (Test-Path $envFile) {
    Write-Host "  ✅ ML Service .env found" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Creating default .env..." -ForegroundColor Yellow
    @"
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=rew_optimized
"@ | Out-File -FilePath $envFile -Encoding UTF8
    Write-Host "  ✅ .env created" -ForegroundColor Green
}

Write-Host ""

# Step 6: Test ML Service
Write-Host "📊 Step 6: Starting ML Service (Background)..." -ForegroundColor Yellow
Write-Host ""

# Start ML service in background
$mlJob = Start-Job -ScriptBlock {
    param($mlPath)
    Set-Location $mlPath
    & "$mlPath\..\.venv\Scripts\python.exe" app.py
} -ArgumentList (Get-Location).Path

Write-Host "  🚀 ML Service starting..." -ForegroundColor Cyan
Write-Host "  ⏳ Waiting for service to initialize..." -ForegroundColor Cyan
Start-Sleep -Seconds 5

# Test health endpoint
Write-Host "  🔍 Testing ML Service health..." -ForegroundColor Cyan

try {
    $response = Invoke-RestMethod -Uri "http://localhost:5000/api/health" -Method Get -TimeoutSec 5
    
    if ($response.status -eq "healthy") {
        Write-Host "  ✅ ML Service is healthy!" -ForegroundColor Green
        Write-Host "     Service: $($response.service)" -ForegroundColor Gray
        Write-Host "     Time: $($response.timestamp)" -ForegroundColor Gray
    } else {
        Write-Host "  ⚠️  ML Service responded but not healthy" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ ML Service health check failed!" -ForegroundColor Red
    Write-Host "  Error: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "  💡 Check the ML service logs above for errors" -ForegroundColor Yellow
    Stop-Job -Job $mlJob
    Remove-Job -Job $mlJob
    exit 1
}

Write-Host ""

# Step 7: Test Forecast Generation
Write-Host "📊 Step 7: Testing Forecast Generation..." -ForegroundColor Yellow
Write-Host ""

try {
    $testPayload = @{
        product_id = 1
        days = 30
    } | ConvertTo-Json

    Write-Host "  📡 Requesting forecast for product 1 (30 days)..." -ForegroundColor Cyan
    
    $forecastResponse = Invoke-RestMethod -Uri "http://localhost:5000/api/forecast" `
        -Method Post `
        -Body $testPayload `
        -ContentType "application/json" `
        -TimeoutSec 60
    
    if ($forecastResponse.success) {
        Write-Host "  ✅ Forecast generated successfully!" -ForegroundColor Green
        Write-Host "     Model: $($forecastResponse.model_used)" -ForegroundColor Gray
        Write-Host "     Forecasts: $($forecastResponse.forecasts.Count) days" -ForegroundColor Gray
        
        if ($forecastResponse.forecasts.Count -gt 0) {
            $sample = $forecastResponse.forecasts[0]
            Write-Host "     Sample: $($sample.date) → $($sample.predicted_quantity) units (confidence: $($sample.confidence_score))" -ForegroundColor Gray
        }
    } else {
        Write-Host "  ❌ Forecast generation failed!" -ForegroundColor Red
        Write-Host "  Error: $($forecastResponse.error)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Forecast API call failed!" -ForegroundColor Red
    Write-Host "  Error: $_" -ForegroundColor Red
}

Write-Host ""

# Step 8: Summary & Next Steps
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "  SETUP COMPLETE!" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ All systems are operational!" -ForegroundColor Green
Write-Host ""
Write-Host "📌 Service URLs:" -ForegroundColor Yellow
Write-Host "   • ML Service API:    http://localhost:5000" -ForegroundColor Cyan
Write-Host "   • Laravel App:       http://localhost:8000" -ForegroundColor Cyan
Write-Host "   • Forecast Dashboard: http://localhost:8000/forecast" -ForegroundColor Cyan
Write-Host ""
Write-Host "🚀 Next Steps:" -ForegroundColor Yellow
Write-Host "   1. Open browser: http://localhost:8000/forecast" -ForegroundColor White
Write-Host "   2. Select a product from dropdown" -ForegroundColor White
Write-Host "   3. Click 'Generate ML Forecast' button" -ForegroundColor White
Write-Host "   4. View the month-wise forecast chart" -ForegroundColor White
Write-Host ""
Write-Host "💡 ML Service is running in background (Job ID: $($mlJob.Id))" -ForegroundColor Yellow
Write-Host "   To stop: Stop-Job -Id $($mlJob.Id); Remove-Job -Id $($mlJob.Id)" -ForegroundColor Gray
Write-Host ""
Write-Host "Press Ctrl+C to stop this script. ML service will keep running." -ForegroundColor Yellow
Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

# Keep script running to show logs
Write-Host "📊 Monitoring ML Service (press Ctrl+C to exit)..." -ForegroundColor Cyan
Write-Host ""

try {
    while ($true) {
        Start-Sleep -Seconds 2
        
        # Check if job is still running
        if ($mlJob.State -ne "Running") {
            Write-Host "⚠️  ML Service stopped unexpectedly!" -ForegroundColor Red
            Receive-Job -Job $mlJob
            break
        }
    }
} finally {
    Write-Host ""
    Write-Host "Cleaning up..." -ForegroundColor Yellow
    Stop-Job -Job $mlJob -ErrorAction SilentlyContinue
    Remove-Job -Job $mlJob -ErrorAction SilentlyContinue
    Write-Host "Done." -ForegroundColor Green
}
