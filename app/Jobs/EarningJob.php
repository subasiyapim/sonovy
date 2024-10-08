<?php

namespace App\Jobs;

use App\Enums\EarningReportStatusEnum;
use App\Models\EarningReport;
use App\Models\Setting;
use App\Models\Song;
use App\Models\Earning;
use App\Traits\HelperTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EarningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HelperTrait;

    public Collection $earningReports;
    public EarningReport $earningReport;

    public $song;
    public $balance = 0;
    public $general_system_commission_rate = 0;

    public int $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->earningReports = EarningReport::where('status', EarningReportStatusEnum::PENDING->value)
            ->orWhereNull('status')->get();

        $setting = Setting::where('key', 'general_system_commission_rate')->first();
        $this->general_system_commission_rate = $setting ? $setting->value : 0;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        bcscale(15); // Ondalık sayıların doğruluğunu 15 basamak olarak ayarla

        Log::info('EarningJob started.');

        foreach ($this->earningReports as $earningReport) {
            $this->earningReport = $earningReport;

            DB::beginTransaction();
            try {
                $this->earningReport->status = EarningReportStatusEnum::PROCESSING->value;
                $this->earningReport->save();

                Log::info('Processing earning report ID: ' . $this->earningReport->id . ' Status: ' . $this->earningReport->status);

                $this->balance = str_replace(',', '.', $this->earningReport->net_revenue);

                // Genel sistem komisyonunu düş
                $systemCommission = bcmul($this->balance, bcdiv($this->general_system_commission_rate, '100'));
                Log::info('General system commission: ' . $systemCommission);
                $this->balance = bcsub($this->balance, $systemCommission);

                $this->song = Song::with('participants', 'broadcasts.label.user')
                    ->whereNotNull('isrc')
                    ->where('isrc', $earningReport->isrc_code)
                    ->first();

                if ($this->song) {
                    Log::info('Found song with ISRC: ' . $this->song->isrc);

                    $labelShare = $this->calculateLabelShare();
                    Log::info('Label share calculated: ' . $labelShare);
                    $participantEarnings = $this->calculateParticipantEarnings($labelShare);
                    Log::info('Participant earnings calculated: ' . json_encode($participantEarnings));

                    $totalParticipantEarnings = array_sum(array_column($participantEarnings, 'earning'));
                    $labelFinalEarning = bcsub($labelShare, $totalParticipantEarnings);

                    $sales_type = $this->earningReport->sales_type == 'PLATFORM PROMOTION' ? 'Promosyon' : 'Kazanç';
                    // Label'a ve katılımcılara ödeme yap
                    $this->createEarnings($this->earningReport, $labelFinalEarning, $this->earningReport->label_id, $sales_type);
                    foreach ($participantEarnings as $participantEarning) {
                        $this->createEarnings($this->earningReport, $participantEarning['earning'], $participantEarning['user_id'], __('panel.earning.participant_earning'));
                    }

                    // İşlem başarılı olduysa durumu COMPLETED olarak güncelle
                    $this->earningReport->status = EarningReportStatusEnum::COMPLETED->value;
                    Log::info('Earning report ID: ' . $this->earningReport->id . ' marked as COMPLETED with label earning: ' . $labelFinalEarning . ' and total participant earnings: ' . $totalParticipantEarnings);
                    $this->earningReport->save();
                    DB::commit();
                } else {
                    // Şarkı bulunamazsa durumu FAILED olarak güncelle
                    Log::warning('Song not found for ISRC code: ' . $this->earningReport->isrc_code);
                    $this->earningReport->status = EarningReportStatusEnum::FAILED->value;
                    $this->earningReport->save();
                    DB::commit();
                }
            } catch (\Exception $e) {
                // Herhangi bir hata olursa durumu FAILED olarak güncelle
                Log::error('EarningJob failed for earning report ID: ' . $this->earningReport->id . ' with error: ' . $e->getMessage());
                $this->earningReport->status = EarningReportStatusEnum::FAILED->value;
                $this->earningReport->save();
                DB::rollBack();
            }
        }

        Log::info('EarningJob completed.');
    }

    protected function calculateLabelShare()
    {
        $labelUser = $this->song->broadcasts()->first()->label?->user;
        $labelCommissionRate = $labelUser->commission_rate;
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
                $upperRate = $upperUser->commission_rate;
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
        Earning::create([
            'earning_report_id' => $earningReport->id,
            'name' => $name,
            'report_date' => $earningReport->report_date,
            'sales_date' => $earningReport->sales_date,
            'country' => $earningReport->country,
            'platform' => $earningReport->platform,
            'user_id' => $userId,
            'uploaded_user_id' => $earningReport->file->user_id,
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
