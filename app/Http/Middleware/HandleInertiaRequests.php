<?php

namespace App\Http\Middleware;

use App\Enums\AnnouncementTypeEnum;
use App\Models\AnnouncementUser;
use App\Models\Setting;
use App\Services\LocaleService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Stancl\Tenancy\Facades\Tenancy;

class HandleInertiaRequests extends Middleware
{
    /**
     * Cache duration in seconds.
     */
    protected $cacheTime = 480; // 8 minutes

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $tenant = null;
        $tenantKey = null;

        if (Tenancy::identifyTenant()) {
            $tenant = tenant();
            $tenantKey = $tenant ? $tenant->getTenantKey() : null;
        }

        $user = $request->user();

        // Locale setting
        $appLocale = session('appLocale', $user->interface_language ?? config('app.locale'));

        // Cached translations
        $translations = Cache::remember(
            "translations_{$appLocale}",
            $this->cacheTime,
            fn() => LocaleService::getLanguageFile($appLocale, ['client', 'control', 'sidebar', 'auth'])
        );

        // Cached site settings
        $settings = Cache::remember(
            'settings',
            $this->cacheTime,
            fn() => Setting::pluck('value', 'key')->toArray()
        );

        $data = [
            'csrf_token' => csrf_token(),
            'ziggy' => fn() => array_merge((new Ziggy)->toArray(), ['location' => $request->url()]),
            'auth' => [
                'user' => $user,
                'roles' => Auth::check() ? $user->roles : [],
                'permissions' => Auth::check() ? PermissionService::getUserPermissions($user) : [],
            ],
            'intent' => fn() => $request->session()->get('intent', []),
            'notification' => fn() => $request->session()->get('notification', []),
            'admin_id' => fn() => $request->session()->get('admin_id', []),
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
            'notifications' => fn() => $this->getNotifications($user),
            'maintenance' => fn() => $this->getMaintenanceNotification($user),
            'site_settings' => $settings,
        ];

        if ($this->isTenant()) {
            $data['verification_code_expire'] = (int) ($settings['verification_code_expire'] ?? 1);
        }

        return array_merge(parent::share($request), $data);
    }

    /**
     * Check if the current request is for a tenant.
     */
    protected function isTenant(): bool
    {
        return function_exists('tenant') && tenant();
    }

    /**
     * Fetch user notifications.
     */
    protected function getNotifications($user)
    {
        if (!$user) {
            return [];
        }

        return AnnouncementUser::with('announcement')
            ->where('user_id', $user->id)
            ->where('type', AnnouncementTypeEnum::SITE->value)
            ->latest()
            ->get()
            ->toArray();
    }

    /**
     * Fetch maintenance notification for the user.
     */
    protected function getMaintenanceNotification($user)
    {
        if (!$user) {
            return null;
        }

        return AnnouncementUser::where('user_id', $user->id)
            ->where('type', AnnouncementTypeEnum::MAINTENANCE->value)
            ->latest()
            ->first();
    }
}
