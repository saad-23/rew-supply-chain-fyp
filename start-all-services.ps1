# PowerShell script to start all services in separate windows
# Usage: .\start-all-services.ps1

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  REW Optimized - Starting All Services" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Activate virtual environment first
Write-Host "Activating Python virtual environment..." -ForegroundColor Yellow
& "$PWD\.venv\Scripts\Activate.ps1"

# Start ML Service in new window
Write-Host "Starting ML Service..." -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; & .\.venv\Scripts\Activate.ps1; cd ml-service; python app.py"

# Wait a moment for ML service to start
Start-Sleep -Seconds 2

# Start Laravel in new window
Write-Host "Starting Laravel..." -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; php artisan serve"

# Wait a moment for Laravel to start
Start-Sleep -Seconds 2

# Start Vite in new window
Write-Host "Starting Vite..." -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; npm run dev"

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "✓ All services starting in separate windows" -ForegroundColor Green
Write-Host ""
Write-Host "Services:" -ForegroundColor Cyan
Write-Host "  • ML Service:  http://localhost:5000" -ForegroundColor White
Write-Host "  • Laravel:     http://localhost:8000" -ForegroundColor White
Write-Host "  • Vite:        http://localhost:5173" -ForegroundColor White
Write-Host ""
Write-Host "Close the terminal windows to stop services" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan
