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

class Announcement extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    use SoftDeletes;

    protected $table = 'announcements';

    protected $fillable = [
        'name',
        'type',
        'content_type',
        'content',
        'template_id',
        'from',
        'to',
        'receivers',
        'url'
    ];

    protected array $filterable = [
        'name',
        'type',
        'content',
        'from',
        'to',
        'receivers'
    ];

    protected array $orderable = [
        'name',
        'type',
        'content',
        'from',
        'to',
        'receivers'
    ];

    protected $appends = ['type_text', 'from_date', 'to_date'];

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => explode(',', $value),
            set: fn(array $value) => implode(',', $value),
        );
    }

    protected function typeText(): Attribute
    {
        return Attribute::make(
        //get: fn () => AnnouncementTypeEnum::getTitles()[$this->type],
            get: fn() => implode(',', array_map(fn($item) => AnnouncementTypeEnum::getTitles()[$item], $this->type)),
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_user',
            'announcement_id', 'user_id')
            ->withPivot('type', 'status');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AnnouncementTemplate::class, 'template_id');
    }

    protected function fromDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->from ? date("Y-m-d", strtotime($this->from)) : '',
        );
    }

    protected function toDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->to ? date("Y-m-d", strtotime($this->to)) : '',
        );
    }
}
