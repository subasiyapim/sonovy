<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'services';

    protected $fillable = [
        'name',
    ];

    protected array $filterable = [
        'name',
    ];
    protected array $orderable = [
        'name',
    ];
}
