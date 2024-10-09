<?php

namespace App\Services;

use App\Enums\ProductStatusEnum;
use App\Models\Song;
use Illuminate\Support\Str;

class SongServices
{

    public static function create(array $data): mixed
    {
        return Song::create($data);
    }

    public static function search($search): mixed
    {
        return Song::where('name', 'like', '%'.$search.'%')->get();
    }

    public static function searchForCatalog($search): mixed
    {
        return Song::with('participants.user', 'broadcasts')
            ->whereHas('broadcasts', function ($query) {
                $query->where('status', ProductStatusEnum::APPROVED->value);
            })
            ->where('name', 'like', '%'.$search.'%')
            ->get();
    }


}
