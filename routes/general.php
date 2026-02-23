<?php

use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/', 'pages::guest-report')->name('reports.guest');
    Route::livewire('guest/{id}/completed', 'pages::guest-report-completed')->name('reports.guest.completed');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
    Route::get('reports/{id}/download', DownloadController::class)->name('reports.download');
});
