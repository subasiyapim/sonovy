<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

class DistributionReportController extends Controller
{


    public function index()
    {


        return inertia('Control/DistributionReports/Index');
    }

}
