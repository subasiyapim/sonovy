<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

/**
 * @method static upsert(array|array[] $roles, string[] $array)
 * @method static advancedFilter()
 */
class Role extends Model implements TranslatableContract
{
    use HasFactory;
    use SoftDeletes;
    use HasAdvancedFilter;
    use Translatable;

    protected $table = 'roles';
    public array $translatedAttributes = ['name'];
    protected $fillable = [
        'code'
    ];

    protected array $filterable = [
        'name',
        'code'
    ];


    protected array $orderable = [
        'name',
        'code',
    ];

    protected array $dates = [
        'created_at',
        'updated_at'
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
