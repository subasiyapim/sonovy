<?php

namespace App\Imports;

use App\Models\Artist;
use App\Models\System\Country;
use App\Models\EarningReport;
use App\Models\EarningReportFile;
use App\Models\Platform;
use App\Models\Song;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use App\Enums\EarningReportFileStatusEnum;
use App\Models\Label;
use Illuminate\Http\UploadedFile;
use Throwable;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use App\Models\Earning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;

class EarningImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable;

    public $timeout = 600;
    public $tries = 3;
    private $headersValidated = false;
    private $currentChunkRows = 0;
    private $lastChunkUpdate = 0;
    private const CHUNK_UPDATE_SIZE = 100;

    // Beklenen başlıklar ve Excel'deki karşılıkları
    private const HEADER_MAPPINGS = [
        'isrc' => ['isrc'],
        'platform' => ['platform'],
        'country' => ['country', 'country_region'],
        'label_name' => ['label_name'],
        'artist_name' => ['artist_name'],
        'release_name' => ['release_name', 'release_title'],
        'song_name' => ['song_name', 'track_title']
    ];

    // Zorunlu alanlar
    private const REQUIRED_FIELDS = [
        'isrc' => 'ISRC',
        'platform' => 'Platform',
        'country' => 'Ülke',
        'label_name' => 'Label Adı',
        'artist_name' => 'Sanatçı Adı',
        'release_name' => 'Release Adı',
        'song_name' => 'Şarkı Adı'
    ];

    protected $reportLanguage;
    protected $fileId;
    protected $processedRows = 0;
    protected $errorRows = 0;
    protected $errors = [];
    protected $reportDate;
    protected $platformId;
    protected $countryId;
    protected $labelId;
    protected $artistId;
    protected $songId;
    protected $fileSize;
    protected $file;
    protected $filePath;

    public function __construct(bool $reportLanguage, $fileId, $reportDate, ?string $filePath = null)
    {
        try {
            // Bellek limitini artır
            ini_set('memory_limit', '1024M');

            Log::info('EarningImport constructor başladı', [
                'report_language' => $reportLanguage,
                'file_id' => $fileId,
                'time' => now()->toDateTimeString(),
                'file_exists' => !empty($filePath),
                'file_path' => $filePath ?? 'no_file'
            ]);

            if (empty($filePath)) {
                throw new \Exception('Excel dosyası yüklenemedi. Dosya bulunamadı.');
            }

            if (!file_exists($filePath)) {
                throw new \Exception('Excel dosyası bulunamadı: '.$filePath);
            }

            $this->reportLanguage = $reportLanguage;
            $this->fileId = $fileId;
            $this->reportDate = Carbon::parse($reportDate);
            $this->filePath = $filePath;

            $this->initializeImport();

        } catch (Throwable $e) {
            Log::error('EarningImport constructor hatası', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function initializeImport(): void
    {
        try {
            Log::info('Import başlatılıyor', [
                'file_id' => $this->fileId,
                'report_date' => $this->reportDate,
                'file_path' => $this->filePath
            ]);

            // Excel dosyasının toplam satır sayısını al
            $totalRows = 0;
            try {
                // Dosya uzantısına göre uygun reader'ı seç
                $extension = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));

                switch ($extension) {
                    case 'xlsx':
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        break;
                    case 'xls':
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                        break;
                    case 'csv':
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                        $reader->setInputEncoding('UTF-8');
                        $reader->setDelimiter(',');
                        $reader->setEnclosure('"');
                        $reader->setSheetIndex(0);
                        break;
                    default:
                        throw new \Exception('Desteklenmeyen dosya formatı: '.$extension);
                }

                $reader->setReadDataOnly(true);
                $reader->setReadEmptyCells(false);

                Log::info('Excel dosyası okunuyor', [
                    'file_path' => $this->filePath,
                    'file_size' => filesize($this->filePath),
                    'file_extension' => $extension
                ]);

                // Dosyayı oku
                try {
                    $spreadsheet = $reader->load($this->filePath);
                } catch (\Exception $e) {
                    // Eğer ilk okuma başarısız olursa, farklı bir encoding ile dene
                    if ($extension === 'csv') {
                        $reader->setInputEncoding('ISO-8859-9');
                        $spreadsheet = $reader->load($this->filePath);
                    } else {
                        throw $e;
                    }
                }

                $worksheet = $spreadsheet->getActiveSheet();
                $totalRows = $worksheet->getHighestDataRow() - 1; // Başlık satırını çıkar

                Log::info('Excel dosyası başarıyla okundu', [
                    'total_rows' => $totalRows,
                    'highest_row' => $worksheet->getHighestDataRow(),
                    'highest_column' => $worksheet->getHighestDataColumn(),
                    'file_extension' => $extension
                ]);

                unset($worksheet);
                unset($spreadsheet);
                unset($reader);
            } catch (\Exception $e) {
                Log::error('Excel dosyası okuma hatası', [
                    'error' => $e->getMessage(),
                    'file_path' => $this->filePath,
                    'file_size' => filesize($this->filePath),
                    'file_extension' => $extension ?? 'unknown'
                ]);
                throw new \Exception('Excel dosyası okunamadı: '.$e->getMessage());
            }

            // Mevcut dosya bilgilerini al
            $currentFile = EarningReportFile::find($this->fileId);

            if (!$currentFile) {
                Log::error('Dosya bulunamadı', ['file_id' => $this->fileId]);
                return;
            }

            // Mevcut değerleri koru
            $this->processedRows = $currentFile->processed_rows ?? 0;
            $this->errorRows = $currentFile->error_rows ?? 0;
            $this->errors = is_array($currentFile->errors) ? $currentFile->errors : [];

            // Dosyayı güncelle
            $currentFile->update([
                'total_rows' => $totalRows,
                'status' => EarningReportFileStatusEnum::PROCESSING->value,
                'is_processed' => false,
                'processed_at' => now(),
                'processed_rows' => $this->processedRows,
                'error_rows' => $this->errorRows
            ]);

            $this->currentChunkRows = 0;
            $this->lastChunkUpdate = $this->processedRows + $this->errorRows;

            Log::info('Import başlatıldı', [
                'file_id' => $this->fileId,
                'total_rows' => $totalRows,
                'current_processed' => $this->processedRows,
                'current_errors' => $this->errorRows
            ]);

            // Belleği temizle
            gc_collect_cycles();

        } catch (\Exception $e) {
            Log::error('Import başlatma hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_id' => $this->fileId
            ]);
            throw $e;
        }
    }

    public function onRow(Row $row)
    {
        try {
            $rowArray = $row->toArray();

            if (!$this->headersValidated) {
                $this->validateHeaders($rowArray);
                $this->headersValidated = true;
                return;
            }

            // Excel verilerini normalize et
            $normalizedData = $this->normalizeRowData($rowArray);

            Log::info('Excel satırı işleniyor', [
                'row_number' => $row->getIndex() + 2,
                'normalized_data' => $normalizedData,
                'current_processed' => $this->processedRows,
                'current_errors' => $this->errorRows
            ]);

            // Zorunlu alan kontrolü
            $missingFields = [];
            foreach (self::REQUIRED_FIELDS as $field => $fieldName) {
                if (empty($normalizedData[$field])) {
                    $missingFields[] = $fieldName;
                }
            }

            if (!empty($missingFields)) {
                Log::warning('Eksik zorunlu alanlar bulundu', [
                    'missing_fields' => $missingFields,
                    'row_data' => $normalizedData
                ]);
                $this->addError(
                    sprintf('Eksik zorunlu alanlar: %s', implode(', ', $missingFields)),
                    $normalizedData
                );
                return;
            }

            $isrc = $normalizedData['isrc'];
            $cleanIsrc = trim($isrc);

            // ISRC araması öncesi log
            Log::info('ISRC aranıyor', [
                'raw_isrc' => $isrc,
                'clean_isrc' => $cleanIsrc,
                'row_number' => $row->getIndex() + 2
            ]);

            $song = Song::query()
                ->where('isrc', $cleanIsrc)
                ->first();

            // ISRC arama sonucu log
            if ($song) {
                Log::info('ISRC eşleşmesi bulundu', [
                    'isrc' => $cleanIsrc,
                    'song_id' => $song->id,
                    'song_name' => $song->name,
                    'row_number' => $row->getIndex() + 2
                ]);
            } else {
                Log::warning('ISRC bulunamadı', [
                    'isrc' => $isrc,
                    'clean_isrc' => $cleanIsrc,
                    'row_number' => $row->getIndex() + 2
                ]);
                $this->addError(
                    sprintf('ISRC bulunamadı: %s', $isrc),
                    $normalizedData
                );
                return;
            }

            try {
                $platform = Platform::query()->where('name', $normalizedData['platform'])->first();
                if (!$platform) {
                    Log::warning('Platform bulunamadı', [
                        'platform_name' => $normalizedData['platform'],
                        'row_number' => $row->getIndex() + 2
                    ]);
                    $this->addError(
                        sprintf('Platform bulunamadı: %s', $normalizedData['platform']),
                        $normalizedData
                    );
                    return;
                }

                $country = Country::query()->where('name', $normalizedData['country'])->first();
                if (!$country) {
                    Log::warning('Ülke bulunamadı', [
                        'country_name' => $normalizedData['country'],
                        'row_number' => $row->getIndex() + 2
                    ]);
                    $this->addError(
                        sprintf('Ülke bulunamadı: %s', $normalizedData['country']),
                        $normalizedData
                    );
                }

                $label = Label::query()->where('name', $normalizedData['label_name'])->first();
                if (!$label) {
                    Log::warning('Label bulunamadı', [
                        'label_name' => $normalizedData['label_name'],
                        'row_number' => $row->getIndex() + 2
                    ]);
                    $this->addError(
                        sprintf('Label bulunamadı: %s', $normalizedData['label_name']),
                        $normalizedData
                    );
                }

                $artist = Artist::query()->where('name', $normalizedData['artist_name'])->first();
                if (!$artist) {
                    Log::warning('Sanatçı bulunamadı', [
                        'artist_name' => $normalizedData['artist_name'],
                        'row_number' => $row->getIndex() + 2
                    ]);
                    $this->addError(
                        sprintf('Sanatçı bulunamadı: %s', $normalizedData['artist_name']),
                        $normalizedData
                    );
                }

                // İlişkili kayıtları logla
                Log::info('İlişkili kayıtlar bulundu', [
                    'row_number' => $row->getIndex() + 2,
                    'platform_id' => $platform->id,
                    'platform_name' => $normalizedData['platform'],
                    'country_id' => $country?->id,
                    'country_name' => $normalizedData['country'],
                    'label_id' => $label?->id,
                    'label_name' => $normalizedData['label_name'],
                    'artist_id' => $artist?->id,
                    'artist_name' => $normalizedData['artist_name']
                ]);

                // EarningReport verilerini hazırla
                $earningReportData = [
                    'user_id' => Auth::id(),
                    'name' => $this->reportDate->format('Y-m-d'),
                    'period' => $this->reportDate->format('Y-m-d'),
                    'report_type' => 'earning',
                    'file_size' => $this->file?->getSize() ?? 0,
                    'status' => EarningReportFileStatusEnum::PENDING->value,
                    'processed_at' => now(),
                    'report_date' => $this->reportDate,
                    'reporting_month' => $this->reportDate->format('Y-m'),
                    'sales_date' => $this->reportDate,
                    'sales_month' => $this->reportDate->format('Y-m'),
                    'platform' => $normalizedData['platform'],
                    'platform_id' => $platform->id,
                    'country' => $normalizedData['country'],
                    'region' => $normalizedData['region'] ?? null,
                    'country_id' => $country?->id,
                    'label_name' => $normalizedData['label_name'],
                    'label_id' => $label?->id,
                    'artist_name' => $normalizedData['artist_name'],
                    'artist_id' => $artist?->id,
                    'release_name' => $normalizedData['release_name'],
                    'song_name' => $normalizedData['song_name'],
                    'song_id' => $song->id,
                    'upc_code' => $normalizedData['upc_code'] ?? null,
                    'isrc_code' => $isrc,
                    'catalog_number' => $normalizedData['catalog_number'] ?? null,
                    'streaming_type' => $normalizedData['streaming_type'] ?? null,
                    'streaming_subscription_type' => $normalizedData['streaming_subscription_type'] ?? null,
                    'release_type' => $normalizedData['release_type'] ?? null,
                    'sales_type' => $normalizedData['sales_type'] ?? null,
                    'quantity' => $normalizedData['quantity'] ?? null,
                    'currency' => $normalizedData['currency'] ?? null,
                    'client_payment_currency' => $normalizedData['client_payment_currency'] ?? null,
                    'unit_price' => $normalizedData['unit_price'] ?? null,
                    'mechanical_fee' => $normalizedData['mechanical_fee'] ?? null,
                    'gross_revenue' => $normalizedData['gross_revenue'] ?? null,
                    'client_share_rate' => $normalizedData['client_share_rate'] ?? null,
                    'net_revenue' => $normalizedData['net_revenue'] ?? null,
                    'earning_report_file_id' => $this->fileId
                ];

                // Kayıt işlemi
                try {
                    DB::beginTransaction();

                    // firstOrCreate ile mükerrer kayıtları önle
                    $earningReport = EarningReport::firstOrCreate(
                        [
                            'isrc_code' => $isrc,
                            'upc_code' => $normalizedData['upc_code'] ?? null,
                            'report_date' => $this->reportDate,
                            'platform' => $normalizedData['platform'],
                            'net_revenue' => $normalizedData['net_revenue'],
                        ],
                        $earningReportData
                    );

                    if (!$earningReport->wasRecentlyCreated) {
                        Log::info('Mükerrer kayıt bulundu, güncelleme yapılıyor', [
                            'id' => $earningReport->id,
                            'file_id' => $this->fileId,
                            'isrc' => $isrc
                        ]);
                    }

                    Log::info('EarningReport kaydı başarıyla oluşturuldu', [
                        'id' => $earningReport->id,
                        'file_id' => $this->fileId,
                        'isrc' => $isrc,
                        'is_new' => $earningReport->wasRecentlyCreated
                    ]);

                    DB::commit();

                    // Başarılı kayıt sonrası işlenen satır sayısını artır
                    $this->processedRows++;

                    // Mevcut dosya bilgilerini güncelle
                    $currentFile = EarningReportFile::find($this->fileId);
                    if ($currentFile) {
                        $currentFile->increment('processed_rows');
                    }

                    Log::info('Satır işleme tamamlandı', [
                        'file_id' => $this->fileId,
                        'processed_rows' => $this->processedRows,
                        'error_rows' => $this->errorRows,
                        'current_chunk' => $this->currentChunkRows
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Kayıt işlemi sırasında hata', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'data' => $earningReportData
                    ]);
                    throw $e;
                }

            } catch (\Exception $e) {
                Log::error('Earning raporu işleme hatası', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $normalizedData,
                    'row_number' => $row->getIndex() + 2
                ]);
                $this->addError('İşlem hatası: '.$e->getMessage(), $normalizedData);
            }

            // Her satır işlendikten sonra normal progress güncelle
            $this->updateProgressIfNeeded();

        } catch (Throwable $e) {
            Log::error('Satır işleme hatası', [
                'row_number' => $row->getIndex() + 2,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addError($e->getMessage(), $rowArray ?? []);
            $this->updateProgressIfNeeded();
        }
    }

    protected function validateHeaders(array $row): void
    {
        $availableHeaders = array_keys($row);
        $missingFields = [];

        foreach (self::HEADER_MAPPINGS as $required => $alternatives) {
            $found = false;
            foreach ($alternatives as $alt) {
                if (in_array($alt, $availableHeaders)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $missingFields[] = $required;
            }
        }

        if (!empty($missingFields)) {
            $errorMessage = 'Eksik başlıklar: '.implode(', ', $missingFields);
            Log::error('Excel başlık hatası', [
                'missing_headers' => $missingFields,
                'found_headers' => $availableHeaders,
                'expected_headers' => self::HEADER_MAPPINGS
            ]);
            throw new \Exception($errorMessage);
        }
    }

    protected function normalizeRowData(array $row): array
    {
        $normalized = [];

        foreach (self::HEADER_MAPPINGS as $key => $alternatives) {
            foreach ($alternatives as $alt) {
                if (isset($row[$alt])) {
                    $normalized[$key] = $row[$alt];
                    break;
                }
            }
        }

        // Diğer alanları ekle
        $otherFields = [
            'quantity', 'catalog_number', 'streaming_type',
            'streaming_subscription_type', 'release_type', 'sales_type',
            'currency', 'client_payment_currency',
            'client_share_rate'
        ];

        foreach ($otherFields as $field) {
            if (isset($row[$field])) {
                $normalized[$field] = $row[$field];
            } else {
                $normalized[$field] = null;
            }
        }

        // Finansal değerleri doğrudan al
        $normalized['net_revenue'] = $row['net_revenue'] ?? null;
        $normalized['gross_revenue'] = $row['gross_revenue'] ?? null;
        $normalized['unit_price'] = $row['unit_price'] ?? null;
        $normalized['mechanical_fee'] = $row['mechanical_fee'] ?? null;
        // country_region'ı region olarak kaydet
        $normalized['region'] = $row['country_region'] ?? null;

        // upc değerini doğru şekilde kaydet
        $normalized['upc_code'] = $row['upc'] ?? null;

        return $normalized;
    }

    protected function addError($message, $row)
    {
        try {
            $this->errorRows++;
            $this->errors[] = [
                'row_number' => $this->processedRows + $this->errorRows,
                'message' => $message,
                'row_data' => $row,
                'timestamp' => now()->toDateTimeString()
            ];

            // Mevcut dosya bilgilerini güncelle
            $currentFile = EarningReportFile::find($this->fileId);
            if ($currentFile) {
                $currentFile->increment('error_rows');

                // Hataları güncelle
                $existingErrors = is_array($currentFile->errors) ? $currentFile->errors : [];
                $allErrors = array_merge($existingErrors, [$this->errors[count($this->errors) - 1]]);

                $currentFile->update([
                    'errors' => $allErrors
                ]);
            }

            // Hata eklendiğinde zorla güncelle
            $this->updateProgressIfNeeded(true);
        } catch (\Exception $e) {
            Log::error('Error adding error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function updateProgressIfNeeded(bool $forceUpdate = false): void
    {
        try {
            $this->currentChunkRows++;

            if ($forceUpdate || $this->currentChunkRows >= self::CHUNK_UPDATE_SIZE) {
                // Mevcut dosya bilgilerini al
                $currentFile = EarningReportFile::find($this->fileId);

                if (!$currentFile) {
                    Log::error('Dosya bulunamadı', ['file_id' => $this->fileId]);
                    return;
                }

                Log::info('Progress güncelleniyor', [
                    'file_id' => $this->fileId,
                    'processed_rows' => $this->processedRows,
                    'error_rows' => $this->errorRows,
                    'total_rows' => $currentFile->total_rows,
                    'forced_update' => $forceUpdate,
                    'current_chunk' => $this->currentChunkRows
                ]);

                // Hataları birleştir
                $existingErrors = is_array($currentFile->errors) ? $currentFile->errors : [];
                $allErrors = array_merge($existingErrors, array_slice($this->errors, -100));

                // Dosyayı güncelle
                $currentFile->update([
                    'errors' => $allErrors,
                    'status' => EarningReportFileStatusEnum::PROCESSING->value,
                    'processed_rows' => $this->processedRows,
                    'error_rows' => $this->errorRows
                ]);

                $this->currentChunkRows = 0;
                $this->lastChunkUpdate = $this->processedRows + $this->errorRows;
                $this->errors = [];

                // Belleği temizle
                gc_collect_cycles();
            }
        } catch (\Exception $e) {
            Log::error('Progress güncelleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_id' => $this->fileId
            ]);
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                Log::info('Import başlıyor', [
                    'file_id' => $this->fileId,
                    'time' => now()->toDateTimeString()
                ]);
            },
            AfterImport::class => function (AfterImport $event) {
                $this->finalizeImport();
            },
            ImportFailed::class => function (ImportFailed $event) {
                $this->handleImportFailure($event);
            }
        ];
    }

    protected function finalizeImport(): void
    {
        try {
            // Son chunk'ı zorla güncelle
            $this->updateProgressIfNeeded(true);

            // Mevcut dosya bilgilerini al
            $currentFile = EarningReportFile::find($this->fileId);

            if (!$currentFile) {
                Log::error('Dosya bulunamadı', ['file_id' => $this->fileId]);
                return;
            }

            Log::info('Import tamamlandı', [
                'file_id' => $this->fileId,
                'total_rows' => $currentFile->total_rows,
                'processed_rows' => $this->processedRows,
                'error_rows' => $this->errorRows,
                'last_chunk_update' => $this->lastChunkUpdate
            ]);

            $status = $this->errorRows > 0
                ? EarningReportFileStatusEnum::COMPLETED_WITH_ERRORS->value
                : EarningReportFileStatusEnum::COMPLETED->value;

            // Hataları birleştir
            $existingErrors = is_array($currentFile->errors) ? $currentFile->errors : [];
            $allErrors = array_merge($existingErrors, $this->errors);

            // Son durumu güncelle
            $currentFile->update([
                'is_processed' => true,
                'processed_at' => now(),
                'processed_rows' => $this->processedRows,
                'error_rows' => $this->errorRows,
                'errors' => $allErrors,
                'status' => $status
            ]);

            Log::info('Import final durumu', [
                'file_id' => $this->fileId,
                'final_processed' => $this->processedRows,
                'final_errors' => $this->errorRows,
                'total_error_count' => count($allErrors),
                'status' => $status
            ]);

            // Import başarılı ise EarningJob'ı tetikle
            if ($status === EarningReportFileStatusEnum::COMPLETED->value) {
                Log::info('EarningJob tetikleniyor', [
                    'file_id' => $this->fileId
                ]);

                dispatch(new \App\Jobs\EarningJob());
            }

        } catch (\Exception $e) {
            Log::error('Import sonlandırma hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_id' => $this->fileId
            ]);
            throw $e;
        }
    }

    protected function handleImportFailure(ImportFailed $event): void
    {
        Log::error('Import başarısız oldu', [
            'file_id' => $this->fileId,
            'error' => $event->getException()->getMessage()
        ]);

        $this->errorRows++;
        $this->updateFileStatus(
            EarningReportFileStatusEnum::FAILED->value,
            ['Import hatası: '.$event->getException()->getMessage()]
        );
    }

    protected function updateFileStatus(int $status, array $additionalErrors = []): void
    {
        try {
            // Mevcut kayıtları al
            $currentFile = EarningReportFile::find($this->fileId);

            if (!$currentFile) {
                Log::error('Dosya bulunamadı', ['file_id' => $this->fileId]);
                return;
            }

            // Mevcut hataları koru
            $existingErrors = is_array($currentFile->errors) ? $currentFile->errors : [];
            $allErrors = array_merge($existingErrors, $this->errors, $additionalErrors);

            // Mevcut sayaçları koru
            $processedRows = max($currentFile->processed_rows, $this->processedRows);
            $errorRows = max($currentFile->error_rows, $this->errorRows);

            Log::info('Dosya durumu güncelleniyor', [
                'file_id' => $this->fileId,
                'status' => $status,
                'processed_rows' => $processedRows,
                'error_rows' => $errorRows,
                'total_errors' => count($allErrors)
            ]);

            $currentFile->update([
                'is_processed' => true,
                'processed_at' => now(),
                'processed_rows' => $processedRows,
                'error_rows' => $errorRows,
                'errors' => $allErrors,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Dosya durumu güncelleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_id' => $this->fileId
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'isrc' => 'required|string',
            'platform' => 'required|string',
            'country' => 'required|string',
            'label_name' => 'required|string',
            'artist_name' => 'required|string',
            'release_name' => 'required|string',
            'song_name' => 'required|string',
            'quantity' => 'nullable|numeric',
            'unit_price' => 'nullable|numeric',
            'gross_revenue' => 'nullable|numeric',
            'net_revenue' => 'nullable|numeric',
            'client_share_rate' => 'nullable|numeric'
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'isrc.required' => 'ISRC alanı boş bırakılamaz',
            'platform.required' => 'Platform alanı boş bırakılamaz',
            'country.required' => 'Ülke alanı boş bırakılamaz',
            'label_name.required' => 'Label adı boş bırakılamaz',
            'artist_name.required' => 'Sanatçı adı boş bırakılamaz',
            'release_name.required' => 'Release adı boş bırakılamaz',
            'song_name.required' => 'Şarkı adı boş bırakılamaz',
            'quantity.numeric' => 'Miktar sayısal bir değer olmalıdır',
            'unit_price.numeric' => 'Birim fiyat sayısal bir değer olmalıdır',
            'gross_revenue.numeric' => 'Brüt gelir sayısal bir değer olmalıdır',
            'net_revenue.numeric' => 'Net gelir sayısal bir değer olmalıdır',
            'client_share_rate.numeric' => 'Müşteri payı oranı sayısal bir değer olmalıdır'
        ];
    }

    public function chunkSize(): int
    {
        return 50; // Chunk boyutunu küçült
    }
}
