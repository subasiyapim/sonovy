<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $plans = [
            [
                'monthly_price' => 0,
                'annual_price' => 0,
                'sort_order' => 1,
                'is_active' => 1,
                'plan_translations' => [
                    [
                        'locale' => 'tr',
                        'name' => 'Ücretsiz',
                        'description' => 'Ücretsiz plan',
                    ],
                    [
                        'locale' => 'en',
                        'name' => 'Free',
                        'description' => 'Free plan',
                    ]
                ],
            ],
            [
                'monthly_price' => 10,
                'annual_price' => 100,
                'sort_order' => 2,
                'is_active' => 1,
                'plan_translations' => [
                    [
                        'locale' => 'tr',
                        'name' => 'Temel',
                        'description' => 'Temel plan',
                    ],
                    [
                        'locale' => 'en',
                        'name' => 'Basic',
                        'description' => 'Basic plan',
                    ]
                ],
            ],
            [
                'monthly_price' => 20,
                'annual_price' => 200,
                'sort_order' => 3,
                'is_active' => 1,
                'plan_translations' => [
                    [
                        'locale' => 'tr',
                        'name' => 'Pro',
                        'description' => 'Profesyonel plan',
                    ],
                    [
                        'locale' => 'en',
                        'name' => 'Pro',
                        'description' => 'Pro plan',
                    ]
                ],
            ],
            [
                'monthly_price' => 30,
                'annual_price' => 300,
                'sort_order' => 4,
                'is_active' => 1,
                'plan_translations' => [
                    [
                        'locale' => 'tr',
                        'name' => 'Gelişmiş',
                        'description' => 'Enterprise plan',
                    ],
                    [
                        'locale' => 'en',
                        'name' => 'Enterprise',
                        'description' => 'Enterprise plan',
                    ]
                ],
            ],
            [
                'monthly_price' => 40,
                'annual_price' => 400,
                'sort_order' => 5,
                'is_active' => 1,
                'plan_translations' => [
                    [
                        'locale' => 'tr',
                        'name' => 'Özel',
                        'description' => 'Özel plan',
                    ],
                    [
                        'locale' => 'en',
                        'name' => 'Custom',
                        'description' => 'Custom plan',
                    ]
                ],
            ]
        ];
        PlanItem::get()->each->delete();

        $this->call(PlanItemSeeder::class);

        foreach ($plans as $row) {

            $plan = Plan::firstOrCreate([
                'monthly_price' => $row['monthly_price'],
                'annual_price' => $row['annual_price'],
                'sort_order' => $row['sort_order'],
                'is_active' => $row['is_active'],
            ]);

            $plan->translations()->delete();
            $plan->translations()->createMany($row['plan_translations']);
            $plan->items()->detach();

            $items = PlanItem::active()->inRandomOrder()->limit(5)->get();

            foreach ($items as $item) {
                $plan->items()->attach($item->id, ['value' => $item->type == 'boolean' ? rand(0, 1) : rand(1, 1000)]);
            }
        }
    }
}
