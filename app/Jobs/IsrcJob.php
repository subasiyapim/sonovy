<?php

namespace App\Jobs;

use App\Models\Song;
use App\Services\ISRCServices;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class IsrcJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [60, 180, 360];

    protected $tenants;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->tenants = Cache::remember('tenants', 3600, function () {
            return \App\Models\System\Tenant::all();
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ISRC Job başarısız oldu', [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if ($this->tenants->isEmpty()) {
                Log::warning('ISRC Job: Hiç tenant bulunamadı');
                return;
            }

            foreach ($this->tenants as $tenant) {
                try {
                    if (!$tenant || !$tenant->exists) {
                        Log::warning('Geçersiz tenant', ['tenant_id' => $tenant->id ?? 'unknown']);
                        continue;
                    }

                    tenancy()->initialize($tenant);
                    Log::info('Tenant başlatıldı', ['tenant' => $tenant->domain]);

                    $totalProcessed = 0;
                    $totalErrors = 0;

                    $songs = Song::whereNull('isrc')
                        ->chunk(100, function ($songs) use ($tenant, &$totalProcessed, &$totalErrors) {
                            foreach ($songs as $song) {
                                try {
                                    if (!$song->type) {
                                        Log::warning('Şarkı tipi bulunamadı', [
                                            'song_id' => $song->id,
                                            'tenant' => $tenant->domain
                                        ]);
                                        continue;
                                    }

                                    $isrc = ISRCServices::make($song->type, $tenant);

                                    if ($isrc === null) {
                                        Log::error('ISRC oluşturulamadı', [
                                            'song_id' => $song->id,
                                            'tenant' => $tenant->domain,
                                            'type' => $song->type
                                        ]);
                                        continue;
                                    }

                                    $song->isrc = $isrc;
                                    $song->save();
                                    $totalProcessed++;

                                } catch (\Exception $e) {
                                    $totalErrors++;
                                    Log::error('ISRC oluşturma hatası', [
                                        'song_id' => $song->id,
                                        'tenant' => $tenant->domain,
                                        'error' => $e->getMessage(),
                                        'type' => $song->type ?? 'unknown'
                                    ]);
                                    continue;
                                }
                            }
                        });

                    Log::info('ISRC işlemi tamamlandı', [
                        'tenant' => $tenant->domain,
                        'processed' => $totalProcessed,
                        'errors' => $totalErrors
                    ]);

                } catch (\Exception $e) {
                    Log::error('Tenant işleme hatası', [
                        'tenant' => $tenant->domain,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                } finally {
                    tenancy()->end();
                    Log::info('Tenant sonlandırıldı', ['tenant' => $tenant->domain]);
                }
            }
        } catch (\Exception $e) {
            Log::error('ISRC Job kritik hata', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            Cache::forget('tenants');
        }
    }

    public function tags()
    {
        return [
            'tenant:'.tenant('id'),
        ];
    }
}
