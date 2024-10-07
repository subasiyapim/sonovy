<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HelperTrait
{
    public function logger($dir, $file_name, $data): void
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . $dir . '/' . $file_name . '.log')
        ])
            ->info($data);
    }

    public function deleteTranslations(Request $request, Model $model, $column = null): void
    {
        $className = class_basename($model);
        $column_name = $column ?? strtolower($className) . '_id';

        $translationClassName = get_class($model) . 'Translation';

        $translationClassName::where($column_name, $model->id)
            ->whereNotIn(
                'locale',
                array_keys($request->validated()['translations'])
            )
            ->get()
            ->each
            ->delete();
    }
}
