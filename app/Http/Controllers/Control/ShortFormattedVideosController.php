<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

class ShortFormattedVideosController extends Controller
{


    public function index()
    {


        return inertia('Control/ShortFormattedVideos/Index');
    }

}
