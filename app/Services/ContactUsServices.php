<?php

namespace App\Services;

use App\Models\ContactUs;
use Illuminate\Support\Str;

class ContactUsServices
{

    public static function create(array $data): mixed
    {
        $label = ContactUs::create($data);

        return $label;
    }
}
