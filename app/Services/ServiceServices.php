<?php

namespace App\Services;

use App\Models\Label;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceServices
{

    public static function create(array $data): mixed
    {
        $data["added_by"] = auth()->id();
        $service = Service::create($data);

        return $service;
    }


    public static function search($search): mixed
    {
        return Service::where('name', 'like', '%' . $search . '%')->get();
    }
}
