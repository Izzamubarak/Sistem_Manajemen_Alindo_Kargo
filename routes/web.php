<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BiayaExportController;
use App\Http\Controllers\LaporanExportController;

// ✅ Akses bebas (tanpa login)
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// ✅ Grup khusus untuk halaman yang butuh login
Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('dashboard.home');

    // Paket
    Route::get('/paket', fn() => view('pages.paket'))->name('paket.index');
    Route::get('/paket/create', fn() => view('pages.create-paket'))->name('paket.create');
    Route::get('/paket/edit/{id}', fn($id) => view('pages.edit-paket', ['id' => $id]))->name('paket.edit');

    // Profile Admin
    Route::get('/profile-admin', fn() => view('pages.profile-admin'));
    Route::get('/profile-admin/create', fn() => view('pages.create-profile-admin'));
    Route::get('/profile-admin/edit/{id}', fn($id) => view('pages.edit-profile-admin', ['id' => $id]))->name('profile-admin.edit');

    // Profile Tim Operasional
    Route::get('/profile-tim-operasional', fn() => view('pages.profile-tim-operasional'));
    Route::get('/profile-tim-operasional/create', fn() => view('pages.create-profile-tim-operasional'));
    Route::get('/profile-tim-operasional/edit/{id}', fn($id) => view('pages.edit-profile-tim-operasional', ['id' => $id]))->name('profile-tim-operasional.edit');

    // Biaya
    Route::get('/biaya', fn() => view('pages.biaya'))->name('biaya.index');
    Route::get('/biaya/create', fn() => view('pages.create-biaya'))->name('biaya.create');
    Route::get('/biaya/edit/{id}', fn($id) => view('pages.edit-biaya', ['id' => $id]))->name('biaya.edit');

    // Vendor
    Route::get('/vendor', fn() => view('pages.vendor'))->name('vendor.index');
    Route::get('/vendor/create', fn() => view('pages.create-vendor'))->name('vendor.create');
    Route::get('/vendor/edit/{id}', fn($id) => view('pages.edit-vendor', ['id' => $id]))->name('vendor.edit');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('pages.laporan');

    // Export dan Invoice
    Route::get('/invoice/download', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/biaya/export', [BiayaExportController::class, 'export'])->name('biaya.export');
    Route::get('/laporan/export', [LaporanExportController::class, 'export'])->name('laporan.export');
});

// ✅ Halaman fallback 404 untuk semua URL tidak dikenal
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
