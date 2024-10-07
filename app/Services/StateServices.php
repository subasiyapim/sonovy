<?php

namespace App\Services;

use App\Models\City;
use App\Models\State;

class StateServices
{
    public static function search($search): mixed
    {
        return State::where('name','LIKE',"%$search%")->get();
    }



}
