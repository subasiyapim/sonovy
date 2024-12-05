<?php

use App\Http\Controllers\Control\BankController;
use App\Http\Controllers\Control\BroadcastController;
use App\Http\Controllers\Control\CityController;
use App\Http\Controllers\Control\CopyrightController;
use App\Http\Controllers\Control\DashboardController;
use App\Http\Controllers\Control\ArtistController;
use App\Http\Controllers\Control\ArtistBranchController;
use App\Http\Controllers\Control\GlobalSearchController;
use App\Http\Controllers\Control\IntegrationController;
use App\Http\Controllers\Control\LabelController;
use App\Http\Controllers\Control\LanguageController;
use App\Http\Controllers\Control\MailTemplateController;
use App\Http\Controllers\Control\MediaController;
use App\Http\Controllers\Control\ProductApplyController;
use App\Http\Controllers\Control\ReportController;
use App\Http\Controllers\Control\SiteController;
use App\Http\Controllers\Control\UpcController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PubController;
use App\Http\Middleware\VerificationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Control\RoleController;
use App\Http\Controllers\Control\UserController;
use App\Http\Controllers\Control\ProductController;
use App\Http\Controllers\Control\SongController;
use App\Http\Controllers\Control\SettingController;
use App\Http\Controllers\Control\ContractController;
use App\Http\Controllers\Control\AuthorController;
use App\Http\Controllers\Control\PlatformController;
use App\Http\Controllers\Control\PlanController;
use App\Http\Controllers\Control\PlanItemController;
use App\Http\Controllers\Control\AnnouncementController;
use App\Http\Controllers\Control\AnnouncementTemplateController;
use App\Http\Controllers\Control\TitleController;
use App\Http\Controllers\Control\FeatureController;
use App\Http\Controllers\Control\HelpCenterFAQController;
use App\Http\Controllers\Control\HelpCenterArticleController;
use App\Http\Controllers\Control\HelpCenterVideoController;
use App\Http\Controllers\Control\PartnerController;
use App\Http\Controllers\Control\SiteFeatureController;
use App\Http\Controllers\Control\TestimonialController;
use App\Http\Controllers\Control\ContactUsController;
use App\Http\Controllers\Control\WorkController;
use App\Http\Controllers\Control\OrderController;
use App\Http\Controllers\Control\PaymentController;
use App\Http\Controllers\Control\SubscriptionManagementController;
use App\Http\Controllers\Control\ExtraServiceController;
use App\Http\Controllers\Control\ServiceController;
use App\Http\Controllers\Control\SummaryController;
use App\Http\Controllers\Control\StatisticController;
use App\Http\Controllers\Control\DistributionReportController;
use App\Http\Controllers\Control\PlayListPerformanceController;
use App\Http\Controllers\Control\ShortFormattedVideosController;
use App\Http\Controllers\Control\FinanceAndEarningController;
use App\Http\Controllers\Control\EarningController;
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

                    Route::post(
                        'make-ddex-xml/{product}',
                        [ProductApplyController::class, 'makeDdexXml']
                    )->name('make-ddex-xml');
                    Route::get(
                        'download-ddex-xml/{product}',
                        [ProductApplyController::class, 'downloadDdexXml']
                    )->name('download-ddex-xml');
                }
            );
        });

        //Statistics Routes
        Route::group(['prefix' => 'statistics', 'as' => 'statistics.'], function () {
            Route::get('statistics', [StatisticController::class, 'index'])->name('statistics.index');
        });

        Route::resource('roles', RoleController::class)->names('roles');

        Route::group(['prefix' => 'user-management', 'as' => 'user-management.'], function () {
            Route::resource('users', UserController::class)->names('users');
            Route::post('users/{user}/toggle-status',
                [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('users/switch-to-user', [UserController::class, 'switchToUser'])->name('users.switch-to-user');
            Route::post('users/switch-back-to-admin', [UserController::class, 'switchBackToAdmin'])
                ->name('users.switch-back-to-admin');
            Route::post('users/{user}/assign-to-products', [UserController::class, 'assignToProducts'])
                ->name('users.assign-to-products');
            Route::post('users/{user}/add-to-children', [UserController::class, 'addToChildren'])
                ->name('users.add-to-children');
        });

        Route::post('user/competency/{user}', [UserController::class, 'competency'])->name('users.competency');

        Route::resource('settings', SettingController::class)->names('settings')->only(['index', 'edit', 'update']);
        Route::resource('contracts', ContractController::class)->names('contracts');
        Route::resource('authors', AuthorController::class)->names('authors');
        Route::resource('platforms', PlatformController::class)->names('platforms');
        Route::post('platform-match', [PlatformController::class, 'match'])->name('platforms.match');

        Route::resource('plans', PlanController::class)->names('plans');
        Route::post('preferred-plan/{plan}', [PlanController::class, 'preferredPlan'])->name('preferred-plan');
        Route::apiResource('plan-items', PlanItemController::class)->names('plan-items');

        //Route::resource('countries', CountryController::class)->names('countries');
        Route::resource('announcements', AnnouncementController::class)->names('announcements');
        Route::post(
            'announcements/destroy-all',
            [AnnouncementController::class, 'destroyAll']
        )->name('announcements.destroy-all');
        Route::resource(
            'announcement-templates',
            AnnouncementTemplateController::class
        )->names('announcement-templates');
        Route::resource('titles', TitleController::class)->names('titles');
        Route::resource('features', FeatureController::class)->names('features');
        Route::resource('help-center/faq', HelpCenterFAQController::class)->names('help-center.faq');
        Route::resource('help-center/articles', HelpCenterArticleController::class)->names('help-center.articles');
        Route::resource('help-center/videos', HelpCenterVideoController::class)->names('help-center.videos');
        Route::resource('partners', PartnerController::class)->names('partners');
        Route::resource('site-features', SiteFeatureController::class)->names('site-features');
        Route::resource('testimonials', TestimonialController::class)->names('testimonials');

        // Contact Us
        Route::get('contact-us', [ContactUsController::class, 'index'])->name('contact-us.list');
        Route::get('contact-us/show/{id}', [ContactUsController::class, 'show'])->name('contact-us.show');
        Route::delete('contact-us', [ContactUsController::class, 'destroy'])->name('contact-us.destroy');


        Route::group(['prefix' => 'works', 'as' => 'works.'], function () {
            Route::get('/', [WorkController::class, 'index'])->name('index');
            Route::get('search', [WorkController::class, 'search'])->name('search');
        });
        Route::resource('orders', OrderController::class)->names('orders');
        Route::get('orders/pdf/{order}', [OrderController::class, 'pdf'])->name('orders.pdf');

        Route::resource('payments', PaymentController::class)->names('payments');
        Route::get('advance-list', [PaymentController::class, 'advanceIndex'])->name('payments.advance-list');
        Route::post(
            'payments/advance-yourself',
            [PaymentController::class, 'advanceYourself']
        )->name('payments.advance-yourself');
        Route::post(
            'payments/confirm-advance',
            [PaymentController::class, 'confirmAdvance']
        )->name('payments.confirm-advance');
        Route::post(
            'payments/payment-yourselfe',
            [PaymentController::class, 'paymentYourself']
        )->name('payments.payment-yourself');
        Route::post(
            'payments/confirm-payments',
            [PaymentController::class, 'confirmPayments']
        )->name('payments.confirm-payments');
        Route::post(
            'payments/reject-payments',
            [PaymentController::class, 'rejectPayments']
        )->name('payments.reject-payments');


        Route::resource(
            'subscription-management',
            SubscriptionManagementController::class
        )->names('subscriptionmanagement');
        Route::resource('extra-services', ExtraServiceController::class)->names('extraservices');
        Route::resource('service', ServiceController::class)->names('service');


        Route::group(['prefix' => 'copyright', "as" => "copyright."], function () {
            Route::get('/', [CopyrightController::class, 'index'])->name('index');
            Route::get('/rejection-demand', [CopyrightController::class, 'demand'])->name('demand');
            Route::post('/', [CopyrightController::class, 'store'])->name('store');
        });


        Route::get('summary', [SummaryController::class, 'index'])->name('summary.index');

        Route::get('distribution-reports', [DistributionReportController::class, 'index'])->name('distribution.index');
        Route::get(
            'playlist-performance',
            [PlayListPerformanceController::class, 'index']
        )->name('playlistperformance.index');
        Route::get('shorts', [ShortFormattedVideosController::class, 'index'])->name('shorts.index');
        Route::get(
            'finance-and-earnings',
            [FinanceAndEarningController::class, 'index']
        )->name('finance-and-earnings.index');

        Route::resource('earnings', EarningController::class)->names('earnings');
        Route::resource('earning-reports', EarningReportController::class)->names('earning-reports');
        Route::post(
            'earning-reports/process-again',
            [EarningReportController::class, 'processAgain']
        )->name('earning-reports.process-again');

        Route::get(
            'upload-earning-report-index',
            [EarningReportController::class, 'fileIndex']
        )->name('uploaded-earning-report-index');
        Route::post(
            'earning-report-upload',
            [EarningReportController::class, 'uploadFile']
        )->name('earning-report-upload');
        Route::delete(
            'earning-report-delete/{earningReportFile}',
            [EarningReportController::class, 'deleteFile']
        )->name('earning-report-delete');


        //Artist Branches
        Route::get('list', [ArtistBranchController::class, 'getBranches'])->name('artist-branches.from-input-format');


        Route::get('hashtags', fn() => HashtagServices::getHashtags())->name('hashtags');

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


        //Excel Import Export
        Route::group(['excel', 'as' => 'excel.'], function () {

            //Export
            Route::group(['prefix' => 'export', 'as' => 'export.'], function () {
                Route::get('products', [ProductController::class, 'export'])->name('products');
            });

            //Import
            Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
                Route::post('products', [ProductController::class, 'import'])->name('products');
            });
        });

        Route::post('convert-audio', [ProductController::class, 'convertAudio'])->name('product.convert-audio');

        Route::resource('integrations', IntegrationController::class)->names('integrations')->only([
            'index',
            'edit',
            'update'
        ]);

        Route::resource('reports', ReportController::class)->names('reports');
        Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

        Route::post('global-search', [GlobalSearchController::class, 'search'])->name('global-search');

        Route::resource('upcs', UpcController::class)->names('upcs');

        Route::post('source-languages', [LanguageController::class, 'sourceLanguages'])->name('source-languages');
        Route::post('translate-language', [LanguageController::class, 'translate'])->name('translate-language');

        Route::resource('mail-templates', MailTemplateController::class)->names('mail-templates');

        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');


        //    Route::middleware('auth')->group(function () {
        //        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        //        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        //        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        //    });

        require __DIR__.'/control/modules/search.php';
        require __DIR__.'/control/modules/last.php';
        require __DIR__.'/control/modules/find.php';


    }
);
