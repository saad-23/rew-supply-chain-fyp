# Start ML Forecast Service with Testing
# This script starts the Python ML service and runs integration tests

Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "  REW OPTIMIZED - ML FORECASTING SERVICE STARTUP" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

# Check if we're in the right directory
$projectRoot = "c:\xampp\htdocs\rew-optimized"
$mlServicePath = Join-Path $projectRoot "ml-service"

if (-not (Test-Path $mlServicePath)) {
    Write-Host "❌ Error: ml-service directory not found!" -ForegroundColor Red
    Write-Host "   Current location: $PWD" -ForegroundColor Yellow
    Write-Host "   Expected: $mlServicePath" -ForegroundColor Yellow
    exit 1
}

Set-Location $mlServicePath
Write-Host "📂 Changed to: $mlServicePath" -ForegroundColor Green
Write-Host ""

# Check if virtual environment exists
$venvPath = Join-Path $projectRoot ".venv"
if (-not (Test-Path $venvPath)) {
    Write-Host "❌ Virtual environment not found!" -ForegroundColor Red
    Write-Host "   Creating virtual environment..." -ForegroundColor Yellow
    python -m venv $venvPath
    Write-Host "✅ Virtual environment created!" -ForegroundColor Green
}

# Activate virtual environment
Write-Host "🔧 Activating Python virtual environment..." -ForegroundColor Cyan
$activateScript = Join-Path $venvPath "Scripts\Activate.ps1"

if (Test-Path $activateScript) {
    & $activateScript
    Write-Host "✅ Virtual environment activated!" -ForegroundColor Green
} else {
    Write-Host "❌ Activation script not found!" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Check if requirements are installed
Write-Host "📦 Checking Python dependencies..." -ForegroundColor Cyan
$requirementsFile = Join-Path $mlServicePath "requirements.txt"

if (Test-Path $requirementsFile) {
    Write-Host "   Installing/updating dependencies..." -ForegroundColor Yellow
    pip install -r $requirementsFile --quiet
    Write-Host "✅ Dependencies installed!" -ForegroundColor Green
} else {
    Write-Host "⚠️  requirements.txt not found, skipping..." -ForegroundColor Yellow
}

Write-Host ""

# Check .env file
Write-Host "⚙️  Checking configuration..." -ForegroundColor Cyan
$envFile = Join-Path $mlServicePath ".env"

if (Test-Path $envFile) {
    Write-Host "✅ Configuration file found!" -ForegroundColor Green
    
    # Read and display config
    $envContent = Get-Content $envFile
    $dbHost = ($envContent | Select-String "DB_HOST=").ToString().Split("=")[1]
    $dbName = ($envContent | Select-String "DB_DATABASE=").ToString().Split("=")[1]
    
    Write-Host "   Database Host: $dbHost" -ForegroundColor Gray
    Write-Host "   Database Name: $dbName" -ForegroundColor Gray
} else {
    Write-Host "⚠️  .env file not found!" -ForegroundColor Yellow
    Write-Host "   Creating default .env..." -ForegroundColor Yellow
    
    @"
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=rew_optimized
"@ | Out-File -FilePath $envFile -Encoding UTF8
    
    Write-Host "✅ Default .env created!" -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "  STARTING ML SERVICE" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "   Service URL: http://localhost:5000" -ForegroundColor Green
Write-Host "   Health Check: http://localhost:5000/api/health" -ForegroundColor Green
Write-Host "   API Docs: http://localhost:5000/" -ForegroundColor Green
Write-Host ""
Write-Host "   Press Ctrl+C to stop the service" -ForegroundColor Yellow
Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

# Start Flask app
python app.py
