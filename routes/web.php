<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MatkulController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [DashboardController::class, 'index'])->name('Dashboard.index');
Route::resource('Kelas', KelasController::class);
Route::resource('Matkul', MatkulController::class);
Route::resource('Mahasiswa', MahasiswaController::class);
Route::get('/export-pdf', [MatkulController::class, 'exportPdf'])->name('export.pdf');
