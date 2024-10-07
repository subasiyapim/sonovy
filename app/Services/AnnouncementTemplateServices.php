<?php

namespace App\Services;

use App\Models\AnnouncementTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementTemplateServices
{

    public static function create(array $data): mixed {

        return AnnouncementTemplate::create($data);
    }

    public static function update(AnnouncementTemplate $announcement_template, $request): void
    {

        $announcement_template->update($request);
    }

    public static function get()
    {
        return AnnouncementTemplate::active()->get();
    }

    public static function search($search): mixed
    {
        // $search_arr = explode('&', $search);
        // $type = explode('=', $search_arr[1]);

        return AnnouncementTemplate::where('name', 'LIKE', "%$search[0]%")
            ->get();
    }


}
