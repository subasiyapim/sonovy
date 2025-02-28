<?php

namespace App\Models;

use App\Models\System\Country;
use App\Traits\DataTables\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EarningReport extends Model
{
    use HasFactory;
    use HasAdvancedFilter;

    protected $table = 'earning_reports';

    protected $fillable = [
        'user_id',
        'name',
        'period',
        'report_type',
        'file_size',
        'status',
        'processed_at',
        'report_date',
        'reporting_month',
        'sales_date',
        'sales_month',
        'platform',
        'platform_id',
        'country',
        'region',
        'country_id',
        'label_name',
        'label_id',
        'artist_name',
        'artist_id',
        'release_name',
        'song_name',
        'song_id',
        'upc_code',
        'isrc_code',
        'catalog_number',
        'streaming_type',
        'streaming_subscription_type',
        'release_type',
        'sales_type',
        'quantity',
        'currency',
        'client_payment_currency',
        'unit_price',
        'mechanical_fee',
        'gross_revenue',
        'client_share_rate',
        'net_revenue',
        'earning_report_file_id'
    ];

    protected array $filterable = [
        'status',
        'user_id',
        'name',
        'report_date',
        'reporting_month',
        'sales_date',
        'sales_month',
        'platform',
        'platform.name',
        'country',
        'region',
        'country.name',
        'label_name',
        'label.name',
        'artist_name',
        'artist.name',
        'release_name',
        'song_name',
        'song.name',
        'upc_code',
        'isrc_code',
        'catalog_number',
        'streaming_type',
        'streaming_subscription_type',
        'release_type',
        'sales_type',
        'quantity',
        'currency',
        'client_payment_currency',
        'unit_price',
        'mechanical_fee',
        'gross_revenue',
        'client_share_rate',
        'net_revenue',
    ];

    protected array $orderable = [
        'status',
        'user_id',
        'name',
        'report_date',
        'reporting_month',
        'sales_date',
        'sales_month',
        'platform',
        'platform.name',
        'country',
        'region',
        'country.name',
        'label_name',
        'label.name',
        'artist_name',
        'artist.name',
        'release_name',
        'song_name',
        'song.name',
        'upc_code',
        'isrc_code',
        'catalog_number',
        'streaming_type',
        'streaming_subscription_type',
        'release_type',
        'sales_type',
        'quantity',
        'currency',
        'client_payment_currency',
        'unit_price',
        'mechanical_fee',
        'gross_revenue',
        'client_share_rate',
        'net_revenue',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sales_date' => 'date',
        'quantity' => 'integer',
    ];

    public function reportFile(): BelongsTo
    {
        return $this->belongsTo(EarningReportFile::class, 'earning_report_file_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class, 'earning_report_id', 'id');
    }

    public function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d-m-Y H:i');
    }

    public function save(array $options = [])
    {
        try {
            Log::info('EarningReport save başlatıldı', [
                'id' => $this->id,
                'file_id' => $this->earning_report_file_id,
                'isrc' => $this->isrc_code,
                'attributes' => $this->getAttributes(),
                'exists' => $this->exists,
                'dirty' => $this->isDirty(),
                'changes' => $this->getChanges()
            ]);

            // Validasyon kuralları
            $rules = [
                'user_id' => 'nullable',
                'country_id' => 'nullable',
                'label_id' => 'nullable',
                'artist_id' => 'nullable',
                'name' => 'required',
                'report_date' => 'required|date',
                'sales_date' => 'required|date',
                'platform' => 'required',
                'platform_id' => 'required',
                'song_id' => 'required',
                'isrc_code' => 'required',
                'earning_report_file_id' => 'required'
            ];

            $messages = [
                'name.required' => 'Rapor adı zorunludur.',
                'report_date.required' => 'Rapor tarihi zorunludur.',
                'report_date.date' => 'Rapor tarihi geçerli bir tarih olmalıdır.',
                'sales_date.required' => 'Satış tarihi zorunludur.',
                'sales_date.date' => 'Satış tarihi geçerli bir tarih olmalıdır.',
                'platform.required' => 'Platform bilgisi zorunludur.',
                'platform_id.required' => 'Platform ID zorunludur.',
                'song_id.required' => 'Şarkı ID zorunludur.',
                'isrc_code.required' => 'ISRC kodu zorunludur.',
                'earning_report_file_id.required' => 'Rapor dosyası ID zorunludur.'
            ];

            $validator = Validator::make($this->getAttributes(), $rules, $messages);

            if ($validator->fails()) {
                Log::error('EarningReport validasyon hatası', [
                    'errors' => $validator->errors()->toArray(),
                    'attributes' => $this->getAttributes()
                ]);
                throw new \Exception('Validasyon hatası: '.json_encode($validator->errors()->toArray()));
            }

            DB::beginTransaction();

            try {
                $result = parent::save($options);

                if (!$result) {
                    throw new \Exception('Kayıt işlemi başarısız oldu');
                }

                DB::commit();

                Log::info('EarningReport save tamamlandı', [
                    'id' => $this->id,
                    'file_id' => $this->earning_report_file_id,
                    'isrc' => $this->isrc_code,
                    'result' => $result,
                    'new_attributes' => $this->getAttributes()
                ]);

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('EarningReport save hatası', [
                'id' => $this->id,
                'file_id' => $this->earning_report_file_id,
                'isrc' => $this->isrc_code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attributes' => $this->getAttributes()
            ]);
            throw $e;
        }
    }
}
