<?php

namespace App\Providers;

use App\Services\LocaleService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject(__('notification.verify_email.subject'))
                ->greeting(__('notification.verify_email.greeting'))
                ->line(__('notification.verify_email.line_1'))
                ->action(__('notification.verify_email.action'), $url)
                ->salutation(__('notification.verify_email.salutation'));
        });
        
    }
}
