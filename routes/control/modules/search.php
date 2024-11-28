<?php

use App\Http\Controllers\Control\AnnouncementTemplateController;
use App\Http\Controllers\Control\ArtistController;
use App\Http\Controllers\Control\CityController;
use App\Http\Controllers\Control\LabelController;
use App\Http\Controllers\Control\PlatformController;
use App\Http\Controllers\Control\ProductController;
use App\Http\Controllers\Control\ServiceController;
use App\Http\Controllers\Control\SongController;
use App\Http\Controllers\Control\UserController;

Route::group(['prefix' => 'search', 'as' => 'search.',], function () {
    Route::get('products', [ProductController::class, 'search'])->name('products');
    Route::get('artists', [ArtistController::class, 'search'])->name('artists');

    Route::get('artists-platform-search', [ArtistController::class, 'searchPlatform'])
        ->name('artists-platform-search');


    Route::get('labels', [LabelController::class, 'search'])->name('labels');
    //Route::get('countries', [CountryController::class, 'search'])->name('countries')->withoutMiddleware('auth:sanctum');
    //Route::get('states', [CountryController::class, 'search'])->name('states')->withoutMiddleware('auth:sanctum');
    Route::get('cities', [CityController::class, 'search'])->name('cities')->withoutMiddleware('auth:sanctum');
    Route::get('songs', [SongController::class, 'search'])->name('songs');
    Route::get('catalog-songs', [SongController::class, 'searchCatalog'])->name('catalog.songs');
    Route::get('platforms', [PlatformController::class, 'search'])->name('platforms');
    Route::get('users', [UserController::class, 'search'])->name('users');
    Route::get('announcement-templates',
        [AnnouncementTemplateController::class, 'search'])->name('announcement-templates');
    Route::get('upc', [ProductController::class, 'checkUPC'])->name('upc');
    Route::get('isrc', [SongController::class, 'checkISRC'])->name('isrc');
    Route::get('services', [ServiceController::class, 'search'])->name('services');
});