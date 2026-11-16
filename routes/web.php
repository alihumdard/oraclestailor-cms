<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\TailorDashboardController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TailorDashboardController::class, 'booking'])->name('home');
Route::post('/track', [TailorDashboardController::class, 'trackOrder'])->middleware('throttle:8,1')->name('orders.track');
Route::post('/contact', [TailorDashboardController::class, 'storeInquiry'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/privacy', [LegalPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [LegalPageController::class, 'terms'])->name('terms');
Route::get('/returns', [LegalPageController::class, 'returns'])->name('returns');

// Tailor (admin/staff) authentication
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:6,1')->name('login.store');
Route::get('/setup', [AuthController::class, 'setup'])->middleware('throttle:10,1')->name('setup');
Route::post('/setup', [AuthController::class, 'createOwner'])->middleware('throttle:3,1')->name('setup.store');

// Customer (shop account) authentication
Route::get('/account/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/account/register', [CustomerAuthController::class, 'register'])->middleware('throttle:6,1')->name('customer.register.store');
Route::get('/account/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/account/login', [CustomerAuthController::class, 'login'])->middleware('throttle:8,1')->name('customer.login.store');
Route::post('/account/logout', [CustomerAuthController::class, 'logout'])->middleware('auth:customer')->name('customer.logout');

Route::middleware('auth:customer')->group(function (): void {
    Route::get('/account', [CustomerPortalController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/account/order/new', [CustomerPortalController::class, 'createOrder'])->name('customer.order.create');
    Route::post('/account/orders', [CustomerPortalController::class, 'storeOrder'])->middleware('throttle:10,1')->name('customer.orders.store');
});

// Tailor workroom (admin)
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/workroom', [TailorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/workroom/board', [TailorDashboardController::class, 'board'])->name('board');
    Route::get('/workroom/reports', [TailorDashboardController::class, 'reports'])->name('reports');
    Route::get('/workroom/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/workroom/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::patch('/workroom/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/workroom/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/orders', [TailorDashboardController::class, 'store'])->name('orders.store');
    Route::post('/bookings', [TailorDashboardController::class, 'store'])->name('bookings.store');
    Route::patch('/orders/{order}/status', [TailorDashboardController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/assign', [TailorDashboardController::class, 'assign'])->name('orders.assign');
    Route::patch('/orders/bulk-status', [TailorDashboardController::class, 'bulkUpdateStatus'])->name('orders.bulk-status');
    Route::patch('/orders/{order}/price', [TailorDashboardController::class, 'updatePrice'])->name('orders.price');
    Route::get('/orders/{order}/receipt', [TailorDashboardController::class, 'receipt'])->name('orders.receipt');
    Route::patch('/inquiries/{inquiry}', [TailorDashboardController::class, 'updateInquiry'])->name('inquiries.update');
    Route::delete('/inquiries/{inquiry}', [TailorDashboardController::class, 'destroyInquiry'])->name('inquiries.destroy');
    Route::get('/workroom/customers', [CustomerManagementController::class, 'index'])->name('customers.index');
    Route::patch('/workroom/customers/{customer}/measurements', [CustomerManagementController::class, 'updateMeasurements'])->name('customers.measurements.update');
    Route::get('/workroom/settings', [TailorDashboardController::class, 'settings'])->name('settings');
    Route::patch('/workroom/settings', [TailorDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::get('/workroom/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/workroom/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/workroom/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/workroom/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});
