<?php

namespace App\Http\Middleware;

use App\Enums\AnnouncementTypeEnum;
use App\Models\AnnouncementUser;
use App\Models\Setting;
use App\Services\LocaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    // Eğer root template tanımı gerekiyorsa buraya ekleyin.

    /**
     * Cache duration in seconds.
     *
     * @var int
     */
    protected $cacheTime = 480; // 8 dakika

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Locale belirleme
        $appLocale = session('appLocale', $user->interface_language ?? config('app.locale'));

        // Çevirileri cache'leme
        $translations = Cache::remember("translations_{$appLocale}", $this->cacheTime, function () use ($appLocale) {
            return LocaleService::getLanguageFile($appLocale, ['client', 'control', 'sidebar', 'auth']);
        });

        // Ayarları cache'leme
        $settings = Cache::remember('settings', $this->cacheTime, function () {
            return Setting::pluck('value', 'key');
        });

        $data = [
            'ziggy' => function () use ($request) {
                return array_merge(
                    (new Ziggy)->toArray(),
                    ['location' => $request->url()]
                );
            },
            'auth' => [
                'user' => $user,
            ],
            'intent' => fn() => $request->session()->get('intent', []),
            'notification' => fn() => $request->session()->get('notification', []),
            'production' => config('app.env') === 'production',
            'default_barcode_type' => 1,
            'editable_catalogues' => [
                'product' => true,
                'song' => true,
                'artist' => false,
                'label' => false,
            ],
            'currentLocale' => $appLocale,
            'defaultLocale' => config('project.default_locale'),
            'supportedLocales' => LocaleService::getLocalizationList(),
            'translations' => $translations,
            'notifications' => [],
            'maintenance' => null,
        ];

        if ($this->isTenant()) {
            $data['verification_code_expire'] = (int) ($settings['verification_code_expire'] ?? 1);

            $data['site_settings'] = fn() => $settings;
        }

        if (Auth::check()) {
            // Roller ve izinler
            $data['auth']['user']['roles'] = $user->roles;
            $data['auth']['user']['permissions'] = function () use ($user) {
                return $user->roles()
                    ->with('permissions')
                    ->get()
                    ->pluck('permissions.*.code')
                    ->flatten()
                    ->unique()
                    ->values()
                    ->all();
            };

            // Bildirimler
            $notifications = AnnouncementUser::with('announcement')
                ->where('user_id', $user->id)
                ->where('type', AnnouncementTypeEnum::SITE->value)
                ->latest()
                ->get();

            if ($notifications->isNotEmpty()) {
                $data['notifications'] = $notifications;
            }

            // Bakım bildirimi
            $maintenance = AnnouncementUser::where('user_id', $user->id)
                ->where('type', AnnouncementTypeEnum::MAINTENANCE->value)
                ->latest()
                ->first();

            if ($maintenance) {
                $data['maintenance'] = $maintenance;
            }
        }

        return array_merge(parent::share($request), $data);
    }

    /**
     * Check if the current request is for a tenant.
     *
     * @return bool
     */
    protected function isTenant(): bool
    {
        return function_exists('tenant') && tenant();
    }
}