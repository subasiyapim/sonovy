<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Services\EarningService;
use App\Traits\DataTables\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Number;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;

/**
 * @method static create(array $array)
 * @method static UpdateOrCreate(array|string[] $array, array $array1)
 * @method static where(string $string, mixed $input)
 * @method static find(int|string|null $id)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasAdvancedFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'email',
        'password',
        'country_id',
        'district_id',
        'city_id',
        'language_id',
        'phone_code',
        'phone',
        'is_verified',
        'commission_rate',
        'uuid',
        'email_verified_at',
        'company_info',
        'address',
        'payment_threshold',
        'currency',
        'last_login_at',
        'flags',
        'status',
        'phone_verified_at',
    ];

    protected array $filterable = [
        'id',
        'name',
        'email',
        'phone',
    ];

    protected array $orderable = [
        'id',
        'name',
        'email',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
        'permissions'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatusEnum::class,
        'company_info' => 'array',
        'last_login_at' => 'datetime',
        'flags' => 'array',
    ];

    protected $appends = ['balance'];

    protected static function booted(): void
    {
        static::creating(fn($user) => self::updateAfterCreatingUser($user));

        static::updating(function ($user) {
            if (isset($user->roles) || isset($user->permissions)) {
                unset($user->roles);
                unset($user->permissions);
            }
        });
    }

    protected static function updateAfterCreatingUser($user): void
    {
        if (auth()->check()) {
            $user->update(['parent_id' => auth()->id()]);
        }
    }

    public function availablePlanItemsCount()
    {
        $counts = [
            'labels' => 0,
            'artists' => 0,
            'users' => 0,
            'songs' => 0,
            'albums' => 0,
            'commissions' => 0,
            'upc' => false,
            'isrc' => false,
            'promotions' => 0,
            'site' => false,
        ];

        $orders = $this->orders()->where(function ($query) {
            $query->whereNull('expiration_date')
                ->orWhere('expiration_date', '>', Carbon::now());
        })->get();

        if ($orders->isEmpty()) {
            return $counts;
        }

        $itemCodes = [
            'artist' => 'artists',
            'album' => 'albums',
            'song' => 'songs',
            'commission-rate' => 'commissions',
            'label' => 'labels',
            'participant' => 'users',
            'use-your-own-upc' => 'upc',
            'create-isrc' => 'isrc',
            'promotion' => 'promotions',
            'sub-site' => 'site',
        ];

        foreach ($orders as $order) {
            $items = $order->model_type === (new Plan())->getMorphClass()
                ? $order->plan['items'] ?? []
                : [$order->plan['item'] ?? []];

            foreach ($items as $item) {
                $code = $item['code'] ?? null;
                $type = $item['type'] ?? null;
                $pivotValue = $item['pivot']['value'] ?? 0;

                if (isset($itemCodes[$code])) {
                    if ($type === 'boolean') {
                        $counts[$itemCodes[$code]] = true;
                    } else {
                        // Sayısal değer ise pivot'taki value'yu ekle
                        $counts[$itemCodes[$code]] += $pivotValue;
                    }
                }
            }
        }

        $counts['labels'] -= $this->labels()->count();
        $counts['artists'] -= $this->artists()->count();
        $counts['users'] -= $this->sub_users()->count();
        $counts['songs'] -= $this->songs()->count();

        $products = $this->broadcasts()->with('promotions');
        $counts['albums'] -= $products->count();
        $counts['promotions'] -= $products->get()->pluck('promotions')->flatten()->count();

        return $counts;
    }

    public function getBalanceAttribute()
    {
        return Number::currency(EarningService::balance(), 'USD', app()->getLocale());
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\System\Country::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(\App\Models\System\District::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\System\City::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class, 'created_by');
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class, 'created_by');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'created_by');
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id', 'id')->with('children');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    public function earningReports(): HasMany
    {
        return $this->hasMany(EarningReport::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(AnnouncementUser::class);
    }

    public function competency(): HasOne
    {
        return $this->hasOne(Competency::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'user_id');
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d-m-Y H:i');
    }
}
