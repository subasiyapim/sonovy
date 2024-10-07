<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\Platform;
use Illuminate\Support\Str;

/**
 * @method static advancedFilter()
 */
class PlatformService
{

    public static function search($search): mixed
    {
        return Platform::where('name', 'like', '%' . $search . '%')->get();
    }
}
