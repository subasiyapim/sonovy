<?php

namespace App\Imports;

use App\Enums\EarningReportStatusEnum;
use App\Models\Artist;
use App\Models\System\Country;
use App\Models\EarningReport;
use App\Models\Platform;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Contracts\Queue\ShouldQueue;

class EarningImport implements ToModel, WithValidation, SkipsEmptyRows, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Importable;

    public $timeout = 0;

    protected $columnMappings = [
        // İngilizce format
        'reporting_month' => 'rapor_ayi',
        'sales_month' => 'satis_ayi',
        'platform' => 'platform',
        'country_region' => 'ulke',
        'label_name' => 'label_adi',
        'artist_name' => 'sanatci_adi',
        'release_title' => 'album_adi',
        'track_title' => 'parca_adi',
        'upc' => 'upc',
        'isrc' => 'isrc',
        'release_catalog_nb' => 'album_katalog_numarasi',
        'release_type' => 'album_tipi',
        'sales_type' => 'satis_tipi',
        'quantity' => 'miktar',
        'client_payment_currency' => 'musteri_odeme_para_birimi',
        'unit_price' => 'unite_fiyati',
        'mechanical_fee' => 'mechanical_fee',
        'gross_revenue' => 'brut_gelir',
        'client_share_rate' => 'musteri_pay_orani',
        'net_revenue' => 'net_gelir',
        'streaming_subscription_type' => 'streaming_abonman_tipi',
    ];

    public function model(array $row)
    {
        Log::info('Row data: ', $row);

        // Kolon isimlerini normalize et
        $data = $this->normalizeColumns($row);

        $song = Song::with([
            'products' => function ($query) {
                $query->with(['label.user', 'artists']);
            }
        ])
            ->where('isrc', $data['isrc'])
            ->first();

        if ($song) {
            Log::info('Song found: ', ['song_id' => $song->id]);

            $platform = Platform::firstOrCreate(
                ['name' => $data['platform']],
                [
                    'visible_name' => $data['platform'],
                    'code' => Str::slug($data['platform'].'-'.rand(1000, 9999)),
                    'type' => 1,
                    'status' => 1
                ]
            );

            // Ülke/Bölge ayrıştırma
            $countryParts = explode('/', $data['ulke']);
            $country = trim($countryParts[0]);
            $region = isset($countryParts[1]) ? trim($countryParts[1]) : null;

            $country_id = Country::where('name', $country)->value('id');
            Log::info('Country ID: ', ['country_id' => $country_id]);

            $product = $song->products->first();
            Log::info('Product: ', ['product' => $product]);

            $artist_id = $product?->artists->first()?->id;
            $label_id = $product?->label?->user?->id;

            Log::info('Artist ID: ', ['artist_id' => $artist_id]);
            Log::info('Label ID: ', ['label_id' => $label_id]);

            // Dosya boyutunu hesapla
            $fileSize = round(filesize(request()->file('file')->getRealPath()) / 1024 / 1024, 2);

            // Dönem bilgisini oluştur
            $reportDate = \Carbon\Carbon::parse($data['rapor_ayi']);
            $period = $reportDate->format('Y - ') . ucfirst($reportDate->translatedFormat('F'));

            // Sayısal değerleri temizle
            $netRevenue = str_replace([',', ' '], ['.', ''], $data['net_gelir']);
            $unitPrice = isset($data['unite_fiyati']) ? str_replace([',', ' '], ['.', ''], $data['unite_fiyati']) : null;
            $mechanicalFee = isset($data['mechanical_fee']) ? str_replace([',', ' '], ['.', ''], $data['mechanical_fee']) : null;
            $grossRevenue = isset($data['brut_gelir']) ? str_replace([',', ' '], ['.', ''], $data['brut_gelir']) : null;
            $clientShareRate = isset($data['musteri_pay_orani']) ? str_replace([',', ' '], ['.', ''], $data['musteri_pay_orani']) : null;

            return EarningReport::updateOrCreate(
                [
                    'report_date' => $data['rapor_ayi'],
                    'sales_date' => $data['satis_ayi'],
                    'platform' => $data['platform'],
                    'isrc_code' => $data['isrc'],
                    'net_revenue' => $netRevenue,
                ],
                [
                    'user_id' => Auth::id(),
                    'status' => EarningReportStatusEnum::PENDING->value,
                    'platform_id' => $platform->id,
                    'country_id' => $country_id,
                    'country' => $country,
                    'region' => $region,
                    'label_id' => $label_id,
                    'artist_id' => $artist_id,
                    'song_id' => $song->id,
                    'label_name' => $data['label_adi'],
                    'artist_name' => $data['sanatci_adi'],
                    'release_name' => $data['album_adi'],
                    'song_name' => $data['parca_adi'],
                    'upc_code' => $data['upc'],
                    'catalog_number' => $data['album_katalog_numarasi'],
                    'streaming_subscription_type' => $data['streaming_abonman_tipi'] ?? null,
                    'release_type' => $data['album_tipi'],
                    'sales_type' => $data['satis_tipi'],
                    'quantity' => $data['miktar'],
                    'currency' => $data['musteri_odeme_para_birimi'],
                    'unit_price' => $unitPrice,
                    'mechanical_fee' => $mechanicalFee,
                    'gross_revenue' => $grossRevenue,
                    'client_share_rate' => $clientShareRate,
                    'period' => $period,
                    'report_type' => 'Rapor Aylık Bülteni',
                    'file_size' => $fileSize,
                    'processed_at' => now(),
                ]
            );
        }

        Log::warning('Song not found for ISRC: '.$data['isrc']);
        return null;
    }

    protected function normalizeColumns(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalizedKey = Str::slug($key, '_');
            $mappedKey = $this->columnMappings[$normalizedKey] ?? $normalizedKey;
            $normalized[$mappedKey] = $value;
        }
        return $normalized;
    }

    public function rules(): array
    {
        return [
            '*.reporting_month' => 'required|string',
            '*.sales_month' => 'required|string',
            '*.platform' => 'required|string',
            '*.country_region' => 'required|string',
            '*.label_name' => 'required|string',
            '*.artist_name' => 'required|string',
            '*.release_title' => 'required|string',
            '*.track_title' => 'required|string',
            '*.upc' => 'nullable|string',
            '*.isrc' => 'required|string',
            '*.release_catalog_nb' => 'nullable|string',
            '*.streaming_subscription_type' => 'nullable|string',
            '*.release_type' => 'required|string',
            '*.sales_type' => 'required|string',
            '*.quantity' => 'required|integer',
            '*.client_payment_currency' => 'required|string',
            '*.unit_price' => 'nullable|string',
            '*.mechanical_fee' => 'nullable|string',
            '*.gross_revenue' => 'nullable|string',
            '*.client_share_rate' => 'nullable|string',
            '*.net_revenue' => 'required|string',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
