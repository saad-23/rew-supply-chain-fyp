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

// Auth Routes
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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // New Admin Dashboard
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Products Routes
    Route::get('/products', ProductList::class)->name('products.index');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{productId}/edit', ProductForm::class)->name('products.edit');

    // Supply Chain Analytics (FR3, FR4, FR7)
    Route::get('/forecast', \App\Livewire\Analytics\ForecastDashboard::class)->name('analytics.forecast');

    // Route Optimization (FR5)
    Route::get('/routes', \App\Livewire\Logistics\RoutePlanner::class)->name('logistics.routes');

    // Anomaly Detection & Alerts (FR6, FR8)
    Route::get('/alerts', \App\Livewire\Analytics\AlertsDashboard::class)->name('analytics.alerts');

    // Operations (Data Entry)
    Route::get('/operations/sales', \App\Livewire\Operations\RecordSale::class)->name('operations.sales');
    Route::get('/operations/create-delivery', \App\Livewire\Operations\CreateDelivery::class)->name('operations.create-delivery');
    Route::get('/operations/manage-deliveries', \App\Livewire\Operations\ManageDeliveries::class)->name('operations.manage-deliveries');
    
    // Legacy route alias for backward compatibility
    Route::get('/operations/delivery', \App\Livewire\Operations\CreateDelivery::class)->name('operations.delivery');

    // System Administration
    Route::get('/settings', \App\Livewire\Settings\GeneralSettings::class)->name('settings');
    Route::get('/categories', \App\Livewire\Catalog\ManageCategories::class)->name('catalog.categories');
    Route::get('/profile', \App\Livewire\Profile\UserProfile::class)->name('profile');

    // Print Receipt
    Route::get('/sales/{sale}/receipt', function (\App\Models\Sale $sale) {
        return view('sales.receipt', compact('sale'));
    })->name('sales.receipt');
});

