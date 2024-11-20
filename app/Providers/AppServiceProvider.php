<?php

namespace App\Providers;

use App\Events\NewNotificationEvent;
use App\Listeners\CheckACRListener;
use App\Services\LocaleService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenancyEnded;

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

        // Tenancy initialized event
        \Event::listen(TenancyInitialized::class, function () {
            config([
                'media-library.temporary_directory_path' => storage_path('app/tenant_'.tenant('domain').'/media-library/temp'),
                'media-library.disk_name' => 'tenant_'.tenant('domain'),
            ]);
        });

        // Optionally handle when tenancy ends
        \Event::listen(TenancyEnded::class, function () {
            config([
                'media-library.temporary_directory_path' => storage_path('app/media-library/temp'),
            ]);
        });
        Event::listen(
            NewNotificationEvent::class,
            CheckACRListener::class
        );

    }
}
