<?php

use App\Http\Controllers\Control\BroadcastController;
use App\Http\Controllers\Control\DashboardController;
use App\Http\Controllers\Control\ArtistController;
use App\Http\Controllers\Control\ArtistBranchController;
use App\Http\Controllers\Control\BankController;
use App\Http\Controllers\Control\LabelController;
use App\Http\Controllers\Control\MediaController;
use App\Http\Controllers\Control\ProductApplyController;
use App\Http\Controllers\Control\ReportController;
use App\Http\Middleware\VerificationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Control\RoleController;
use App\Http\Controllers\Control\UserController;
use App\Http\Controllers\Control\ProductController;
use App\Http\Controllers\Control\SongController;
use App\Http\Controllers\Control\PaymentController;
use App\Http\Controllers\Control\StatisticController;
use App\Http\Controllers\Control\FinanceAnalysController;
use App\Http\Controllers\Control\FinanceAnalysisController;
use App\Http\Controllers\Control\EarningReportController;

Route::group(
    [
        'middleware' => ['auth', VerificationMiddleware::class],
        'prefix' => 'control',
        'as' => 'control.',
    ],
    function () {

        Route::any('tus/{any?}', [SongController::class, 'uploadSong'])->where('any', '.*')->name('tus');
        Route::redirect('/', '/control/dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //Catalog routes
        Route::group(['prefix' => 'catalog', 'as' => 'catalog.'], function () {
            Route::resource('artists', ArtistController::class)->names('artists');
            Route::post('labels/{label}/dsp/create', [LabelController::class, 'createDSP'])->name('label.dsp.create');
            Route::post('labels/{label}/dsp/status',
                [LabelController::class, 'changeStatus'])->name('label.dsp.status');
            Route::resource('labels', LabelController::class)->names('labels');
            Route::apiResource('artist-branches', ArtistBranchController::class)->names('artist-branches');

            Route::resource('songs', SongController::class)->names('songs');
            Route::post('song/favorite/{song}', [SongController::class, 'toggleFavorite'])->name('song.toggleFavorite');
            Route::post('song/change-status', [SongController::class, 'changeStatus'])->name('song-change-status');
            Route::get('songs/{song}/search-track', [SongController::class, 'searchTrack'])->name('songs.search-track');
            Route::get('songs/{song}/get-lyrics', [SongController::class, 'getLyrics'])->name('songs.get-lyrics');
            Route::post(
                'songs/{song}/store-lyrics',
                [SongController::class, 'storeLyrics']
            )->name('songs.store-lyrics');
            Route::post('songs-delete', [SongController::class, 'songsDelete'])->name('songs.songsDelete');

            Route::group(
                ['prefix' => 'products', 'as' => 'products.'],
                function () {
                    Route::get('/', [ProductController::class, 'index'])->name('index');
                    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
                    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('delete');
                    Route::get('create', [ProductController::class, 'create'])->name('create');
                    Route::post('store', [ProductController::class, 'store'])->name('store');
                    Route::post('change-type/{product}', [ProductController::class, 'changeType'])->name('change.type');


                    Route::group(['prefix' => 'form', 'as' => 'form.'], function () {
                        Route::get('step/{step}/{product}', [ProductController::class, 'edit'])->name('edit');
                        Route::post('/{product}', [ProductController::class, 'stepStore'])->name('step.store');
                    });
                }
            );

            // Get ISRC
            Route::post(
                'changeStatus/{product}',
                [ProductController::class, 'changeStatus']
            )->name('products.changeStatus');
            Route::get('getISRC', [ProductController::class, 'getISRC'])->name('products.get-isrc');
            // Check ISRC
            Route::get('checkISRC', [ProductController::class, 'checkISRC'])->name('products.check-isrc');

            Route::group(
                ['prefix' => 'products-apply', 'as' => 'products-apply.'],
                function () {
                    Route::get('/', [ProductApplyController::class, 'index'])->name('index');
                    Route::post('/change-status', [ProductApplyController::class, 'changeStatus'])
                        ->name('change-status');
                    Route::post('/correction', [ProductApplyController::class, 'correction'])
                        ->name('correction');
                }
            );
        });

        //Finance routes
        Route::group(['prefix' => 'finance', 'as' => 'finance.'], function () {

            Route::apiResource('payments', PaymentController::class)
                ->only(['index', 'store'])->names('payments');

            Route::get('reports/create-demo-earnings', [ReportController::class, 'createDemoEarnings'])
                ->name('reports.create-demo-earnings');
            Route::get('reports/download/{report}', [ReportController::class, 'download'])
                ->name('reports.download');
            Route::apiResource('reports', ReportController::class)->names('reports');


            Route::get('analysis', [FinanceAnalysisController::class, 'index'])
                ->name('analysis.index');

            Route::get('analysis/show', [FinanceAnalysisController::class, 'show'])->name('analysis.show');
        });

        Route::group(['prefix' => 'bank', 'as' => 'bank.'], function () {
            Route::post('account', [BankController::class, 'store'])->name('account.store');
            Route::put('account/{bankAccount}', [BankController::class, 'update'])->name('account.update');
        });

        //Statistics Routes
        Route::group(['prefix' => 'statistics', 'as' => 'statistics.'], function () {
            Route::get('statistics', [StatisticController::class, 'index'])->name('statistics.index');
        });

        Route::resource('roles', RoleController::class)->names('roles');

        Route::group(['prefix' => 'user-management', 'as' => 'user-management.'], function () {
            Route::resource('users', UserController::class)->names('users');
            Route::post(
                'users/{user}/toggle-status',
                [UserController::class, 'toggleStatus']
            )->name('users.toggle-status');
            Route::post('users/switch-to-user', [UserController::class, 'switchToUser'])->name('users.switch-to-user');
            Route::post('users/switch-back-to-admin', [UserController::class, 'switchBackToAdmin'])
                ->name('users.switch-back-to-admin');
            Route::post('users/{user}/assign-to-products', [UserController::class, 'assignToProducts'])
                ->name('users.assign-to-products');
            Route::post('users/{user}/assign-to-labels', [UserController::class, 'assignToLabels'])
                ->name('users.assign-to-labels');
            Route::post('users/{user}/add-to-children', [UserController::class, 'addToChildren'])
                ->name('users.add-to-children');
            Route::post('users/{user}/assign-to-commission-rate', [UserController::class, 'assignToCommissionRate'])
                ->name('users.assign-to-commission-rate');
            Route::post('users/{user}/assign-to-payment-threshold', [UserController::class, 'assignToPaymentThreshold'])
                ->name('users.assign-to-payment-threshold');
            Route::post('users/{user}/togglePermissions', [UserController::class, 'togglePermissions'])
                ->name('users.togglePermissions');
            Route::post('users/{user}/detach-parent', [UserController::class, 'detachParent'])
                ->name('users.detach-parent');

            Route::post('users/{product}/detach-product', [UserController::class, 'detachProduct'])
                ->name('users.detach-product');

            Route::post(
                'users/{label}/detach-label',
                [UserController::class, 'detachLabel']
            )->name('users.detach-label');
        });

        Route::post('song-upload', [MediaController::class, 'songUpload'])->name('song.upload');
        Route::post('image-upload/{model}/{id}', [MediaController::class, 'mediaUpload'])->name('image.upload');

        Route::post(
            'artists-platform-match',
            [ArtistController::class, 'artistPlatformMatch']
        )->name('artists-platform-match');

        Route::post(
            'artist-platform-detach/{artist}',
            [ArtistController::class, 'detachPlatform']
        )->name('artist-platform-detach');


        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');


        require __DIR__.'/control/modules/search.php';
        require __DIR__.'/control/modules/last.php';
        require __DIR__.'/control/modules/find.php';
    }
);
