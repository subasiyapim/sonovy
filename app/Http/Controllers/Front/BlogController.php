<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;


class BlogController extends Controller
{
    public function show()
    {
        return inertia('Front/Blog/Show');
    }
}
