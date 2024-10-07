<?php

namespace Database\Seeders;

use App\Models\PlanItem;
use App\Models\Translations\PlanItemTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanItemSeeder extends Seeder
{
    private static array $items = [
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Sanatçı'
            ],
            'en' => [
                'name' => 'Artist'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Albüm'
            ],
            'en' => [
                'name' => 'Album'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Şarkı'
            ],
            'en' => [
                'name' => 'Song'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Komisyon Oranı'
            ],
            'en' => [
                'name' => 'Commission Rate'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Label'
            ],
            'en' => [
                'name' => 'Label'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Katılımcı'
            ],
            'en' => [
                'name' => 'Participant'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Bölgesel Sınırlamalar'
            ],
            'en' => [
                'name' => 'Regional Restrictions'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Telif Yönetimi'
            ],
            'en' => [
                'name' => 'Copyright'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Müşteri destek süresi'
            ],
            'en' => [
                'name' => 'Customer Support Period'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Mağaza Otomasyonu'
            ],
            'en' => [
                'name' => 'Mağaza Otomasyonu'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Kendi UPC\'nizi kullanma'
            ],
            'en' => [
                'name' => 'Use your own UPC'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Kendi çıkış tarihinizi planla'
            ],
            'en' => [
                'name' => 'Plan your own release date'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Ön Satış Tarihi'
            ],
            'en' => [
                'name' => 'Pre-Sale Date'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'ISRC oluşturabilme'
            ],
            'en' => [
                'name' => 'Create ISRC'
            ]
        ],
        [
            'type' => 'number',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Promosyon'
            ],
            'en' => [
                'name' => 'Promotion'
            ]
        ],
        [
            'type' => 'boolean',
            'category' => '1',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Alt Site'
            ],
            'en' => [
                'name' => 'Sub Site'
            ]
        ],
        [
            'type' => 'number',
            'category' => '2',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Sanatçı'
            ],
            'en' => [
                'name' => 'Artist'
            ]
        ],
        [
            'type' => 'number',
            'category' => '2',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Albüm'
            ],
            'en' => [
                'name' => 'Album'
            ]
        ],
        [
            'type' => 'number',
            'category' => '2',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Şarkı'
            ],
            'en' => [
                'name' => 'Song'
            ]
        ],
        [
            'type' => 'number',
            'category' => '2',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Komisyon oranı'
            ],
            'en' => [
                'name' => 'Commission rate'
            ]
        ],
        [
            'type' => 'number',
            'category' => '2',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Label'
            ],
            'en' => [
                'name' => 'Label'
            ]
        ],
        [
            'type' => 'number',
            'category' => '2',
            'is_deletable' => '0',
            'tr' => [
                'name' => 'Katılımcı'
            ],
            'en' => [
                'name' => 'Participant'
            ]
        ]


    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlanItem::get()->each->delete();

        foreach (self::$items as $item) {
            $plan_item_translation = PlanItem::where('category', $item['category'])
                ->where('type', $item['type'])
                ->whereHas('translations', function ($query) use ($item) {
                    $query->where('name', $item['tr']['name']);
                })->first();

            if ($plan_item_translation) {
                continue;
            }

            $planItem = PlanItem::create([
                'code' => Str::slug($item['en']['name']),
                'category' => $item['category'],
                'type' => $item['type'],
                'is_deletable' => $item['is_deletable'],
            ]);

            foreach (['tr', 'en'] as $locale) {
                PlanItemTranslation::create([
                        'plan_item_id' => $planItem->id,
                        'locale' => $locale,
                        'name' => $item[$locale]['name'],
                    ]
                );
            }

            $planItem->save();
        }
    }
}
