<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\DataTables\HasAdvancedFilter;

class ContactUs extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasAdvancedFilter;

    protected $table = 'contact_us';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];

    protected array $orderable = [
        'name',
        'email',
        'phone',
        'message',
    ];

    protected array $filterable = [
        'name',
        'email',
        'phone',
        'message',
    ];
}
