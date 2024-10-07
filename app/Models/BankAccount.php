<?php

namespace App\Models;

use App\Traits\DataTables\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $fillable = [
        'user_id',
        'title',
        'country_id',
        'name',
        'iban',
        'swift_code',
    ];

    protected array $filterable = [
        'id',
        'user.name',
        'title',
        'name',
        'iban',
        'swift_code',
    ];

    protected array $orderable = [
        'id',
        'user.name',
        'title',
        'name',
        'iban',
        'swift_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }


}
