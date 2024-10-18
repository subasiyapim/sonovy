<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Services\EarningService;
use App\Traits\DataTables\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Notifications\VerifyEmail;
use function Symfony\Component\String\s;

/**
 * @method static create(array $array)
 * @method static UpdateOrCreate(array|string[] $array, array $array1)
 * @method static where(string $string, mixed $input)
 * @method static find(int|string|null $id)
 */
class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasAdvancedFilter;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'surname',
        'email',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'phone_code',
        'phone',
        'is_verified',
        'gender',
        'birth_place_id',
        'birth_date',
        'access_all_artists',
        'access_all_labels',
        'access_all_platforms',
        'company_name',
        'customer_number',
        'payment_email',
        'title',
        'subscribe_newsletter',
        'theme',
        'interface_language',
        'timezone_id',
        'credit_cards',
        'commission_rate',
        'uuid',
        'last_login_at'
    ];

    protected array $filterable = [
        'id',
        'name',
        'surname',
        'email',
        'password',
        'country_id',
        'phone',
    ];

    protected array $orderable = [
        'id',
        'name',
        'surname',
        'email',
        'password',
        'country_id',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'credit_cards' => 'array',
        'bill_info' => 'array',
        'last_login_at' => 'datetime',
    ];

    protected $appends = ['image', 'balance', 'status_text', 'status_class'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('users')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100)
                    ->keepOriginalImageFormat()
                    ->optimize();

                $this->addMediaConversion('small')
                    ->width(400)
                    ->height(400)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
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

    public function getImageAttribute()
    {
        $file = $this->getMedia('users')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->small = $file->getUrl('small');
            $file->thumb = $file->getUrl('thumb');
        }

        return $file;
    }

    protected function statusText(): Attribute
    {
        //eğer status null ise default değeri döndür aksi halde status değerini döndür
        if ($this->status === null) {
            return Attribute::make(
                get: fn() => UserStatusEnum::getTitles()[UserStatusEnum::PENDING_APPROVAL->value]
            );
        } else {
            return Attribute::make(
                get: fn() => UserStatusEnum::getTitles()[$this->status]
            );
        }
    }

    protected function statusClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                $styles = [
                    UserStatusEnum::PENDING_APPROVAL->value => 'default-badge',
                    UserStatusEnum::PASSIVE->value => 'red-badge',
                    UserStatusEnum::ACTIVE->value => 'green-badge',
                ];

                $statusValue = $this->status ?? 'default';
                $class = $styles[$statusValue] ?? 'default-style';

                return $class;
            }
        );
    }

    public function getBalanceAttribute()
    {
        return EarningService::balance();
    }

    public function birthPlace(): BelongsTo
    {
        return $this->belongsTo(City::class, 'birth_place_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }

    public function site(): HasOne
    {
        return $this->hasOne(Site::class, 'user_id');
    }

    public function broadcasts(): HasMany
    {
        return $this->hasMany(Product::class, 'added_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class, 'added_by');
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class, 'added_by');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'added_by');
    }

    public function permittedArtists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_user');
    }

    public function permittedLabels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    public function permittedPlatforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class);
    }

    public function sub_users(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id', 'id')->with('sub_users');
    }

    public function parent_user(): BelongsTo
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

    public function announcements(): HasMany
    {
        return $this->hasMany(AnnouncementUser::class);
    }

    public function competency(): HasOne
    {
        return $this->hasOne(Competency::class);
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d-m-Y H:i');
    }


}
