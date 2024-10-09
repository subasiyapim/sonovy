<?php

namespace App\Http\Controllers\Control;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Product;
use App\Models\Label;
use App\Models\Participant;
use App\Models\Song;
use App\Models\User;
use App\Services\ArtistBranchServices;
use App\Services\EarningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('d_1')
            ? $request->input('d_1')
            : Carbon::now()->startOfMonth();
        $end_date = $request->input('d_2')
            ? $request->input('d_2')
            : Carbon::now()->endOfMonth();

        $user = User::find(Auth::id());

        $roles = $user->roles->pluck('code')->toArray();
        $has_admin_role = in_array('admin', $roles);

        $earning_total = EarningService::balance();
        $earning_report = EarningService::balanceReport();

        //Katalog Özeti
        $catalog_counts = self::getCatalogCounts($request);

        $monthly_platform_report = EarningService::monthlyPlatformReport($start_date, $end_date);
        $monthly_earning_report = EarningService::monthlyEarningReport($start_date, $end_date);
        $monthly_listening_report = EarningService::monthlyListeningReport($start_date, $end_date);

        $monthlyTrend = EarningService::getMonthlyTrendRequest($request->has('monthlyTrend') ? $request->get('monthlyTrend') : null,
            $start_date, $end_date);

        //dd($monthlyTrend);
        return inertia('Control/Summary/Index', [
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

    protected static function getCatalogCounts($request): array
    {
        return [
            'product' => Product::when($request->input('d_1') && $request->input('d_2'),
                function ($query) use ($request) {
                    $query->whereBetween('created_at', [request()->input('d_1'), request()->input('d_2')]);
                })->count(),

            'draft_broadcast' => Product::when($request->input('d_1') && $request->input('d_2'),
                function ($query) use ($request) {
                    $query->whereBetween('created_at', [request()->input('d_1'), request()->input('d_2')]);
                })->where('status', ProductStatusEnum::DRAFT->value)->count(),

            'artist' => Artist::when($request->input('d_1') && $request->input('d_2'),
                function ($query) use ($request) {
                    $query->whereBetween('created_at', [request()->input('d_1'), request()->input('d_2')]);
                })->count(),

            'label' => Label::when($request->input('d_1') && $request->input('d_2'), function ($query) use ($request) {
                $query->whereBetween('created_at', [request()->input('d_1'), request()->input('d_2')]);
            })->count(),

            'song' => Song::when($request->input('d_1') && $request->input('d_2'), function ($query) use ($request) {
                $query->whereBetween('created_at', [request()->input('d_1'), request()->input('d_2')]);
            })->count(),

            'participant' => Product::with('songs.participants')
                ->when($request->input('d_1') && $request->input('d_2'), function ($query) use ($request) {
                    $query->whereBetween('created_at', [request()->input('d_1'), request()->input('d_2')]);
                })->whereHas('songs.participants')->count(),
        ];
    }
}
