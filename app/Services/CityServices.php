<?php

namespace App\Services;

use App\Models\City;

class CityServices
{
    public static function search($search): mixed
    {
        return City::where('name','LIKE',"%$search%" )->get();
    }



}
