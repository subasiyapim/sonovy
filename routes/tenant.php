<?php

declare(strict_types=1);

use App\Http\Controllers\Front\HomeController;
use App\Http\Middleware\InitializeTenantMiddleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    PreventAccessFromCentralDomains::class,
    InitializeTenancyBySubdomain::class,
])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::group(['prefix' => 'search', 'as' => 'search.'], function () {
        Route::get('countries', function (Request $request) {

            Validator::make($request->all(), [
                'search' => 'required|string|min:3|max:255'
            ])->validate();

            $search = $request->input('search');

            return CountryServices::search($search);
        })->name('countries');


        Route::get('articles', function (Request $request) {

            Validator::make($request->all(), [
                'search' => 'required|string|min:3|max:255'
            ])->validate();

            $search = $request->input('search');

            return HelpCenterArticleServices::search($search);
        })->name('articles');


    });

    require __DIR__.'/auth.php';
    require __DIR__.'/control.php';

});
