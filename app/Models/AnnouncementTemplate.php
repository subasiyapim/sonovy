<?php

namespace App\Models;

use App\Enums\AnnouncementTypeEnum;
use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnouncementTemplate extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $table = 'announcement_templates';

    protected $fillable = [
        'name',
        'type',
        'send_type',
        'content',
    ];

    protected array $filterable = [
        'name',
        'type',
        'send_type',
        'content',
    ];

    protected array $orderable = [
        'name',
        'type',
        'send_type',
        'content',
    ];

    protected $appends = ['type_text'];

    protected function typeText(): Attribute
    {
        return Attribute::make(
            get: fn () => AnnouncementTypeEnum::getTitles()[$this->type],
        );
    }
}
