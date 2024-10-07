<?php

namespace App\Models;

use App\Enums\IntegrationTypeEnum;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static advancedFilter()
 */
class Integration extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'integrations';

    protected $fillable = [
        'name',
        'code',
        'type',
        'class_name',
        'url',
        'key',
        'secret',
        'status',
        'description',
    ];

    protected array $filterable = [
        'id',
        'name',
        'type',
        'url',
        'status'
    ];

    protected array $orderable = [
        'id',
        'name',
        'type',
        'url',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = [
        'type_text'
    ];

    protected function typeText(): Attribute
    {
        return Attribute::make(
            get: fn() => IntegrationTypeEnum::getTitles()[$this->type]
        );
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
