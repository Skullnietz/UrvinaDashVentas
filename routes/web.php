<?php

use App\Http\Controllers\DashVentasController;
use App\Http\Controllers\DashboardController;

Route::get('/rentautil', [DashVentasController::class, 'RentaUtilView']);
Route::get('/topsupinf', [DashVentasController::class, 'TopSupInfView'])->name('topsupinf');
Route::get('/ventaspermes', [DashVentasController::class, 'VentasPerMesView']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');

Route::get('/', function () {
    return view('welcome');
});
