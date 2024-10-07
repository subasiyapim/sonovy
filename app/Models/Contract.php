<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static upsert(mixed[] $toArray, string[] $array, string[] $array1)
 * @method static inRandomOrder()
 */
class Contract extends Model
{
    use HasFactory;
    use HasAdvancedFilter;
    protected $table = 'contracts';

    protected $fillable = [
        'representetive_share_ratio',
        'physical_share_ratio',
        'digital_share_ratio',
        'contract_start_date',
        'contract_end_date',
        'auto_renewal',
        'scope',
    ];

    protected array $filterable = [
        'representetive_share_ratio',
        'physical_share_ratio',
        'digital_share_ratio',
        'contract_start_date',
        'contract_end_date',
        'auto_renewal',
        'scope',
    ];

    protected array $orderable = [
        'representetive_share_ratio',
        'physical_share_ratio',
        'digital_share_ratio',
        'contract_start_date',
        'contract_end_date',
        'auto_renewal',
        'scope',
    ];


}
