<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Data_paketController;
use App\Http\Controllers\Biaya_operasionalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\VendorController;
// use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;

// =====================
// PUBLIC ROUTES
// =====================
Route::post('/login', [AuthController::class, 'login']);


// =====================
// PROTECTED ROUTES
// =====================

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/register', [AuthController::class, 'register']);
    // User info & logout
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Paket Routes
    Route::prefix('paket')->group(function () {
        Route::get('/', [Data_paketController::class, 'index']);
        Route::get('{id}', [Data_paketController::class, 'show']);
        Route::post('/', [Data_paketController::class, 'store']);
        Route::put('{id}', [Data_paketController::class, 'update']);
        Route::delete('{id}', [Data_paketController::class, 'destroy']);
    });

    // Biaya Operasional Routes
    Route::prefix('biaya')->group(function () {
        Route::get('/', [Biaya_operasionalController::class, 'index']);
        Route::get('{id}', [Biaya_operasionalController::class, 'show']);
        Route::post('/', [Biaya_operasionalController::class, 'store']);
        Route::put('{id}', [Biaya_operasionalController::class, 'update']);
        Route::delete('{id}', [Biaya_operasionalController::class, 'destroy']);
    });

    // Laporan Routes
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index']);
        Route::get('{id}', [LaporanController::class, 'show']);
        Route::post('/', [LaporanController::class, 'store']);
        Route::put('{id}', [LaporanController::class, 'update']);
        Route::delete('{id}', [LaporanController::class, 'destroy']);
    });

    // Vendor Routes
    Route::prefix('vendor')->group(function () {
        Route::get('/', [VendorController::class, 'index']);
        Route::get('{id}', [VendorController::class, 'show']);
        Route::post('/', [VendorController::class, 'store']);
        Route::put('{id}', [VendorController::class, 'update']);
        Route::delete('{id}', [VendorController::class, 'destroy']);
    });

    // Tim Operasional Routes
    Route::prefix('tim')->group(function () {
        Route::get('/', [UserController::class, 'timIndex']);
        Route::post('/', [UserController::class, 'timStore']);
        Route::delete('{id}', [UserController::class, 'timDestroy']);
    });

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'show']);
        Route::put('/', [UserController::class, 'update']);
        Route::get('/admin', [UserController::class, 'showAdmin']);
        Route::put('/admin', [UserController::class, 'updateAdmin']);
        Route::delete('/admin/{id}', [UserController::class, 'destroyAdmin']);
        Route::get('/tim-operasional', [UserController::class, 'showTim']);
        Route::put('/tim-operasional', [UserController::class, 'updateTim']);
        Route::delete('/tim-operasional/{id}', [UserController::class, 'destroyTim']);
            
    });
    


    // Invoice Download
   
    
Route::get('/invoice/by-paket/{id}', [InvoiceController::class, 'showByPaket']);

    // invoice Routes
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::get('{id}', [InvoiceController::class, 'show']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::put('{id}', [InvoiceController::class, 'update']);
        Route::delete('{id}', [InvoiceController::class, 'destroy']);
    });

});

// =====================
// PING ROUTE
// =====================

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});
