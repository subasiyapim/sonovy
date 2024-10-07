<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\PlanItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $plan_item = PlanItem::first();
        if ($plan_item){
            foreach (self::$features as $feature) {
                $feature['plan_item_id'] = $plan_item->id;
                Feature::create($feature);
            }
        }

    }

    /*public static array $periods = [
        1 => 'one_time',
        2 => 'monthly',
        3 => 'yearly',
    ];*/


    public static array $features = [

        [
            'type' => 'number',
            'is_active' => '1',
            'period' => '1',
            'limit' => '1',
            'title' => [
                'tr' => 'Sanatçı',
                'en' => 'Artist'
            ],
            'description' => [
                'tr' => 'Extra Sanatçı Açıklama',
                'en' => 'Extra Artist Description'
            ]
        ],
    ];
}
