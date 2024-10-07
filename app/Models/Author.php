<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static upsert(mixed[] $toArray, string[] $array, string[] $array1)
 * @method static inRandomOrder()
 */
class Author extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    protected $table = 'authors';

    protected $fillable = [
        'name',
        'birth_date',
        'is_registered_pro',
        'pro_id',
        'cae_ipi_number',
    ];

    protected array $filterable = [
        'name',
        'birth_date',
        'is_registered_pro',
        'pro_id',
        'cae_ipi_number',
    ];

    protected array $orderable = [
        'name',
        'birth_date',
        'is_registered_pro',
        'pro_id',
        'cae_ipi_number',
    ];


}
