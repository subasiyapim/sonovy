<?php
return [
    'server' => env('OCTANE_SERVER', 'frankenphp'),

    'https' => env('OCTANE_HTTPS', true),

    'workers' => env('OCTANE_WORKERS', 2),
    'tasks_per_worker' => env('OCTANE_TASKS_PER_WORKER', 1000),
    'max_execution_time' => 30,

    'warm' => [
        \Illuminate\Database\DatabaseManager::class,
    ],

    'listeners' => [],

    'flush' => [
        \Illuminate\Database\DatabaseManager::class,
        \Illuminate\Cache\Repository::class,
    ],
];