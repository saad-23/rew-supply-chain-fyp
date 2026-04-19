# PowerShell script to start the ML service
# Usage: .\start-ml-service.ps1

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Starting ML Forecasting Service" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Check if Python is installed
try {
    $pythonVersion = python --version 2>&1
    Write-Host "✓ Python found: $pythonVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Python not found!" -ForegroundColor Red
    Write-Host "  Please install Python 3.8+ from python.org" -ForegroundColor Yellow
    exit 1
}

# Change to ml-service directory
Set-Location -Path "ml-service"

# Check if requirements are installed
Write-Host ""
Write-Host "Checking dependencies..." -ForegroundColor Cyan

try {
    python -c "import flask" 2>$null
    python -c "import prophet" 2>$null
    python -c "import pandas" 2>$null
    Write-Host "✓ All dependencies installed" -ForegroundColor Green
} catch {
    Write-Host "✗ Missing dependencies. Installing..." -ForegroundColor Yellow
    pip install -r requirements.txt
}

# Start the ML service
Write-Host ""
Write-Host "Starting ML Service on port 5000..." -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the service" -ForegroundColor Yellow
Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

python app.py
