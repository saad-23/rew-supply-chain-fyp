<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Products\ProductList;
use App\Livewire\Products\ProductForm;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Auth Routes (unauthenticated) ───────────────────────────────────────────
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ─── Shared Routes (Admin + Staff) ───────────────────────────────────────────
Route::middleware(['auth', 'role:admin,staff,manager'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Main dashboard — both roles land here after login
    Route::get('/admin', \App\Livewire\AdminDashboard::class)->name('admin.dashboard');

    // Staff dashboard alias (staff redirected here, renders same component)
    Route::get('/staff/dashboard', \App\Livewire\AdminDashboard::class)->name('staff.dashboard');

    // Products
    Route::get('/products', ProductList::class)->name('products.index');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{productId}/edit', ProductForm::class)->name('products.edit');

    // Analytics
    Route::get('/forecast', \App\Livewire\Analytics\ForecastDashboard::class)->name('analytics.forecast');
    Route::get('/alerts', \App\Livewire\Analytics\AlertsDashboard::class)->name('analytics.alerts');

    // Logistics
    Route::get('/routes', \App\Livewire\Logistics\RoutePlanner::class)->name('logistics.routes');

    // Operations
    Route::get('/operations/sales', \App\Livewire\Operations\RecordSale::class)->name('operations.sales');
    Route::get('/operations/create-delivery', \App\Livewire\Operations\CreateDelivery::class)->name('operations.create-delivery');
    Route::get('/operations/manage-deliveries', \App\Livewire\Operations\ManageDeliveries::class)->name('operations.manage-deliveries');
    Route::get('/operations/delivery', \App\Livewire\Operations\CreateDelivery::class)->name('operations.delivery');

    // Google Maps server-side proxy (bypasses API key HTTP referrer restrictions)
    Route::get('/maps/autocomplete', function (\Illuminate\Http\Request $request) {
        $q = trim($request->get('q', ''));
        $service = new \App\Services\GoogleMapsService();
        return response()->json($service->placesAutocomplete($q));
    })->name('maps.autocomplete');

    Route::get('/maps/place', function (\Illuminate\Http\Request $request) {
        $placeId = trim($request->get('id', ''));
        $service = new \App\Services\GoogleMapsService();
        return response()->json($service->placeDetails($placeId));
    })->name('maps.place');

    // Profile (every authenticated user)
    Route::get('/profile', \App\Livewire\Profile\UserProfile::class)->name('profile');

    // Print Receipt
    Route::get('/sales/{sale}/receipt', function (\App\Models\Sale $sale) {
        return view('sales.receipt', compact('sale'));
    })->name('sales.receipt');
});

// ─── Admin-Only Routes ────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // System settings
    Route::get('/settings', \App\Livewire\Settings\GeneralSettings::class)->name('settings');

    // Catalog / categories
    Route::get('/categories', \App\Livewire\Catalog\ManageCategories::class)->name('catalog.categories');

    // Staff management
    Route::get('/admin/staff', \App\Livewire\Admin\StaffManagement::class)->name('admin.staff');
});

