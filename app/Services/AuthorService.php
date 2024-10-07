<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Support\Str;

class AuthorService
{


    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data): mixed
    {
        $author = Author::create($data);



        return $author;
    }

    public static function update(Author $author, $request): void
    {
        $author->update($request);

    }

    /**
     * @param $search
     * @return mixed
     */
    public static function search($search): mixed
    {
        return Author::where('name', 'like', '%' . $search . '%')->get();
    }
}
