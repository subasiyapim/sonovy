<?php

use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Contracts\OperationTerminated;

return [
    'server' => env('OCTANE_SERVER', 'frankenphp'),

    'https' => env('OCTANE_HTTPS', true),

    'workers' => env('OCTANE_WORKERS', 2),
    'tasks_per_worker' => env('OCTANE_TASKS_PER_WORKER', 1000),
    'max_execution_time' => env('OCTANE_MAX_EXECUTION_TIME', 30),

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

        OperationTerminated::class => [
            function () {
                if (app()->bound('redis')) {
                    app('redis')->connection()->client()->disconnect();
                }
            },
        ],
    ],

    'cache' => [
        'headers' => [
            'X-Powered-By' => 'Sonovy',
            'Cache-Control' => 'public, max-age=3600',
        ],
    ],
];