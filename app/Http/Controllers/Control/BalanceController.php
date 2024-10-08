<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

class BalanceController extends Controller
{


    public function index()
    {


        return inertia('Control/Balance/Index');
    }

}
