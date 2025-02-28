<?php

namespace App\Jobs;

use App\Enums\EarningReportStatusEnum;
use App\Models\EarningReport;
use App\Models\Setting;
use App\Models\Song;
use App\Models\Earning;
use App\Traits\HelperTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EarningJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HelperTrait;

    public Collection $earningReports;
    public EarningReport $earningReport;
    protected $tenants;
    public $song;
    public $balance = 0;
    public $general_system_commission_rate = 0;

    public int $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->tenants = Cache::rememberForever('tenants', function () {
            return \App\Models\System\Tenant::all();
        });
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);

                $this->earningReports = EarningReport::where('status', EarningReportStatusEnum::PENDING->value)
                    ->orWhereNull('status')->get();

                Log::info('earningReports count: ' . $this->earningReports->count());

                $setting = Setting::where('key', 'general_system_commission_rate')->first();
                $this->general_system_commission_rate = $setting ? $setting->value : 0;

                bcscale(15);

                Log::info('EarningJob started.');

                foreach ($this->earningReports as $earningReport) {
                    DB::beginTransaction();
                    try {
                        $this->processEarningReport($earningReport);
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error processing earning report: ' . $e->getMessage(), [
                            'report_id' => $earningReport->id,
                            'tenant' => $tenant->domain,
                            'stack_trace' => $e->getTraceAsString()
                        ]);
                        $earningReport->status = EarningReportStatusEnum::FAILED->value;
                        $earningReport->save();
                    }
                }

                Log::info('EarningJob completed.');
            } catch (\Exception $e) {
                Log::error('Error processing tenant: ' . $e->getMessage(), [
                    'tenant' => $tenant->domain
                ]);
            }
        }

        Cache::forget('tenants');
    }

    protected function processEarningReport(EarningReport $earningReport): void
    {
        $this->earningReport = $earningReport;
        $this->earningReport->status = EarningReportStatusEnum::PROCESSING->value;
        $this->earningReport->save();

        Log::info('Processing earning report', [
            'id' => $this->earningReport->id,
            'status' => $this->earningReport->status
        ]);

        $this->balance = str_replace(',', '.', $this->earningReport->net_revenue);

        $systemCommission = bcmul($this->balance, bcdiv($this->general_system_commission_rate, '100'));
        Log::info('General system commission calculated', ['commission' => $systemCommission]);
        $this->balance = bcsub($this->balance, $systemCommission);

        $this->song = $this->findSong();

        if (!$this->song) {
            Log::warning('Song not found', [
                'isrc' => $this->earningReport->isrc_code,
                'upc' => $this->earningReport->upc_code
            ]);
            $this->earningReport->status = EarningReportStatusEnum::FAILED->value;
            $this->earningReport->save();
            return;
        }

        Log::info('Found song', ['isrc' => $this->song->isrc]);

        $labelShare = $this->calculateLabelShare();
        $participantEarnings = $this->calculateParticipantEarnings($labelShare);

        $totalParticipantEarnings = array_sum(array_column($participantEarnings, 'earning'));

        $num2 = sprintf('%.15f', (float) $totalParticipantEarnings);
        $labelFinalEarning = bcsub($labelShare, $num2, 15);

        $this->distributeEarnings($labelFinalEarning, $participantEarnings);

        $this->earningReport->status = EarningReportStatusEnum::COMPLETED->value;
        $this->earningReport->save();

        Log::info('Earning report processed successfully', [
            'id' => $this->earningReport->id,
            'label_earning' => $labelFinalEarning,
            'total_participant_earnings' => $totalParticipantEarnings
        ]);
    }

    protected function findSong(): ?Song
    {
        return Song::with(['participants', 'products.label.user'])
            ->whereNotNull('isrc')
            ->whereHas('products', function ($query) {
                $query->where('upc_code', $this->earningReport->upc_code);
            })
            ->where('isrc', $this->earningReport->isrc_code)
            ->first();
    }

    protected function distributeEarnings($labelFinalEarning, $participantEarnings): void
    {
        $sales_type = $this->earningReport->sales_type == 'PLATFORM PROMOTION' ? 'Promosyon' : 'Kazanç';

        // Label earnings
        $this->createEarnings(
            $this->earningReport,
            $labelFinalEarning,
            $this->earningReport->label_id,
            $sales_type
        );

        // Participant earnings
        foreach ($participantEarnings as $participantEarning) {
            $this->createEarnings(
                $this->earningReport,
                $participantEarning['earning'],
                $participantEarning['user_id'],
                __('control.earning.participant_earning')
            );
        }
    }

    protected function calculateLabelShare()
    {
        $labelUser = $this->song->products()->first()->label?->user;
        $labelCommissionRate = $labelUser->commission_rate ?? 0;
        $labelShare = bcmul($this->balance, bcdiv($labelCommissionRate, '100'));
        Log::info('Label commission rate: ' . $labelCommissionRate . ' Label share: ' . $labelShare);
        $this->balance = bcsub($this->balance, $labelShare);
        return $labelShare;
    }

    protected function calculateParticipantEarnings($labelShare)
    {
        $participantEarnings = [];

        if ($this->song && $this->song->participants) {
            foreach ($this->song->participants as $participant) {
                $user = $participant->user;
                if ($user && $participant->rate && $participant->rate > 0) {
                    $participantEarning = bcmul($labelShare, bcdiv($participant->rate, '100'));
                    $participantEarnings[] = [
                        'user_id' => $user->id,
                        'earning' => $this->roundEarning($participantEarning)
                    ];

                    Log::info('Participant earning: ' . $participantEarning . ' for user ID: ' . $user->id);

                    // Üst kullanıcıya pay ekle
                    $this->distributeToUpperUsers($participantEarnings, $user, $participantEarning);
                }
            }
        }

        return $participantEarnings;
    }

    protected function distributeToUpperUsers(&$participantEarnings, $user, $earning)
    {
        try {
            $upperUser = $user->parent_user;
            while ($upperUser) {
                $upperRate = $upperUser->commission_rate ?? 0;
                $upperEarning = bcmul($earning, bcdiv($upperRate, '100'));
                $participantEarnings[] = [
                    'user_id' => $upperUser->id,
                    'earning' => $this->roundEarning($upperEarning)
                ];
                Log::info('Upper user earning: ' . $upperEarning . ' for upper user ID: ' . $upperUser->id);
                $user = $upperUser;
                $upperUser = $upperUser->parent_user;
                $earning = $upperEarning;
            }
        } catch (\Exception $e) {
            Log::error('Failed to distribute earnings to upper users: ' . $e->getMessage());
        }
    }

    protected function createEarnings(EarningReport $earningReport, $amount, $userId, $name)
    {
        Log::info($earningReport->release_name);
        Earning::create([
            'earning_report_id' => $earningReport->id,
            'name' => $name,
            'report_date' => $earningReport->report_date,
            'sales_date' => $earningReport->sales_date,
            'country' => $earningReport->country,
            'platform' => $earningReport->platform,
            'user_id' => $userId,
            'uploaded_user_id' => $earningReport->file?->user_id,
            'earning' => $this->roundEarning($amount),
            'country_id' => $earningReport->country_id,
            'platform_id' => $earningReport->platform_id,
            'label_name' => $earningReport->label_name,
            'label_id' => $earningReport->label_id,
            'artist_name' => $earningReport->artist_name,
            'artist_id' => $earningReport->artist_id ?? null,
            'release_name' => $earningReport->release_name,
            'song_name' => $earningReport->song_name,
            'song_id' => $earningReport->song_id ?? null,
            'upc_code' => $earningReport->upc_code,
            'isrc_code' => $earningReport->isrc_code,
            'catalog_number' => $earningReport->catalog_number,
            'release_type' => $earningReport->release_type,
            'sales_type' => $earningReport->sales_type,
            'quantity' => $earningReport->quantity,
            'currency' => $earningReport->currency,
            'unit_price' => $earningReport->unit_price,
        ]);
    }

    protected function roundEarning($earning)
    {
        // Eğer kazanç 15 ondalık haneden büyükse aşağı yönde yuvarla
        if (strlen(substr(strrchr($earning, "."), 1)) > 15) {
            Log::info('Earning amount before rounding: ' . $earning);
            $roundedEarning = bcadd($earning, '0', 15);
            Log::info('Earning amount after rounding: ' . $roundedEarning);
            return $roundedEarning;
        }
        // Eğer kazanç 15 ondalık haneden küçükse, 15 ondalık haneye tamamla
        $completedEarning = bcadd($earning, '0', 15);
        Log::info('Earning amount completed to 15 decimal places: ' . $completedEarning);
        return $completedEarning;
    }
}
