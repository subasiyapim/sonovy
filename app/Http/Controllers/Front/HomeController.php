<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SiteFeature;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function Laravel\Prompts\password;

class HomeController extends Controller
{
    public function index()
    {
        $site_banner = Setting::where('key', 'site_banner')->first()?->value;
        $partners = Partner::all();
        $site_features = SiteFeature::all();
        $testimonials = Testimonial::all();
        $plans = Plan::active()->with('items')->get();

        return inertia('Front/Index',
            compact('site_banner', 'partners', 'site_features', 'testimonials', 'plans'));
    }
}
