<?php

namespace App\Services;

use App\Models\HelpCenterArticle;
use Illuminate\Support\Str;

class HelpCenterArticleServices
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
        $article = HelpCenterArticle::create($data);
        if (isset($data['image'])) {
            HelpCenterArticleServices::imageUpload($article, $data['image']);
        }

        return $article;
    }

    public static function update(HelpCenterArticle $article, $request): void
    {
        $article->update($request);
    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        //TODO SEARCH İN JSON
        return HelpCenterArticle::latest()->get();
        // return HelpCenterArticle::whereJsonContains( 'title', '%'.$search.'%')->get();
    }
}
