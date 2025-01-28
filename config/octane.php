<?php

use Laravel\Octane\Contracts\OperationTerminated;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\WorkerStarting;

return [
    'server' => env('OCTANE_SERVER', 'frankenphp'),

    'https' => env('OCTANE_HTTPS', true),

    'workers' => env('OCTANE_WORKERS', 2),
    'tasks_per_worker' => env('OCTANE_TASKS_PER_WORKER', 1000),
    'max_execution_time' => 30,

    'warm' => [
        \Illuminate\Database\DatabaseManager::class,
    ],

    'listeners' => [
        WorkerStarting::class => [
            function () {
                if (app()->bound('redis')) {
                    app('redis')->connection()->client()->disconnect();
                    app('redis')->connect();
                }
            },
        ],

        RequestTerminated::class => [
            function () {
                if (app()->bound('redis')) {
                    app('redis')->connection()->client()->disconnect();
                }

                // Request sonrası temizlik
                if (app()->bound('db')) {
                    app('db')->disconnect();
                }
            },
        ],
    ],

    'flush' => [
        \Illuminate\Database\DatabaseManager::class,
        \Illuminate\Cache\Repository::class,
    ],
];