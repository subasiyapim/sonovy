<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\SupportController;
use App\Http\Controllers\Front\ContactUsController;
use App\Services\CountryServices;
use App\Services\HelpCenterArticleServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

Route::group(
    [
        'middleware' => [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain::class,
        ]
    ], function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/support-center', [SupportController::class, 'index'])->name('support.index');
    Route::get('/faq', [SupportController::class, 'faq'])->name('support.faq');
    Route::get('/faq/{id}', [SupportController::class, 'faq_show'])->name('support.faq.show');
    Route::get('/video-education', [SupportController::class, 'video'])->name('support.video');
    Route::get('/support-blog', [SupportController::class, 'blog'])->name('support.blog');
    Route::get('/support-blog/{id}', [SupportController::class, 'blog_show'])->name('support.blog.show');
    Route::get('/blog/{id}/show', [SupportController::class, 'show'])->name('blog.show');
    Route::post('/contact-us/store', [ContactUsController::class, 'store'])->name('contact-us.store');

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
});