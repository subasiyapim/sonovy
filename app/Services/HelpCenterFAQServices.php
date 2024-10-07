<?php

namespace App\Services;

use App\Models\HelpCenterFAQ;
use Illuminate\Support\Str;

class HelpCenterFAQServices
{

    public static function imageUpload($model, $image): void
    {

        $name = $model->title[LocaleService::getLocalizationList()[0]];
        $file_name = Str::slug($name);
        $collection = $model->getTable();

        MediaServices::upload($model, $image, $name, $file_name, $collection);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data): mixed
    {
        $faq = HelpCenterFAQ::create($data);
        if (isset($data['image'])) {
            HelpCenterFAQServices::imageUpload($faq, $data['image']);
        }

        return $faq;
    }

    public static function update(HelpCenterFAQ $faq, $request): void
    {
        $faq->update($request);
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return HelpCenterFAQ::where('title', 'like', '%' . $search . '%')
            ->orWhere('subtitle', 'like', '%' . $search . '%')
            ->orWhere('question', 'like', '%' . $search . '%')
            ->orWhere('answer', 'like', '%' . $search . '%')->get();
    }
}
