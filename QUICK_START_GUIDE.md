# Quick Start Guide - Development Environment

## 🚀 Starting the Application

### **Method 1: Artisan Command (Recommended - Single Terminal)**
```bash
php artisan dev:serve
```
Automatically starts:
- ✅ ML Service (Port 5000)
- ✅ Laravel Server (Port 8000)

---

### **Method 2: PowerShell Script (Separate Windows)**
```powershell
.\start-all-services.ps1
```
Opens separate terminal windows for:
- ML Service (Port 5000)
- Laravel (Port 8000)
- Vite Dev Server (Port 5173)

---

### **Method 3: Batch File (Quick Start)**
```bash
start.bat
```
Double-click or run from command prompt. Same as Method 1.

---

### **Method 4: Manual Control**

**Start ML Service:**
```bash
php artisan ml:service start
```

**Check ML Service Status:**
```bash
php artisan ml:service status
```

**Stop ML Service:**
```bash
php artisan ml:service stop
```

**Restart ML Service:**
```bash
php artisan ml:service restart
```

**Start Laravel Only:**
```bash
php artisan serve
```

---

## 📋 Available Services

| Service | URL | Description |
|---------|-----|-------------|
| **Laravel** | http://localhost:8000 | Main application |
| **ML Service** | http://localhost:5000 | Python forecasting API |
| **ML Health** | http://localhost:5000/api/health | Service health check |
| **Vite** | http://localhost:5173 | Frontend dev server |

---

## 🔧 Troubleshooting

### ML Service won't start?
```bash
# Check status
php artisan ml:service status

# Try manual start
cd ml-service
python app.py
```

### Port already in use?
```bash
# Stop ML Service
php artisan ml:service stop

# Or kill manually (Windows)
netstat -ano | findstr :5000
taskkill /F /PID <PID>
```

### Check logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# ML Service logs (if started via Artisan)
tail -f ml-service/ml-service.log
```

---

## 🎯 Production Deployment

For production, use:
- **Supervisor** (Linux) - Keeps ML service running
- **Windows Service** (Windows) - Background service
- **Docker** - Containerized deployment

See `DEPLOYMENT.md` for details.

---

## 📞 Quick Commands Reference

```bash
# Development
php artisan dev:serve              # Start everything
.\start-all-services.ps1           # Separate windows

# ML Service Management
php artisan ml:service start       # Start ML service
php artisan ml:service stop        # Stop ML service  
php artisan ml:service status      # Check status
php artisan ml:service restart     # Restart service

# Database
php artisan migrate               # Run migrations
php artisan db:seed              # Seed database

# Cache
php artisan optimize:clear       # Clear all caches
```

---

## ✅ First Time Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate
php artisan db:seed

# 4. Install Python dependencies
cd ml-service
pip install -r requirements.txt
cd ..

# 5. Start everything!
php artisan dev:serve
```

Done! 🎉
