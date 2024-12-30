<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;


class FinanceAnalysisController extends Controller
{
    public function index()
    {


        return inertia('Control/Finance/Analysis/Index', []);
    }
}
