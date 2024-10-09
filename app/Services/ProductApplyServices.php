<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Label;
use Illuminate\Support\Str;

class ProductApplyServices
{

    public static function update(Product $product, $request): void
    {
        $product->update($request);
    }


}
