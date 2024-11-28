<?php


use App\Http\Controllers\PubController;

Route::group(['prefix' => 'last', 'as' => 'last.'], function () {
    Route::get('artist', [PubController::class, 'lastArtist'])->name('artists');
    Route::get('label', [PubController::class, 'lastLabel'])->name('labels');
    Route::get('song', [PubController::class, 'lastSong'])->name('songs');
    Route::get('titles', [PubController::class, 'lastTitle'])->name('titles');
    Route::get('features', [PubController::class, 'lastFeature'])->name('features');
    Route::get('plans', [PubController::class, 'lastPlan'])->name('plans');
    Route::get(
        'announcement-template',
        [PubController::class, 'lastAnnouncementTemplate']
    )->name('announcement-template');
    Route::get('artist-branches', [PubController::class, 'lastArtistBranch'])->name('artist-branches');
    Route::get('service', [PubController::class, 'lastService'])->name('service');
});