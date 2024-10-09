<?php

namespace App\Providers;

use App\Services\LocaleService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        config(['translatable.locales' => LocaleService::getLocalizationList()]);
        JsonResource::withoutWrapping();
    }
}
