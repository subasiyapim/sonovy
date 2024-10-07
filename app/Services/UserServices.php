<?php

namespace App\Services;

use App\Http\Resources\Panel\CountryResource;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserServices
{

    public static function create(array $data): mixed {

        return User::create($data);
    }

    public static function update(User $user, $request): void
    {

        $user   ->update($request);
    }

    public static function get()
    {
        return User::active()->get();
    }

    public static function search($search): mixed
    {
        return User::whereAny(
            ['name', 'email'],
            'LIKE',
            "%$search%"
        )->get();
    }


}
