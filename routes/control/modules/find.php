<?php


use App\Http\Controllers\PubController;

Route::group(['prefix' => 'find', 'as' => 'find.'], function () {
    Route::post('product', [PubController::class, 'findBroadcast'])->name('products');
    Route::post('song', [PubController::class, 'findSong'])->name('songs');
    Route::post('label', [PubController::class, 'findLabel'])->name('labels');
    Route::post('plan', [PubController::class, 'findPlan'])->name('plans');
    Route::post('user', [PubController::class, 'findUser'])->name('users');
    Route::post('artist', [PubController::class, 'findArtist'])->name('artist');
    Route::post(
        'announcement-templates',
        [PubController::class, 'findAnnouncementTemplates']
    )->name('announcement-templates');
});

Route::group(['prefix' => 'findall', 'as' => 'findall.'], function () {
    Route::post('artists', [PubController::class, 'findAllArtists'])->name('artists');
    Route::post('cities', [PubController::class, 'findAllCities'])->name('cities');
    Route::post('states', [PubController::class, 'findAllStates'])->name('states');
});