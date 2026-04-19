# Setup Status & Testing Guide

**Last Verified**: April 6, 2026

---

## ✅ Installation Status

### Composer Packages (PHP)
**Status**: ✅ INSTALLED
- Laravel Framework 12.0
- All dependencies in `vendor/` folder

### NPM Packages (JavaScript)
**Status**: ❌ **NOT INSTALLED** - REQUIRED
- Tailwind CSS v4
- Vite bundler
- Laravel Vite Plugin
- Axios

**To Install NPM packages:**
```bash
cd "c:\xampp\htdocs\restaurant-app"
npm install
npm run build
```

---

## Database Setup

### Configuration
- **Connection Type**: MySQL
- **Host**: 127.0.0.1 (localhost)
- **Port**: 3306
- **Database Name**: `restaurant_core_db`
- **Username**: root
- **Password**: (empty)

### Migrations Created
✅ Migrations exist in `database/migrations/`:
- `create_users_table.php`
- `create_cache_table.php`
- `create_jobs_table.php`
- `create_restaurants_table.php`
- `create_restaurant_areas_table.php`
- `create_restaurant_tables_table.php`
- `create_menus_table.php`
- `create_menu_items_table.php`
- `create_slots_table.php`

**To Run Migrations:**
```bash
cd "c:\xampp\htdocs\restaurant-app"
php artisan migrate
```

---

## 🚀 How to Run & Test

### Option 1: Full Development Environment (Recommended)
```bash
cd "c:\xampp\htdocs\restaurant-app"
npm run dev
```

This will:
- Start PHP development server on `http://localhost:8000`
- Start Vite dev server for hot module reloading
- Watch CSS/JS changes

### Option 2: Manual Steps
```bash
# Terminal 1 - PHP Server (already running)
php artisan serve

# Terminal 2 - Vite Build Watch
npm run dev

# Terminal 3 - Optional: Queue Worker
php artisan queue:listen
```

### Option 3: Production Build
```bash
npm run build
```

---

## Testing Progress

### Test URLs (After Running Migrations)

🔐 **Authentication**
```
http://localhost:8000/login
http://localhost:8000/register
```

👨‍💼 **Admin Dashboard**
```
http://localhost:8000/admin/dashboard
```

🏪 **Owner Dashboard**
```
http://localhost:8000/owner/dashboard
```

🍽️ **Restaurant Management**
```
http://localhost:8000/restaurant/create         (Create new)
http://localhost:8000/restaurant/{id}/show      (View details)
http://localhost:8000/restaurant/{id}/edit      (Edit)
http://localhost:8000/restaurant/{id}/areas     (Manage areas)
http://localhost:8000/restaurant/{id}/tables    (Manage tables)
```

📋 **Menu Management**
```
http://localhost:8000/restaurant/{id}/menu              (List)
http://localhost:8000/restaurant/{id}/menu/create       (Create)
http://localhost:8000/restaurant/{id}/menu/{mid}/show   (View)
```

⏰ **Booking Slots**
```
http://localhost:8000/restaurant/{id}/slot             (Manage)
```

⏳ **Admin Pending**
```
http://localhost:8000/admin/pending                     (Review requests)
```

---

## Design System Testing

### What to Check
1. ✅ Colors match design-theme (purple primary #702f7e, orange secondary)
2. ✅ Buttons have correct styling (primary blue, secondary gray, danger red)
3. ✅ Forms display properly (input focus rings, error states)
4. ✅ Badges show correct variants (approved green, pending yellow, rejected red)
5. ✅ Stat cards have gradient backgrounds
6. ✅ Cards have hover effects with shadows
7. ✅ Material Icons appear correctly
8. ✅ Dark mode works if enabled

### CSS Validation
All component classes defined in `resources/css/app.css` @layer components section.

---

## Environment Files

### .env (Current)
```
DATABASE: restaurant_core_db
HOST: localhost
USER: root
PASSWORD: (empty)
```

**If needed, update:**
```bash
cp .env.example .env
php artisan key:generate
```

---

## Quick Troubleshooting

### Issue: `npm run dev` not working
**Solution**: 
```bash
npm install
npm run build
```

### Issue: Database connection fails
**Solution**: 
1. Ensure MySQL is running in XAMPP
2. Create database: `CREATE DATABASE restaurant_core_db;`
3. Run migrations: `php artisan migrate`

### Issue: Vite not compiling CSS
**Solution**:
```bash
npm install
npm run build
```

### Issue: Getting 404 on routes
**Solution**: 
1. Check `routes/web.php` has the routes defined
2. Run `php artisan route:list` to see all available routes

---

## Next Steps

1. **Install NPM packages**: `npm install && npm run build`
2. **Run migrations**: `php artisan migrate` (if not done)
3. **Start server**: `php artisan serve` (already running)
4. **Start Vite**: `npm run dev` (in another terminal)
5. **Visit**: http://localhost:8000
6. **Test design**: Verify components render correctly
7. **Create test data**: Log in and create restaurants/menus/slots

---

## File Structure

```
resources/
├── css/
│   └── app.css              ← All component styling (updated ✅)
├── js/
│   ├── app.js               ← Orchestrator (updated ✅)
│   ├── bootstrap.js         ← Axios/plugins (updated ✅)
│   └── modules/
│       ├── ui.js            ← UI interactions (updated ✅)
│       ├── forms.js         ← Form handlers (updated ✅)
│       ├── api.js           ← API calls (updated ✅)
│       └── utils.js         ← Utilities (updated ✅)
└── views/                   ← Blade templates
    ├── layouts/
    │   └── app.blade.php
    ├── auth/
    │   ├── login.blade.php          ✅ Updated
    │   └── register.blade.php        📝 Pending
    ├── admin/
    │   ├── dashboard.blade.php      ✅ Updated
    │   └── pending.blade.php         📝 Pending
    ├── owner/
    │   └── dashboard.blade.php      ✅ Updated
    └── restaurant/
        ├── create.blade.php          ✅ Updated
        ├── show.blade.php            📝 Pending
        ├── edit.blade.php            📝 Pending
        ├── areas.blade.php           📝 Pending
        ├── tables.blade.php          📝 Pending
    └── menu/
        ├── index.blade.php           📝 Pending
        ├── create.blade.php          📝 Pending
        └── show.blade.php            📝 Pending
    └── slot/
        └── index.blade.php           📝 Pending
```

---

**Status Summary**: 
- ✅ PHP/Composer: Ready
- ❌ NPM/Node: Needs installation
- ⏳ Database: Migrations exist, need to run
- ✅ CSS: Complete with design-theme
- 🟡 Blade Views: 4 of 10+ updated, 6+ pending
