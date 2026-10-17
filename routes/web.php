<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SinglePredictionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard & Analisis Sentimen
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Upload Dataset Baru
    Route::get('/datasets/upload', [DatasetController::class, 'create'])->name('datasets.create');
    Route::post('/datasets/upload', [DatasetController::class, 'store'])->name('datasets.store');

    // Uji Coba Ulasan Tunggal (Real-time Tester)
    Route::get('/prediction/single', [SinglePredictionController::class, 'index'])->name('prediction.single');
    Route::post('/prediction/single', [SinglePredictionController::class, 'predict'])->name('prediction.single.post');

    // Ekspor Laporan PDF & CSV
    Route::get('/reports/{evaluation}/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/{evaluation}/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Khusus Administrator
    Route::middleware('admin')->group(function () {
        Route::delete('/datasets/{evaluation}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
        Route::resource('/admin/users', UserController::class)->names('admin.users')->except(['create', 'show', 'edit']);
    });
});

require __DIR__.'/auth.php';
