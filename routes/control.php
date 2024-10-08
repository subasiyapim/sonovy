<?php

use App\Http\Controllers\Control\DashboardController;
use App\Http\Controllers\Control\ArtistController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::group(
    [
        'middleware' => [
            'auth'
        ],
        'prefix' => 'control',
        'as' => 'control.',
    ], function () {


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('artists', ArtistController::class)->names('artists');


    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});