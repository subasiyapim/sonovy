<?php

use App\Http\Controllers\Control\AnnouncementController;
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
use App\Http\Controllers\Control\FinanceAnalysisController;
use App\Http\Controllers\Control\PlanController;
use App\Http\Controllers\Control\PlanItemController;

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
            Route::post(
                'labels/{label}/dsp/status',
                [LabelController::class, 'changeStatus']
            )->name('label.dsp.status');
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


            Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                Route::get('/', [ReportController::class, 'index'])->name('index');
                Route::post('/', [ReportController::class, 'store'])->name('store');

                Route::get('report-files', [ReportController::class, 'reportFiles'])->name('report-files');
                Route::get('participant-reports', [ReportController::class, 'participantReports'])->name('participant-reports');
                Route::post('/upload-file', [ReportController::class, 'uploadFile'])->name('uploadFile');
                Route::get('/download/{report}', [ReportController::class, 'download'])->name('download');
                Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
                Route::get('/{report}', [ReportController::class, 'show'])->name('show');
                Route::get('/export/participant-reports', [ReportController::class, 'exportParticipantReports'])->name('export.participant-reports');

                // Yeni rotalar
                Route::get('/filter/by-platform/{platform}', [ReportController::class, 'filterByPlatform'])
                    ->name('filter.platform');
                Route::get('/filter/by-period/{period}', [ReportController::class, 'filterByPeriod'])
                    ->name('filter.period');
                Route::get('/filter/by-type/{type}', [ReportController::class, 'filterByType'])
                    ->name('filter.type');
                Route::get('/filter/by-status/{status}', [ReportController::class, 'filterByStatus'])
                    ->name('filter.status');
                Route::post('/bulk-download', [ReportController::class, 'bulkDownload'])
                    ->name('bulk.download');
                Route::post('/bulk-delete', [ReportController::class, 'bulkDelete'])
                    ->name('bulk.delete');
                Route::get('/export/summary', [ReportController::class, 'exportSummary'])
                    ->name('export.summary');
            });

            Route::get('analysis', [FinanceAnalysisController::class, 'index'])
                ->name('analysis.index');

            Route::get('analysis/show', [FinanceAnalysisController::class, 'show'])->name('analysis.show');
        });


        Route::group(['prefix' => 'bank', 'as' => 'bank.'], function () {
            Route::post('account', [BankController::class, 'store'])->name('account.store');
            Route::put('account/{bankAccount}', [BankController::class, 'update'])->name('account.update');
        });

        Route::group(['prefix' => 'management', 'as' => 'management.'], function () {
            Route::group(['prefix' => 'announcement', 'as' => 'announcements.'], function () {
                Route::get('/', [AnnouncementController::class, 'index'])->name('index');
            });
            Route::group(['prefix' => 'plan-items', 'as' => 'planItems.'], function () {
                Route::get('/', [PlanItemController::class, 'index'])->name('index');
            });
        });

        //Statistics Routes
        Route::group(['prefix' => 'statistics', 'as' => 'statistics.'], function () {
            Route::get('/', [StatisticController::class, 'index'])->name('index');
            Route::get('/product/{product}', [StatisticController::class, 'product'])->name('product');
            Route::get('/artist/{artist}', [StatisticController::class, 'artist'])->name('artist');
            Route::get('/label/{label}', [StatisticController::class, 'label'])->name('label');
            Route::get('/song/{song}', [StatisticController::class, 'song'])->name('song');
        });

        Route::resource('roles', RoleController::class)->names('roles');

        Route::group(['prefix' => 'user-management', 'as' => 'user-management.'], function () {
            Route::resource('users', UserController::class)->names('users');
            Route::post(
                'users/{user}/toggle-status',
                [UserController::class, 'toggleStatus']
            )->name('users.toggle-status');
            Route::post(
                'users/{user}/toggle-email-verification',
                [UserController::class, 'toggleEmailVerification']
            )->name('users.toggle-email-verification');
            Route::post(
                'users/{user}/toggle-phone-verification',
                [UserController::class, 'togglePhoneVerification']
            )->name('users.toggle-phone-verification');
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


        require __DIR__ . '/control/modules/search.php';
        require __DIR__ . '/control/modules/last.php';
        require __DIR__ . '/control/modules/find.php';
    }
);
