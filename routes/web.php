<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingCalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\FinanceOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MarketingOrderController;
use App\Http\Controllers\StorageReservationController;
use App\Http\Controllers\MarketingCustomerController;
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\OwnerReportController;
use App\Http\Controllers\QualityControlController;
use App\Http\Controllers\SystemLogController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/change-password', [AuthController::class, 'changePassword']);

/*
|--------------------------------------------------------------------------
| OWNER
|--------------------------------------------------------------------------
*/

Route::get('/owner/dashboard', [DashboardController::class, 'dbowner'])
    ->name('owner.dashboard');

Route::prefix('owner')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/create', [UserController::class, 'create']);
    Route::post('/users/store', [UserController::class, 'store']);
    Route::post('/users/update', [UserController::class, 'update']);
    Route::get('/users/reset/{id}', [UserController::class, 'resetPassword']);
});

Route::get('/owner/report', [OwnerReportController::class, 'index'])
    ->name('owner.report');

Route::get('/owner/system-log', [SystemLogController::class, 'index']);

/*
|--------------------------------------------------------------------------
| SUPERVISOR & CREATIVE
|--------------------------------------------------------------------------
*/

Route::get('/supervisor/dashboard', function () {
    return "Dashboard Supervisor";
});

Route::get('/creative/dashboard', function () {
    return "Dashboard Creative dan Design";
});

/*
|--------------------------------------------------------------------------
| CUSTOMER ORDER
|--------------------------------------------------------------------------
| Alur:
| 1. Customer buka /order
| 2. Submit form utama ke POST /order
| 3. Jika pilih tambah produk, controller redirect ke /order/add-order/{id}
| 4. Produk tambahan disimpan lewat POST /order/product/store
|--------------------------------------------------------------------------
*/

Route::get('/order', [OrderController::class, 'index']);
Route::post('/order', [OrderController::class, 'store']);

Route::get('/order/add-order/{id}', [OrderController::class, 'addProductForm']);
Route::post('/order/product/store', [OrderController::class, 'storeProduct']);

Route::get('/check-availability', [OrderController::class, 'checkAvailability']);

/*
|--------------------------------------------------------------------------
| STORAGE - EQUIPMENT
|--------------------------------------------------------------------------
*/

Route::prefix('storage')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dbstorage']);

    Route::get('/equipment', [UnitController::class, 'index']);
    Route::post('/equipment/store', [UnitController::class, 'store']);
    Route::post('/equipment/update', [UnitController::class, 'update']);
    Route::get('/equipment/delete/{id}', [UnitController::class, 'delete']);
    Route::get('/get-kode-unit/{kategori}', [UnitController::class, 'getKode']);
});

/*
|--------------------------------------------------------------------------
| MARKETING
|--------------------------------------------------------------------------
*/

Route::prefix('marketing')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dbmarketing']);

    Route::get('/orders', [MarketingOrderController::class, 'index']);

    Route::post('/orders/process/{id}', [MarketingOrderController::class, 'process']);

    Route::get('/orders/detail/{id}', [MarketingOrderController::class, 'detail']);

    Route::get('/orders/cancel/{id}', [MarketingOrderController::class, 'cancel']);
    Route::post('/orders/cancel/{id}', [MarketingOrderController::class, 'cancel']);

    Route::get('/availability-check', [AvailabilityController::class, 'checkMarketingByRange'])
        ->name('availability.marketing.check');

    Route::post('/orders/update/{id}', [MarketingOrderController::class, 'updateOrder']);

    Route::post('/orders/check-edit-availability/{id}', [MarketingOrderController::class, 'checkEditAvailability']);

    Route::get('/customers', [MarketingCustomerController::class, 'index']);
    Route::post('/customers/rate/{id}', [MarketingCustomerController::class, 'rate']);
    Route::post('/customers/update/{id}', [MarketingCustomerController::class, 'update']);
});

Route::post('/marketing/orders/invoice/{id}', [MarketingOrderController::class, 'generateInvoice']);

Route::get('/marketing/orders/download/{id}', [MarketingOrderController::class, 'downloadInvoice'])
    ->name('marketing.download.invoice');

/*
|--------------------------------------------------------------------------
| STORAGE - RESERVATION
|--------------------------------------------------------------------------
*/

Route::get('/storage/reservation-list', [StorageReservationController::class, 'index']);

Route::get('/storage/reservation-list/assign/{id}', [StorageReservationController::class, 'assign']);

Route::get('/storage/reservation-list/status/{id}/{status}', [StorageReservationController::class, 'updateStatus']);

Route::get('/storage/assignment/{id}', [StorageReservationController::class, 'showAssignPage']);

Route::post('/storage/assignment/{id}', [StorageReservationController::class, 'storeAssign']);

Route::get('/storage/reservation-detail/{id}', [StorageReservationController::class, 'detail']);

Route::post('/storage/return/{id}', [StorageReservationController::class, 'return']);

Route::post('/storage/pickup/{id}', [StorageReservationController::class, 'pickup']);

/*
|--------------------------------------------------------------------------
| QUALITY CONTROL
|--------------------------------------------------------------------------
*/

Route::get('/storage/quality-control', [QualityControlController::class, 'index']);

Route::get('/storage/quality-control/pending', [QualityControlController::class, 'pending']);

Route::get('/storage/quality-control/input/{id}', [QualityControlController::class, 'input']);

Route::post('/storage/quality-control/store/{id}', [QualityControlController::class, 'store']);

Route::get('/storage/quality-control/monthly', [QualityControlController::class, 'monthly']);

Route::post('/storage/quality-control/monthly/store', [QualityControlController::class, 'storeMonthly']);

/*
|--------------------------------------------------------------------------
| WEEKLY REPORT
|--------------------------------------------------------------------------
*/

Route::get('/weekly-report', [WeeklyReportController::class, 'index']);

Route::post('/weekly-report/store', [WeeklyReportController::class, 'store']);

Route::post('/weekly-report/update/{id}', [WeeklyReportController::class, 'update']);

Route::post('/weekly-report/delete/{id}', [WeeklyReportController::class, 'delete'])
    ->name('weekly.report.delete');

/*
|--------------------------------------------------------------------------
| BOOKING CALENDAR
|--------------------------------------------------------------------------
*/

Route::get('/calendar', [BookingCalendarController::class, 'calendar']);

Route::get('/calendar/events', [BookingCalendarController::class, 'calendarEvents']);

/*
|--------------------------------------------------------------------------
| AVAILABILITY CHECK
|--------------------------------------------------------------------------
*/

Route::get('/availability-check', [AvailabilityController::class, 'index'])
    ->name('availability.index');

Route::get('/availability-check/result', [AvailabilityController::class, 'checkByRange'])
    ->name('availability.check');

/*
|--------------------------------------------------------------------------
| FINANCE
|--------------------------------------------------------------------------
*/

Route::prefix('finance')->group(function () {
    Route::get('/dashboard', [FinanceOrderController::class, 'dashboard']);
    Route::get('/orders', [FinanceOrderController::class, 'index']);

    Route::post('/orders/dp-paid/{id}', [FinanceOrderController::class, 'markDpPaid']);
    Route::post('/orders/fully-paid/{id}', [FinanceOrderController::class, 'markFullyPaid']);

    Route::get('/orders/download-dp-invoice/{id}', [FinanceOrderController::class, 'downloadDpInvoice']);
    Route::get('/orders/download-fully-paid-invoice/{id}', [FinanceOrderController::class, 'downloadFullyPaidInvoice']);
});