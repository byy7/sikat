<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.auth.login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('reports/{id}/download', DownloadController::class)->name('reports.download');
    Route::get('reports/export/{type}/{month}/{year}', ReportController::class)->name('reports.export');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
