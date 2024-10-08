<?php

namespace App\Imports;

use App\Models\Artist;
use App\Models\Country;
use App\Models\EarningReport;
use App\Models\EarningReportFile;
use App\Models\Platform;
use App\Models\Song;
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

    public function model(array $row)
    {
        Log::info('Row data: ', $row);

        $song = Song::with(['broadcasts' => function ($query) {
            $query->with(['label.user', 'artists']);
        }])
            ->where('isrc', $row['isrc'])
            ->first();

        if ($song) {
            Log::info('Song found: ', ['song_id' => $song->id]);

            $file_id = EarningReportFile::latest()->first()?->id;

            $platform = Platform::firstOrCreate(
                ['name' => $row['platform']],
                [
                    'visible_name' => $row['platform'],
                    'code' => Str::slug($row['platform'] . '-' . rand(1000, 9999)),
                    'type' => 1,
                    'status' => 1
                ]
            );

            $country_id = Country::where('name', $row['ulke'])->value('id');
            Log::info('Country ID: ', ['country_id' => $country_id]);

            $broadcast = $song->broadcasts->first();
            Log::info('Broadcast: ', ['broadcast' => $broadcast]);

            $artist_id = $broadcast?->artists->first()?->id;
            $label_id = $broadcast?->label?->user?->id;

            Log::info('Artist ID: ', ['artist_id' => $artist_id]);
            Log::info('Label ID: ', ['label_id' => $label_id]);

            return EarningReport::updateOrCreate(
                [
                    'report_date' => $row['rapor_ayi'],
                    'sales_date' => $row['satis_ayi'],
                    'platform' => $row['platform'],
                    'isrc_code' => $row['isrc'],
                    'net_revenue' => str_replace(',', '.', $row['net_gelir']),
                ],
                [
                    'platform_id' => $platform->id,
                    'country_id' => $country_id,
                    'label_id' => $label_id,
                    'artist_id' => $artist_id,
                    'song_id' => $song->id,
                    'earning_report_file_id' => $file_id,
                    'country' => $row['ulke'],
                    'label_name' => $row['label_adi'],
                    'artist_name' => $row['sanatci_adi'],
                    'release_name' => $row['album_adi'],
                    'song_name' => $row['parca_adi'],
                    'upc_code' => $row['upc'],
                    'catalog_number' => $row['album_katalog_numarasi'],
                    'release_type' => $row['album_tipi'],
                    'sales_type' => $row['satis_tipi'],
                    'quantity' => $row['miktar'],
                    'currency' => $row['musteri_odeme_para_birimi'],
                    'unit_price' => isset($row['unite_fiyati']) ? str_replace(',', '.', $row['unite_fiyati']) : null,
                ]
            );
        }

        Log::warning('Song not found for ISRC: ' . $row['isrc']);
        return null;
    }

    public function rules(): array
    {
        return [
            'rapor_ayi' => 'required|date',
            'satis_ayi' => 'required|date',
            'platform' => 'required|string',
            'ulke' => 'required|string',
            'label_adi' => 'required|string',
            'sanatci_adi' => 'required|string',
            'album_adi' => 'required|string',
            'parca_adi' => 'required|string',
            'upc' => 'nullable',
            'isrc' => 'required|string',
            'album_katalog_numarasi' => 'nullable',
            'streaming_abonman_tipi' => 'nullable',
            'album_tipi' => 'required|string',
            'satis_tipi' => 'required|string',
            'miktar' => 'required|integer',
            'musteri_odeme_para_birimi' => 'required|string',
            'unite_fiyati' => 'nullable|numeric',
            'net_gelir' => 'required',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
