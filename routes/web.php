<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\PaketExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BiayaExportController;
use App\Http\Controllers\LaporanExportController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/home', [HomeController::class, 'index'])->name('dashboard.home');

Route::get('/paket', function () {
    return view('pages.paket');
})->name('paket.index');
Route::get('/paket/create', function () {
    return view('pages.create-paket');
})->name('paket.create');
Route::get('/paket/edit/{id}', function ($id) {
    return view('pages.edit-paket', ['id' => $id]);
})->name('paket.edit');


Route::get('/profile-admin', function () {
    return view('pages.profile-admin');
});
Route::get('/profile-admin/create', function () {
    return view('pages.create-profile-admin');
});

Route::get('/profile-admin/edit/{id}', function ($id) {
    return view('pages.edit-profile-admin', ['id' => $id]);
})->name('profile-admin.edit');



Route::get('/profile-tim-operasional', function () {
    return view('pages.profile-tim-operasional');
});
Route::get('/profile-tim-operasional/create', function () {
    return view('pages.create-profile-tim-operasional');
});
Route::get('/profile-tim-operasional/edit/{id}', function ($id) {
    return view('pages.edit-profile-tim-operasional', ['id' => $id]);
})->name('profile-tim-operasional.edit');


Route::get('/biaya', function () {
    return view('pages.biaya');
})->name('biaya.index');

Route::get('/biaya/create', function () {
    return view('pages.create-biaya');
})->name('biaya.create');

Route::get('/biaya/edit/{id}', function ($id) {
    return view('pages.edit-biaya', ['id' => $id]);
})->name('biaya.edit');


Route::get('/invoice/download', [InvoiceController::class, 'download'])->name('invoice.download');

// Route::get('/paket/export', [PaketExportController::class, 'export'])->name('paket.export');
Route::get('/biaya/export', [BiayaExportController::class, 'export'])->name('biaya.export');
Route::get('/laporan/export', [LaporanExportController::class, 'export'])->name('laporan.export');


Route::get('/laporan', [LaporanController::class, 'index'])->name('pages.laporan');

Route::get('/vendor', function () {
    return view('pages.vendor');
})->name('vendor.index');
Route::get('/vendor/create', function () {
    return view('pages.create-vendor');
})->name('vendor.create');
Route::get('/vendor/edit/{id}', function ($id) {
    return view('pages.edit-vendor', ['id' => $id]);
})->name('vendor.edit');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
