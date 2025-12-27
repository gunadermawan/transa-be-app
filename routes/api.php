<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
// get outlet by user
Route::get('/my-outlet', [App\Http\Controllers\Api\AuthController::class, 'getOutletByUser'])->middleware('auth:sanctum');

// me
Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->middleware('auth:sanctum');

// add manager - only business owner (role_id: 1)
Route::post('/add-manager', [App\Http\Controllers\Api\AuthController::class, 'addManager'])->middleware(['auth:sanctum', 'role:1']);

// staff
Route::get('/get-staff/{businessId}', [App\Http\Controllers\Api\StaffController::class, 'getStaff'])->middleware('auth:sanctum');
// add staff - owner and manager (role_id: 1,2)
Route::post('/add-staff', [App\Http\Controllers\Api\StaffController::class, 'addStaff'])->middleware(['auth:sanctum', 'role:1,2']);
// edit staff - owner and manager (role_id: 1,2)
Route::put('/edit-staff/{id}', [App\Http\Controllers\Api\StaffController::class, 'editStaff'])->middleware(['auth:sanctum', 'role:1,2']);

// outlets - only business owner (role_id: 1)
Route::post('/add-outlet', [App\Http\Controllers\Api\OutletController::class, 'addOutlet'])->middleware(['auth:sanctum', 'role:1']);
Route::put('/update-outlet/{id}', [App\Http\Controllers\Api\OutletController::class, 'updateOutlet'])->middleware(['auth:sanctum', 'role:1']);
Route::get('/get-outlets/{businessId}', [App\Http\Controllers\Api\OutletController::class, 'getOutlets'])->middleware('auth:sanctum');

// categories
Route::post('/add-category', [App\Http\Controllers\Api\CategoryController::class, 'addCategory'])->middleware(['auth:sanctum', 'role:1,2']);
Route::get('/get-categories', [App\Http\Controllers\Api\CategoryController::class, 'getCategories'])->middleware('auth:sanctum');
Route::put('/update-category/{id}', [App\Http\Controllers\Api\CategoryController::class, 'updateCategory'])->middleware(['auth:sanctum', 'role:1,2']);

// products
Route::post('/add-product', [App\Http\Controllers\Api\ProductController::class, 'addProduct'])->middleware(['auth:sanctum', 'role:1,2']);
Route::put('/update-product/{id}', [App\Http\Controllers\Api\ProductController::class, 'updateProduct'])->middleware(['auth:sanctum', 'role:1,2']);
Route::post('/update-product-with-image/{id}', [App\Http\Controllers\Api\ProductController::class, 'updateProductWithImage'])->middleware(['auth:sanctum', 'role:1,2']);
Route::get('/get-products', [App\Http\Controllers\Api\ProductController::class, 'getProducts'])->middleware('auth:sanctum');
Route::get('/get-product/{id}', [App\Http\Controllers\Api\ProductController::class, 'getProduct'])->middleware('auth:sanctum');
Route::delete('/delete-product/{id}', [App\Http\Controllers\Api\ProductController::class, 'deleteProduct'])->middleware(['auth:sanctum', 'role:1,2']);

// stocks - owner and manager can manage stocks (role_id: 1,2)
Route::post('/add-stock', [App\Http\Controllers\Api\StockController::class, 'addStock'])->middleware(['auth:sanctum', 'role:1,2']);
Route::put('/update-stock/{id}', [App\Http\Controllers\Api\StockController::class, 'updateStock'])->middleware(['auth:sanctum', 'role:1,2']);
Route::get('/get-stocks', [App\Http\Controllers\Api\StockController::class, 'getStocks'])->middleware('auth:sanctum');
Route::get('/get-stock/{id}', [App\Http\Controllers\Api\StockController::class, 'getStock'])->middleware('auth:sanctum');
Route::delete('/delete-stock/{id}', [App\Http\Controllers\Api\StockController::class, 'deleteStock'])->middleware(['auth:sanctum', 'role:1,2']);

// orders - all roles can create orders
Route::post('/add-order', [App\Http\Controllers\Api\OrderController::class, 'addOrder'])->middleware('auth:sanctum');
Route::get('/get-orders', [App\Http\Controllers\Api\OrderController::class, 'getOrders'])->middleware('auth:sanctum');
Route::get('/get-order/{id}', [App\Http\Controllers\Api\OrderController::class, 'getOrder'])->middleware('auth:sanctum');
// void order - only owner and manager (role_id: 1,2)
Route::delete('/delete-order/{id}', [App\Http\Controllers\Api\OrderController::class, 'deleteOrder'])->middleware(['auth:sanctum', 'role:1,2']);

// get order by outlet id
Route::get('/get-orders-by-outlet/{id}', [App\Http\Controllers\Api\OrderController::class, 'getOrdersByOutlet'])->middleware('auth:sanctum');

// printers
Route::post('/add-printer', [App\Http\Controllers\Api\PrinterController::class, 'addPrinter'])->middleware('auth:sanctum');
Route::get('/get-printers-by-outlet/{outlet_id}', [App\Http\Controllers\Api\PrinterController::class, 'getPrintersByOutlet'])->middleware('auth:sanctum');
Route::delete('/delete-printer/{id}', [App\Http\Controllers\Api\PrinterController::class, 'deletePrinter'])->middleware('auth:sanctum');

