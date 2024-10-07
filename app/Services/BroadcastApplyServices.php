<?php

namespace App\Services;

use App\Models\Broadcast;
use App\Models\Label;
use Illuminate\Support\Str;

class BroadcastApplyServices
{

    public static function update(Broadcast $broadcast, $request): void
    {
        $broadcast->update($request);
    }


}
