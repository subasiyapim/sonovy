<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;


class AnalyseService
{
    protected $totalEarnings;
    protected $data;
    protected $groupedData;

    private const COUNTRIES = ['Turkey', 'United States', 'United Kingdom', 'Portugal', 'Spain'];
    private const PLATFORMS = ['Spotify', 'Facebook', 'Youtube', 'Apple', 'Tiktok'];

    public function __construct($data)
    {
        $this->data = $data;
        $this->totalEarnings = $data->sum('earning');
        $this->groupedData = $this->data->groupBy(fn($item) => Carbon::parse($item->sales_date)
            ->locale(app()->getLocale())
            ->translatedFormat('F Y')
        );
    }

    public function countries(): array
    {
        return $this->cacheResults('countries', function () {
            return $this->processEntities(
                'country',
                self::COUNTRIES,
                fn($releaseData, $totalEarnings) => $this->mapReleaseData($releaseData, $totalEarnings),
                'countries'
            );
        });
    }

    public function platforms(): array
    {
        return $this->cacheResults('platforms', function () {
            return $this->processEntities(
                'platform',
                self::PLATFORMS,
                fn($releaseData, $totalEarnings) => $this->mapReleaseData($releaseData, $totalEarnings),
                'platforms'
            );
        });
    }

    private function cacheResults(string $key, \Closure $calculation): array
    {
        $userId = auth()->id(); // Kullanıcı ID'sinin alınması
        $startDate = Cache::get('start_date');
        $endDate = Cache::get('end_date');

        // Cache anahtarını belirleme
        $cacheKey = "analyse_service_{$key}_{$userId}_{$startDate}_{$endDate}";

        // Cache süresi (8 saat)
        $cacheDuration = 8 * 60 * 60; // 8 saat (saniye cinsinden)

        // Hesaplama ve cacheleme
        return Cache::remember($cacheKey, $cacheDuration, $calculation);
    }

    private function processEntities(string $attribute, array $topEntities, callable $releaseMapper, string $key): array
    {
        $entityEarnings = $this->data->groupBy($attribute)->map(function ($entityData, $entity) use (
            $topEntities,
            $releaseMapper
        ) {
            $totalEarnings = $entityData->sum('earning');
            $totalQuantity = $entityData->sum('quantity');
            $releases = $entityData->groupBy('release_name')->map(fn($releaseData) => $releaseMapper($releaseData,
                $totalEarnings)
            );
            return [
                'total_earning' => $totalEarnings,
                'total_quantity' => $totalQuantity,
                'releases' => $releases->toArray(),
            ];
        });

        // Sort and compile top entities and others
        $sortedEarnings = $entityEarnings->sortDesc();
        $topEntitiesData = $sortedEarnings->only($topEntities);
        $otherEntitiesData = $sortedEarnings->except($topEntities);
        $topEntitiesData['Others'] = $this->calculateOtherEntities($otherEntitiesData);

        // Compile releases data
        $releases = $this->data->groupBy('release_name')->map(function ($releaseData) use ($topEntities, $attribute) {
            return [
                'start_date' => Cache::get('start_date'),
                'end_date' => Cache::get('end_date'),
                'release_name' => $releaseData->first()->release_name,
                'total_quantity' => $releaseData->sum('quantity'),
                'total_earning' => Number::currency($releaseData->sum('earning'), 'USD', app()->getLocale()),
                $key => $this->mapInnerEntities($releaseData, $topEntities, $attribute),
            ];
        })->values()->toArray();

        return [
            $key => array_merge($topEntities, ['Others']),
            'releases' => $releases,
        ];
    }

    private function calculateOtherEntities($otherEntitiesData): array
    {
        return [
            'total_earning' => $otherEntitiesData->sum('total_earning'),
            'total_quantity' => $otherEntitiesData->sum('total_quantity'),
            'releases' => [],
        ];
    }

    private function mapReleaseData($releaseData, $totalEarnings): array
    {
        $releaseEarnings = $releaseData->sum('earning');
        $percentage = $totalEarnings > 0 ? ($releaseEarnings / $totalEarnings) * 100 : 0;
        return [
            'release_name' => $releaseData->first()->release_name,
            'total_quantity' => $releaseData->sum('quantity'),
            'total_earning' => Number::currency($releaseEarnings, 'USD', app()->getLocale()),
            'percentage' => round($percentage, 2),
            'quantity' => $releaseData->sum('quantity'),
        ];
    }

    private function mapInnerEntities($releaseData, array $entities, string $attribute): array
    {
        $entityData = [];
        $otherEarnings = 0;
        $otherQuantity = 0;

        foreach ($releaseData->groupBy($attribute) as $entity => $data) {
            $entityEarnings = $data->sum('earning');
            $totalEarnings = $releaseData->sum('earning');
            $percentage = $totalEarnings > 0 ? ($entityEarnings / $totalEarnings) * 100 : 0;
            $quantity = $data->sum('quantity');

            if (in_array($entity, $entities)) {
                $entityData[strtolower($entity)] = [
                    'earning' => Number::currency($entityEarnings, 'USD', app()->getLocale()),
                    'percentage' => round($percentage, 2),
                    'quantity' => $quantity,
                ];
            } else {
                $otherEarnings += $entityEarnings;
                $otherQuantity += $quantity;
            }
        }

        $entityData['others'] = [
            'earning' => Number::currency($otherEarnings, 'USD', app()->getLocale()),
            'percentage' => $releaseData->sum('earning') > 0 ? round(($otherEarnings / $totalEarnings) * 100, 2) : 0,
            'quantity' => $otherQuantity,
        ];

        return $entityData;
    }
}
