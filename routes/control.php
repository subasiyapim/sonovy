<?php

use App\Http\Controllers\Control\BroadcastController;
use App\Http\Controllers\Control\DashboardController;
use App\Http\Controllers\Control\ArtistController;
use App\Http\Controllers\Control\LabelController;
use App\Http\Controllers\ProfileController;
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
use App\Http\Controllers\Control\CountryController;
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
use App\Http\Controllers\Control\BroadcastApplyController;
use App\Http\Controllers\Control\SummaryController;
use App\Http\Controllers\Control\StatisticController;
use App\Http\Controllers\Control\DistributionReportController;
use App\Http\Controllers\Control\PlayListPerformanceController;
use App\Http\Controllers\Control\ShortFormattedVideosController;
use App\Http\Controllers\Control\FinanceAndEarningController;
use App\Http\Controllers\Control\EarningController;
use App\Http\Controllers\Control\EarningReportController;


Route::group(
    ['middleware' => ['auth'], 'prefix' => 'control', 'as' => 'control.',], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Catalog routes
    Route::group(['prefix' => 'catalog', 'as' => 'catalog.'], function () {
        Route::resource('artists', ArtistController::class)->names('artists');
        Route::resource('labels', LabelController::class)->names('labels');
        Route::apiResource('artist-branches', ArtistBranchController::class)->names('artist-branches');

        Route::resource('songs', SongController::class)->names('songs');
        Route::post('song/change-status', [SongController::class, 'changeStatus'])->name('song-change-status');
        Route::get('songs/{song}/search-track', [SongController::class, 'searchTrack'])->name('songs.search-track');
        Route::get('songs/{song}/get-lyrics', [SongController::class, 'getLyrics'])->name('songs.get-lyrics');
        Route::post('songs/{song}/store-lyrics', [SongController::class, 'storeLyrics'])->name('songs.store-lyrics');

        Route::resource('products', ProductController::class)->names('products');
        Route::post('products/add-participant',
            [ProductController::class, 'addParticipant'])->name('products.add-participant');
        Route::post('products/delete-participant',
            [ProductController::class, 'deleteParticipants'])->name('products.delete-participant');
        // Get ISRC
        Route::get('getISRC', [ProductController::class, 'getISRC'])->name('products.get-isrc');
        // Check ISRC
        Route::get('checkISRC', [ProductController::class, 'checkISRC'])->name('products.check-isrc');

        Route::group(
            ['prefix' => 'products-apply', 'as' => 'products-apply.'], function () {
            Route::get('/', [BroadcastApplyController::class, 'index'])->name('index');
            Route::post('/change-status', [BroadcastApplyController::class, 'changeStatus'])
                ->name('change-status');
            Route::post('/correction', [BroadcastApplyController::class, 'correction'])
                ->name('correction');

            Route::post('make-ddex-xml/{product}',
                [BroadcastApplyController::class, 'makeDdexXml'])->name('make-ddex-xml');
            Route::get('download-ddex-xml/{product}',
                [BroadcastApplyController::class, 'downloadDdexXml'])->name('download-ddex-xml');
        });
    });

    //Statistics Routes
    Route::group(['prefix' => 'statistics', 'as' => 'statistics.'], function () {
        Route::get('statistics', [StatisticController::class, 'index'])->name('statistics.index');
    });

    Route::resource('roles', RoleController::class)->names('roles');

    Route::resource('users', UserController::class)->names('users');
    Route::post('user/competency/{user}', [UserController::class, 'competency'])->name('users.competency');

    Route::resource('settings', SettingController::class)->names('settings')->only(['index', 'edit', 'update']);
    Route::resource('contracts', ContractController::class)->names('contracts');
    Route::resource('authors', AuthorController::class)->names('authors');
    Route::resource('platforms', PlatformController::class)->names('platforms');
    Route::post('platform-match', [PlatformController::class, 'match'])->name('platforms.match');

    Route::resource('plans', PlanController::class)->names('plans');
    Route::post('preferred-plan/{plan}', [PlanController::class, 'preferredPlan'])->name('preferred-plan');
    Route::apiResource('plan-items', PlanItemController::class)->names('plan-items');

    Route::resource('countries', CountryController::class)->names('countries');
    Route::resource('announcements', AnnouncementController::class)->names('announcements');
    Route::post('announcements/destroy-all',
        [AnnouncementController::class, 'destroyAll'])->name('announcements.destroy-all');
    Route::resource('announcement-templates', AnnouncementTemplateController::class)->names('announcement-templates');
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
    Route::post('payments/advance-yourself',
        [PaymentController::class, 'advanceYourself'])->name('payments.advance-yourself');
    Route::post('payments/confirm-advance',
        [PaymentController::class, 'confirmAdvance'])->name('payments.confirm-advance');
    Route::post('payments/payment-yourselfe',
        [PaymentController::class, 'paymentYourself'])->name('payments.payment-yourself');
    Route::post('payments/confirm-payments',
        [PaymentController::class, 'confirmPayments'])->name('payments.confirm-payments');
    Route::post('payments/reject-payments',
        [PaymentController::class, 'rejectPayments'])->name('payments.reject-payments');


    Route::resource('subscription-management',
        SubscriptionManagementController::class)->names('subscriptionmanagement');
    Route::resource('extra-services', ExtraServiceController::class)->names('extraservices');
    Route::resource('service', ServiceController::class)->names('service');


    Route::group(['prefix' => 'copyright', "as" => "copyright."], function () {
        Route::get('/', [CopyrightController::class, 'index'])->name('index');
        Route::get('/rejection-demand', [CopyrightController::class, 'demand'])->name('demand');
        Route::post('/', [CopyrightController::class, 'store'])->name('store');

    });


    Route::get('summary', [SummaryController::class, 'index'])->name('summary.index');

    Route::get('distribution-reports', [DistributionReportController::class, 'index'])->name('distribution.index');
    Route::get('playlist-performance',
        [PlayListPerformanceController::class, 'index'])->name('playlistperformance.index');
    Route::get('shorts', [ShortFormattedVideosController::class, 'index'])->name('shorts.index');
    Route::get('finance-and-earnings',
        [FinanceAndEarningController::class, 'index'])->name('finance-and-earnings.index');

    Route::resource('earnings', EarningController::class)->names('earnings');
    Route::resource('earning-reports', EarningReportController::class)->names('earning-reports');
    Route::post('earning-reports/process-again',
        [EarningReportController::class, 'processAgain'])->name('earning-reports.process-again');

    Route::get('upload-earning-report-index',
        [EarningReportController::class, 'fileIndex'])->name('uploaded-earning-report-index');
    Route::post('earning-report-upload', [EarningReportController::class, 'uploadFile'])->name('earning-report-upload');
    Route::delete('earning-report-delete/{earningReportFile}',
        [EarningReportController::class, 'deleteFile'])->name('earning-report-delete');


    Route::group(
        ['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', [ProfileController::class, 'index'])->name('detail');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/updateBillInfo', [ProfileController::class, 'updateBillInfo'])->name('updateBillInfo');
        Route::post('/updateUserBillInfo',
            [ProfileController::class, 'updateUserBillInfo'])->name('updateUserBillInfo');

        Route::get('/bank-accounts', [BankController::class, 'index'])->name('bank-accounts.index');
        Route::get('/bank-account/create', [BankController::class, 'create'])->name('bank-account.create');
        Route::post('/bank-account/store', [BankController::class, 'store'])->name('bank-account.store');
        Route::get('/bank-account/edit/{bankAccount}', [BankController::class, 'edit'])->name('bank-account.edit');
        Route::post('/bank-account/update/{bankAccount}',
            [BankController::class, 'update'])->name('bank-account.update');
        Route::delete('/bank-account/delete/{bankAccount}',
            [BankController::class, 'delete'])->name('bank-account.delete');

        Route::post('/site/store', [SiteController::class, 'store'])->name('site.store');
        Route::post('credit-card', [ProfileController::class, 'addCreditCard'])->name('credit-card.store');
        Route::post('credit-card/delete', [ProfileController::class, 'deleteCreditCard'])->name('credit-card.delete');
        Route::post('credit-card/set-default',
            [ProfileController::class, 'setDefaultCreditCard'])->name('credit-card.set-default');
        Route::post('credit-card/update', [ProfileController::class, 'updateCreditCard'])->name('credit-card.update');
    });

    //Artist Branches
    Route::get('list', [ArtistBranchController::class, 'getBranches'])->name('artist-branches.from-input-format');


    Route::get('hashtags', fn() => HashtagServices::getHashtags())->name('hashtags');

    Route::post('media-upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::post('artists-platform-match',
        [ArtistController::class, 'artistPlatformMatch'])->name('artists-platform-match');

    //Search routes
    Route::group(['prefix' => 'search', 'as' => 'search.',], function () {
        Route::get('products', [ProductController::class, 'search'])->name('products');
        Route::get('artists', [ArtistController::class, 'search'])->name('artists');

        Route::get('artists-platform-search', [ArtistController::class, 'searchPlatform'])
            ->name('artists-platform-search');

        Route::get('labels', [LabelController::class, 'search'])->name('labels');
        Route::get('countries', [CountryController::class, 'search'])
            ->name('countries')->withoutMiddleware('auth:sanctum');
        Route::get('states', [CountryController::class, 'search'])->name('states')->withoutMiddleware('auth:sanctum');
        Route::get('cities', [CityController::class, 'search'])->name('cities')->withoutMiddleware('auth:sanctum');
        Route::get('songs', [SongController::class, 'search'])->name('songs');
        Route::get('catalog-songs', [SongController::class, 'searchCatalog'])->name('catalog.songs');
        Route::get('platforms', [PlatformController::class, 'search'])->name('platforms');
        Route::get('users', [UserController::class, 'search'])->name('users');
        Route::get('announcement-templates', [AnnouncementTemplateController::class, 'search'])
            ->name('announcement-templates');
        Route::get('upc', [ProductController::class, 'checkUPC'])->name('upc');
        Route::get('isrc', [SongController::class, 'checkISRC'])->name('isrc');
        Route::get('services', [ServiceController::class, 'search'])->name('services');
    });

    Route::group(['prefix' => 'last', 'as' => 'last.'], function () {
        Route::get('artist', [PubController::class, 'lastArtist'])->name('artists');
        Route::get('label', [PubController::class, 'lastLabel'])->name('labels');
        Route::get('song', [PubController::class, 'lastSong'])->name('songs');
        Route::get('titles', [PubController::class, 'lastTitle'])->name('titles');
        Route::get('features', [PubController::class, 'lastFeature'])->name('features');
        Route::get('plans', [PubController::class, 'lastPlan'])->name('plans');
        Route::get('announcement-template',
            [PubController::class, 'lastAnnouncementTemplate'])->name('announcement-template');
        Route::get('artist-branches', [PubController::class, 'lastArtistBranch'])->name('artist-branches');
        Route::get('service', [PubController::class, 'lastService'])->name('service');
    });

    Route::group(['prefix' => 'find', 'as' => 'find.'], function () {
        Route::post('product', [PubController::class, 'findBroadcast'])->name('products');
        Route::post('song', [PubController::class, 'findSong'])->name('songs');
        Route::post('label', [PubController::class, 'findLabel'])->name('labels');
        Route::post('plan', [PubController::class, 'findPlan'])->name('plans');
        Route::post('user', [PubController::class, 'findUser'])->name('users');
        Route::post('artist', [PubController::class, 'findArtist'])->name('artist');
        Route::post('announcement-templates',
            [PubController::class, 'findAnnouncementTemplates'])->name('announcement-templates');
    });
    Route::group(['prefix' => 'findall', 'as' => 'findall.'], function () {
        Route::post('artists', [PubController::class, 'findAllArtists'])->name('artists');
        Route::post('cities', [PubController::class, 'findAllCities'])->name('cities');
        Route::post('states', [PubController::class, 'findAllStates'])->name('states');
    });

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
        'index', 'edit', 'update'
    ]);

    Route::resource('reports', ReportController::class)->names('reports');
    Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

    Route::post('global-search', [GlobalSearchController::class, 'search'])->name('global-search');

    Route::resource('upcs', UpcController::class)->names('upcs');

    Route::post('source-languages', [LanguageController::class, 'sourceLanguages'])->name('source-languages');
    Route::post('translate-language', [LanguageController::class, 'translate'])->name('translate-language');

    Route::resource('mail-templates', MailTemplateController::class)->names('mail-templates');

//    Route::middleware('auth')->group(function () {
//        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//    });
});
