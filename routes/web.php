<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group(
    [
        'middleware' => [
            'web'
        ]
    ],
    function () {

        Route::get('/', function () {
            return view('welcome');
        });
    }
);
