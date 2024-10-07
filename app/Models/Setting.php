<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static upsert(array|array[] $settings, string[] $array, string[] $array1)
 * @method static get()
 * @method static advancedFilter()
 */
class Setting extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'input_type',
    ];

    protected array $filterable = [
        'key',
        'name',
        'value',
        'input_type',
    ];

    protected array $orderable = [
        'key',
        'name',
        'value',
        'input_type',
    ];
    public static array $INPUT_TYPE_SELECT = [
        [
            'key' => 1,
            'name' => 'Text',
            'value' => 'text',
        ],
        [
            'key' => 2,
            'name' => 'Textarea',
            'value' => 'textarea',
        ],
        [
            'key' => 3,
            'name' => 'Select',
            'value' => 'select',
        ],
        [
            'key' => 4,
            'name' => 'Radio',
            'value' => 'radio',
        ],
        [
            'key' => 5,
            'name' => 'Checkbox',
            'value' => 'checkbox',
        ],
        [
            'key' => 6,
            'name' => 'Date',
            'value' => 'date',
        ],
        [
            'key' => 7,
            'name' => 'Time',
            'value' => 'time',
        ],
        [
            'key' => 8,
            'name' => 'Datetime',
            'value' => 'datetime',
        ],
        [
            'key' => 9,
            'name' => 'File',
            'value' => 'file',
        ],
        [
            'key' => 10,
            'name' => 'Image',
            'value' => 'image',
        ],
        [
            'key' => 11,
            'name' => 'Email',
            'value' => 'email'
        ],
        [
            'key' => 12,
            'name' => 'MultiSelect',
            'value' => 'multiselect'
        ]
    ];

    protected array $dates = [
        'created_at',
        'updated_at'
    ];

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}
