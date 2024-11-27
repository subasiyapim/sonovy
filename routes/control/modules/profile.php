<?php

use App\Http\Controllers\Control\BankController;
use App\Http\Controllers\Control\ProfileController;
use App\Http\Controllers\Control\SiteController;

Route::group(
    ['prefix' => 'profile', 'as' => 'profile.'],
    function () {
        Route::get('/', [ProfileController::class, 'index'])->name('detail');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/updateBillInfo', [ProfileController::class, 'updateBillInfo'])->name('updateBillInfo');
        Route::post(
            '/updateUserBillInfo',
            [ProfileController::class, 'updateUserBillInfo']
        )->name('updateUserBillInfo');

        Route::get('/bank-accounts', [BankController::class, 'index'])->name('bank-accounts.index');
        Route::get('/bank-account/create', [BankController::class, 'create'])->name('bank-account.create');
        Route::post('/bank-account/store', [BankController::class, 'store'])->name('bank-account.store');
        Route::get(
            '/bank-account/edit/{bankAccount}',
            [BankController::class, 'edit']
        )->name('bank-account.edit');
        Route::post(
            '/bank-account/update/{bankAccount}',
            [BankController::class, 'update']
        )->name('bank-account.update');
        Route::delete(
            '/bank-account/delete/{bankAccount}',
            [BankController::class, 'delete']
        )->name('bank-account.delete');

        Route::post('/site/store', [SiteController::class, 'store'])->name('site.store');
        Route::post('credit-card', [ProfileController::class, 'addCreditCard'])->name('credit-card.store');
        Route::post(
            'credit-card/delete',
            [ProfileController::class, 'deleteCreditCard']
        )->name('credit-card.delete');
        Route::post(
            'credit-card/set-default',
            [ProfileController::class, 'setDefaultCreditCard']
        )->name('credit-card.set-default');
        Route::post(
            'credit-card/update',
            [ProfileController::class, 'updateCreditCard']
        )->name('credit-card.update');
    }
);