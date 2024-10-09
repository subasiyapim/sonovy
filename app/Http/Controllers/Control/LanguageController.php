<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslateRequest;
use App\Jobs\TranslateAllTranslationsJob;
use App\Services\LocaleService;

class LanguageController extends Controller
{
    public function sourceLanguages(): array
    {
        return LocaleService::getLocalizationListFromInputFormat();

    }

    public function translate(TranslateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $sourceLang = $request->input('source');
        $targetLang = $request->input('target');
        $chunkSize = 5;
        $skipExisting = $request->input('skip_existing', false);


        TranslateAllTranslationsJob::dispatch($sourceLang, $targetLang, $chunkSize, $skipExisting);

        return redirect()->back()->with(
            [
                'notification' => [
                    'type' => 'success',
                    'message' => __('control.general.success_message')
                ]
            ]
        );
    }

}
