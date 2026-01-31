<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\RincianPembelianController;
use App\Http\Controllers\RincianPenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfgenerateController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\GudangsController;


// Route accessible to everyone
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/wait', function () {
    if (Auth::user()->level >= 1) {
        return redirect()->route('dashboard');
    }
    return view('wait');
})->name('wait');

Route::get('/overdue', function () {
    return view('overdue');
})->name('overdue');


// Routes that require authentication and email verification
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/rincianpenjualan/{penjualan_id}', [RincianPenjualanController::class, 'index'])->name('rincianpenjualan.index');
    Route::get('/penjualan/{penjualan_id}/rincianpenjualan/create', [RincianPenjualanController::class, 'create'])->name('rincianpenjualan.create');
    Route::post('/rincianpenjualan', [RincianPenjualanController::class, 'store'])->name('rincianpenjualan.store');
    Route::get('/rincianpenjualan/{id}/edit', [RincianPenjualanController::class, 'edit'])->name('rincianpenjualan.edit');
    Route::put('/rincianpenjualan/{id}', [RincianPenjualanController::class, 'update'])->name('rincianpenjualan.update');
    Route::delete('/rincianpenjualan/{id}', [RincianPenjualanController::class, 'destroy'])->name('rincianpenjualan.destroy');

    Route::get('/rincianpembelian/{pembelian_id}', [RincianPembelianController::class, 'index'])->name('rincianpembelian.index');
    Route::get('/pembelian/{pembelian_id}/rincianpembelian/create', [RincianPembelianController::class, 'create'])->name('rincianpembelian.create');
    Route::post('/rincianpembelian', [RincianPembelianController::class, 'store'])->name('rincianpembelian.store');
    Route::get('/rincianpembelian/{id}/edit', [RincianPembelianController::class, 'edit'])->name('rincianpembelian.edit');
    Route::put('/rincianpembelian/{id}', [RincianPembelianController::class, 'update'])->name('rincianpembelian.update');
    Route::delete('/rincianpembelian/{id}', [RincianPembelianController::class, 'destroy'])->name('rincianpembelian.destroy');

    Route::resource('customers', CustomersController::class);
    Route::resource('suppliers', SuppliersController::class);
    Route::resource('pembelian', PembelianController::class);
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('gudangs', GudangsController::class);
    
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::delete('/stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
    Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('/stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::get('/stocks/autocomplete', [StockController::class, 'autocomplete']);

    
    // Additional routes that require authentication and verification
    Route::get('/laporan/profit', [LaporanController::class, 'profit'])->name('laporan.profit');
    Route::get('/rincianpenjualan/{penjualan_id}/invoice', [PdfgenerateController::class, 'generatePDF'])->name('rincianpenjualan.invoice');
    Route::get('/rincianpenjualan/{penjualan_id}/suratjalan', [PdfgenerateController::class, 'generatePDF2'])->name('rincianpenjualan.suratjalan');
    
    Route::resource('level', LevelController::class);
    
});

require __DIR__.'/auth.php';
