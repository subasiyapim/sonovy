<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
       
        $artists = Artist::all();
        return Inertia::render('Dashboard', compact('artists'));
    }
}
