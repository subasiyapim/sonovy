<?php

namespace App\Services;

use App\Http\Resources\Panel\CountryResource;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserServices
{

    public static function create(array $data): mixed
    {

        return User::create($data);
    }

    public static function update(User $user, $request): void
    {

        $user->update($request);
    }

    public static function get()
    {
        return User::active()->get();
    }

    public static function search($search): mixed
    {
        return User::whereAny(
            ['name', 'email'],
            'LIKE',
            "%$search%"
        )->get();
    }


    public static function statistics($statistic, $period = 'month')
    {
        $cacheKey = $statistic.'_'.$period;

        $cacheTime = match ($period) {
            'day' => 24 * 60,
            'week' => 7 * 24 * 60,
            'year' => 365 * 24 * 60,
            default => 12 * 60,
        };

        $startDate = match ($period) {
            'day' => Carbon::now()->subDays(6)->startOfDay(),
            'week' => Carbon::now()->subWeeks(6)->startOfWeek(),
            'year' => Carbon::now()->subYears(6)->startOfYear(),
            default => Carbon::now()->subMonths(6)->startOfMonth(),
        };

        $dateFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'year' => '%Y',
            default => '%Y-%m',
        };

        return match ($statistic) {
            'users' => self::getUSersGroupedByPeriod($cacheKey, $cacheTime, $period, $startDate, $dateFormat),
            'active_users' => self::getActiveUsersGroupedByPeriod($cacheKey, $cacheTime, $period, $startDate,
                $dateFormat),
            default => null,
        };

    }

    public static function getUSersGroupedByPeriod($cacheKey, $cacheTime, $period, $startDate, $dateFormat)
    {
        return Cache::remember($cacheKey, $cacheTime, function () use ($period, $startDate, $dateFormat) {


            $users = User::where('created_at', '>=', $startDate)
                ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, users.name, count(*) as count")
                ->groupBy('period', 'users.name')
                ->orderBy('period')
                ->get();

            $totalCount = $users->sum('count');

            $result = $users->groupBy('period')->map(function ($items, $period) use ($totalCount) {
                $periodTotal = $items->sum('count');
                return [
                    'period' => $period,
                    'users' => $items->map(fn($item) => $item->name)->toArray(),
                    'total_count' => $periodTotal,
                    'percentage_of_total' => round(($periodTotal / $totalCount) * 100, 2),
                ];
            })->values()->toArray();

            return [
                'total' => $totalCount,
                'data' => $result,
                'last_three_users' => $users->take(3)->map(fn($item) => $item->name)->toArray()
            ];
        });
    }

    public static function getActiveUsersGroupedByPeriod($cacheKey, $cacheTime, $period, $startDate, $dateFormat)
    {
        return Cache::remember($cacheKey, $cacheTime, function () use ($period, $startDate, $dateFormat) {
            $users = User::with('products')
                ->whereHas('products')
                ->withCount('products')
                ->orderBy('products_count', 'desc')
                ->take(5)
                ->get(['name']);

            return $users->map(function ($user) {
                return [
                    'label' => Str::limit($user->name, 3),
                    'name' => $user->name,
                    'value' => $user->products_count,
                ];
            })->toArray();

        });
    }

}
