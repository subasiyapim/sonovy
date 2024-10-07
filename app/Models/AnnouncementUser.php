<?php

namespace App\Models;

use App\Enums\AnnouncementTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnouncementUser extends Model
{

    use SoftDeletes;

    protected $table = 'announcement_user';

    protected $fillable = [
        'announcement_id',
        'user_id',
        'type',
        'status',
        'content',
    ];

    protected $casts = [
        'type' => AnnouncementTypeEnum::class
    ];


    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
