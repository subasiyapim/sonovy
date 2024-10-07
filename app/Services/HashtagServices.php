<?php

namespace App\Services;

use App\Models\Hashtag;

class HashtagServices
{

    public static function getHashtags()
    {
        return Hashtag::get(['id', 'name', 'code']);
    }

}
