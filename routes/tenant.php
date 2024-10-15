<?php

declare(strict_types=1);

use App\Http\Middleware\InitializeTenantMiddleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    PreventAccessFromCentralDomains::class,
    InitializeTenancyByDomainOrSubdomain::class,
    InitializeTenantMiddleware::class,
])->group(function () {


    Route::get('/', [\App\Http\Controllers\Front\HomeController::class, 'index'])->name('home');
    Route::get('/support-center',
        [\App\Http\Controllers\Front\SupportController::class, 'index'])->name('support.index');
    Route::get('/faq', [\App\Http\Controllers\Front\SupportController::class, 'faq'])->name('support.faq');
    Route::get('/faq/{id}',
        [\App\Http\Controllers\Front\SupportController::class, 'faq_show'])->name('support.faq.show');
    Route::get('/video-education',
        [\App\Http\Controllers\Front\SupportController::class, 'video'])->name('support.video');
    Route::get('/support-blog', [\App\Http\Controllers\Front\SupportController::class, 'blog'])->name('support.blog');
    Route::get('/support-blog/{id}',
        [\App\Http\Controllers\Front\SupportController::class, 'blog_show'])->name('support.blog.show');
    Route::get('/blog/{id}/show', [\App\Http\Controllers\Front\SupportController::class, 'show'])->name('blog.show');
    Route::post('/contact-us/store',
        [\App\Http\Controllers\Front\ContactUsController::class, 'store'])->name('contact-us.store');

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


    require __DIR__.'/control.php';

});