// business settings
Route::post('/add-business-setting', [App\Http\Controllers\Api\BusinessSettingController::class, 'addBusinessSetting'])->middleware('auth:sanctum');
Route::get('/get-business-settings-by-business/{business_id}', [App\Http\Controllers\Api\BusinessSettingController::class, 'getBusinessSettingsByBusiness'])->middleware('auth:sanctum');
Route::put('/update-business-setting/{id}', [App\Http\Controllers\Api\BusinessSettingController::class, 'updateBusinessSetting'])->middleware('auth:sanctum');
// delete
Route::delete('/delete-business-setting/{id}', [App\Http\Controllers\Api\BusinessSettingController::class, 'deleteBusinessSetting'])->middleware('auth:sanctum');

// sales report
Route::post('/get-daily-sales-report', [App\Http\Controllers\Api\SalesReportController::class, 'getDailySalesReport'])->middleware('auth:sanctum');

// dashboard
Route::get('/dashboard', [App\Http\Controllers\Api\DashboardController::class, 'getStats'])->middleware('auth:sanctum');

// receipts
Route::get('/receipts/{orderId}', [App\Http\Controllers\Api\ReceiptController::class, 'getReceipt'])->middleware('auth:sanctum');
Route::post('/receipts/batch', [App\Http\Controllers\Api\ReceiptController::class, 'getReceipts'])->middleware('auth:sanctum');

// multi-outlet
Route::get('/my-outlets', [App\Http\Controllers\Api\MultiOutletController::class, 'getMyOutlets'])->middleware('auth:sanctum');
Route::post('/switch-outlet', [App\Http\Controllers\Api\MultiOutletController::class, 'switchOutlet'])->middleware('auth:sanctum');
Route::get('/cross-outlet-dashboard', [App\Http\Controllers\Api\MultiOutletController::class, 'crossOutletDashboard'])->middleware('auth:sanctum');
Route::get('/outlet-comparison', [App\Http\Controllers\Api\MultiOutletController::class, 'outletComparison'])->middleware('auth:sanctum');
Route::get('/outlet-ranking', [App\Http\Controllers\Api\MultiOutletController::class, 'outletRanking'])->middleware('auth:sanctum');

// subscription management
Route::get('/subscription/plans', [App\Http\Controllers\Api\SubscriptionController::class, 'getPlans']);
Route::get('/subscription/current', [App\Http\Controllers\Api\SubscriptionController::class, 'getCurrentSubscription'])->middleware('auth:sanctum');
Route::get('/subscription/usage', [App\Http\Controllers\Api\SubscriptionController::class, 'getUsage'])->middleware('auth:sanctum');
Route::post('/subscription/change-plan', [App\Http\Controllers\Api\SubscriptionController::class, 'changePlan'])->middleware(['auth:sanctum', 'role:1']);
Route::post('/subscription/check-limit', [App\Http\Controllers\Api\SubscriptionController::class, 'checkLimit'])->middleware('auth:sanctum');

// payment - midtrans integration
Route::post('/payment/initiate', [App\Http\Controllers\Api\PaymentController::class, 'initiatePayment'])->middleware(['auth:sanctum', 'role:1']);
Route::post('/payment/webhook', [App\Http\Controllers\Api\PaymentController::class, 'webhook']);
Route::post('/payment/check-status', [App\Http\Controllers\Api\PaymentController::class, 'checkStatus'])->middleware('auth:sanctum');
Route::get('/payment/finish', [App\Http\Controllers\Api\PaymentController::class, 'finish']);

// quick service - dine in & take away
Route::prefix('quick-service')->middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [App\Http\Controllers\Api\QuickServiceController::class, 'index']);
    Route::post('/orders', [App\Http\Controllers\Api\QuickServiceController::class, 'store']);
    Route::get('/orders/{id}', [App\Http\Controllers\Api\QuickServiceController::class, 'show']);
    Route::put('/orders/{id}', [App\Http\Controllers\Api\QuickServiceController::class, 'update']);
    Route::delete('/orders/{id}', [App\Http\Controllers\Api\QuickServiceController::class, 'destroy']);

    Route::post('/orders/{orderId}/items', [App\Http\Controllers\Api\QuickServiceController::class, 'addItem']);
    Route::put('/orders/{orderId}/items/{itemId}', [App\Http\Controllers\Api\QuickServiceController::class, 'updateItem']);
    Route::delete('/orders/{orderId}/items/{itemId}', [App\Http\Controllers\Api\QuickServiceController::class, 'deleteItem']);

    Route::post('/orders/{id}/save', [App\Http\Controllers\Api\QuickServiceController::class, 'save']);
    Route::post('/orders/{id}/payments', [App\Http\Controllers\Api\QuickServiceController::class, 'processPayment']);
    Route::post('/orders/{id}/cancel', [App\Http\Controllers\Api\QuickServiceController::class, 'cancel']);

    Route::get('/orders/{id}/receipt', [App\Http\Controllers\Api\QuickServiceController::class, 'getReceipt']);
    Route::post('/orders/{id}/print-kitchen', [App\Http\Controllers\Api\QuickServiceController::class, 'printKitchen']);
    Route::post('/orders/{id}/print-receipt', [App\Http\Controllers\Api\QuickServiceController::class, 'printReceipt']);

    Route::get('/statistics', [App\Http\Controllers\Api\QuickServiceController::class, 'statistics']);
});
