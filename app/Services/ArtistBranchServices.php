<?php

namespace App\Services;

use App\Models\ArtistBranch;
use Illuminate\Support\Str;

class ArtistBranchServices
{
    public static function get()
    {
        return ArtistBranch::all();
    }

    public static function getBranchesFromInputFormat()
    {
        $data = self::get();

        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'value' => $item['id'],
                'label' => $item['name'],
            ];
        }
        return $result;
    }

}
