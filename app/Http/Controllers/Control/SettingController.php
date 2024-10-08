<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::advancedFilter();

        return inertia('Control/Settings/Index', compact('settings'));
    }

    public function edit(Setting $setting)
    {
        return inertia('Control/Settings/Edit', [
            'setting' => [
                'id' => $setting->id,
                'key' => $setting->key,
                'name' => $setting->name,
                'description' => $setting->description,
                'value' => $setting->value,
                'input_type' => Setting::$INPUT_TYPE_SELECT[$setting->input_type - 1]['value'],
            ],
        ]);
    }

    public function update(Request $request, Setting $setting)
    {
        $rules = $request->type === 'file' ? ['required', 'mimes:jpeg,jpg,png'] : ['required'];

        $request->validate([
            'value' => $rules
        ]);

        if ($request->type === 'file') {

            if (File::exists(public_path($setting->value))) {
                File::delete(public_path($setting->value));
            }

            if (!File::exists(public_path('assets/images'))) {
                File::makeDirectory(public_path('assets/images'), 0777, true, true);
            }

            $file_name = $request->key.'-'.uniqid().'.'.$request->file('value')->extension();

            $request->file('value')->move(public_path('assets/images'), $file_name);

            $setting->update(['value' => '/assets/images/'.$file_name]);
        } else {
            $setting->update(['value' => $request->value]);
        }

        $settings = Setting::pluck('value', 'key')->all();

        //$this->updateSettingsConfigFile($settings);

        return to_route('dashboard.settings.index')
            ->with([
                'notification' => __('panel.notification_updated', ['model' => __('panel.setting.title_singular')])
            ]);
    }

    protected function updateSettingsConfigFile(array $settings): void
    {
        $path = config_path('settings.php');

        $content = "<?php\n\nreturn ".var_export($settings, true).";\n";

        file_put_contents($path, $content);

        Artisan::call('optimize');
    }

    public function show(Setting $setting)
    {
        return response()->json([
            'id' => $setting->id,
            'key' => $setting->key,
            'name' => $setting->name,
            'value' => $setting->value,
            'input_type' => __('setting.input_type.'.Setting::$INPUT_TYPE_SELECT[$setting->input_type - 1]['value']),
        ]);
    }
}
