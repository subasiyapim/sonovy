<?php

namespace App\Http\Middleware;

use App\Enums\AnnouncementTypeEnum;
use App\Models\AnnouncementUser;
use App\Models\Setting;
use App\Models\System\Menu;
use App\Models\User;
use App\Services\LocaleService;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

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
        $translations = LocaleService::getLanguageFile(session('appLocale',
            $request->user()
                ? $request->user()->interface_language
                : config('app.locale')),
            ['client', 'control', 'sidebar', 'auth']);

        $data = [
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'auth' => [
                'user' => $request->user(),
            ],
            'verification_code_expire' => intval(Setting::where('key',
                'verification_code_expire')->first()->value ?? 1),
            'notification' => fn() => $request->session()->get('notification', []),
            'intent' => fn() => $request->session()->get('intent', []),
            'production' => config('app.env') === 'production',
            'site_settings' => function () {
                $settings_arr = [];
                $settings = \App\Models\Setting::get(['value', 'key', 'value']);

                foreach ($settings as $setting) {
                    $settings_arr[$setting->key] = $setting->value;
                }

                return $settings_arr;
            },
            'default_barcode_type' => 1, //UPC
            'editable_catalogues' => [
                'product' => true,
                'song' => true,
                'artist' => false,
                'label' => false,
            ],
            'currentLocale' => session('appLocale', Auth::user()->interface_language ?? config('app.locale')),
            'defaultLocale' => config('project.default_locale'),
            'supportedLocales' => LocaleService::getLocalizationList(),
            'translations' => $translations,
            'notifications' => [],
            'maintenance' => null,
        ];
        if (Auth::check()) {
            $data['auth.user.roles'] = Auth::user()->roles;
            $data['auth.user.permissions'] = function () {
                $roles = Auth::user()->roles()->with('permissions')->get();
                $permissionsArray = [];

                foreach ($roles as $role) {
                    foreach ($role->permissions as $permissions) {
                        $permissionsArray[] = $permissions->code;
                    }
                }

                return array_unique($permissionsArray);
            };

            $notifications = \App\Models\AnnouncementUser::with('announcement')
                ->where('user_id', Auth::id())
                ->where('type', AnnouncementTypeEnum::SITE->value)
                ->orderBy('created_at', 'desc')
                ->get();
            if ($notifications) {
                $data['notifications'] = $notifications;
            }

            $maintenance = \App\Models\AnnouncementUser::where('user_id', Auth::id())
                ->where('type', AnnouncementTypeEnum::MAINTENANCE->value)->latest()->first();
            if ($maintenance) {
                $data['maintenance'] = $maintenance;
            }
        }

        return array_merge(parent::share($request), $data);
    }
}
