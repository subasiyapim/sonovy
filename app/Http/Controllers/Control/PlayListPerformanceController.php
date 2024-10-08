<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

class PlayListPerformanceController extends Controller
{


    public function index()
    {


        return inertia('Control/PlaylistPerformance/Index');
    }

}
