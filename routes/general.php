<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('livewire.auth.login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
    Route::get('reports/{id}/download', DownloadController::class)->name('reports.download');
    Route::get('reports/export/{type}/{month}/{year}', ReportController::class)->name('reports.export');
});
