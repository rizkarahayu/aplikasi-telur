<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');

// });
use App\Http\Controllers\PenjualanController;
Route::get('/', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
