<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'orders';

    protected $fillable = [
        'model_id',
        'model_type',
        'amount',
        'expiration_date',
        'status',
        'payment_info',
        'payment_service',
        'user_id',
        'plan',
        'invoice_date',
    ];
    protected array $filterable = [
        'id',
        'model_id',
        'model_type',
        'amount',
        'expiration_date',
        'status',
        'payment_service',
        'user.name',
    ];

    protected array $orderable = [
        'id',
        'model_id',
        'model_type',
        'amount',
        'expiration_date',
        'payment_service',
        'user.name',
    ];

    protected array $dates = [
        'expiration_date',
    ];

    protected $casts = [
        'payment_info' => 'array',
        'plan' => 'array'
    ];

    protected $appends = ['status_text'];

    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn() => OrderStatusEnum::getTitles()[$this->status],
        );
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getUserBillings($order)
    {
        $bill_info = is_string($order->user['bill_info'])
            ? json_decode($order->user['bill_info'], true)
            : $order->user['bill_info'];

        return [
            'name' => $order->user['name'] ?? '',
            'email' => $order->user['email'] ?? '',
            'phone' => $bill_info['phone'] ?? '',
            'address' => $bill_info['address'] ?? '',
            'city' => City::find($bill_info['city_id'])->name ?? '',
            'state' => City::find($bill_info['state_id'])->name ?? '',
            'country' => Country::find($bill_info['country_id'])->name ?? '',
        ];


    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d-m-Y H:i');
    }
}
