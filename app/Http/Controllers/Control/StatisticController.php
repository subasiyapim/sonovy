<?php

namespace App\Http\Controllers\Control;

use App\Enums\BroadcastStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Broadcast;
use App\Models\Label;
use App\Models\Song;
use App\Services\EarningService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class StatisticController extends Controller
{


    public function index(Request $request)
    {
        $user = auth()->user();
        $roles = $user->roles->pluck('code')->toArray();
        $has_admin_role = in_array('admin', $roles);

        $earning_total = EarningService::balance();
        $earning_report = EarningService::balanceReport();

        //Catalog counts
        $catalog_counts = [
            'broadcast' => Broadcast::count(),
            'draft_broadcast' => Broadcast::where('status', BroadcastStatusEnum::DRAFT->value)->count(),
            'artist' => Artist::count(),
            'label' => Label::count(),
            'song' => Song::count(),
            'participant' => Broadcast::with('songs.participants')->whereHas('songs.participants')->count(),

        ];

        $monthly_platform_report = EarningService::monthlyPlatformReport();
        $monthly_earning_report = EarningService::monthlyEarningReport();
        $monthly_listening_report = EarningService::monthlyListeningReport();
        $monthlyTrend = EarningService::getMonthlyTrendRequest($request->has('monthlyTrend') && $request->get('monthlyTrend') || null);

        return inertia('Control/Statistics/Index', [
            'has_admin_role' => $has_admin_role,
            'earning_total' => $earning_total,
            'earning_report' => $earning_report,
            'catalog_counts' => $catalog_counts,
            'monthly_platform_report' => $monthly_platform_report,
            'monthly_earning_report' => $monthly_earning_report,
            'monthly_listening_report' => $monthly_listening_report,
            'monthlyTrend' => $monthlyTrend ?? null,
        ]);

    }

}
