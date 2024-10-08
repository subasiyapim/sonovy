<?php

namespace App\Jobs;

use App\Enums\AnnouncementTypeEnum;
use App\Events\NewMaintenanceEvent;
use App\Events\NewNotificationEvent;
use App\Facades\SMS\TwilioServices;
use App\Models\AnnouncementUser;
use App\Services\EmailServices;
use App\Services\MaintenanceServices;
use App\Services\MessageBoxServices;
use App\Services\NetGsmServices;
use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    /**
     * Create a new job instance.
     */
    public function __construct()
    {


    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $waitingToSend = AnnouncementUser::with('user', 'announcement')
            ->where('status', 'NEW')->get();

        foreach ($waitingToSend as $send) {

            switch ($send->type) {

                case AnnouncementTypeEnum::SITE->value:
                    $this->siteNotification($send);
                    break;
                case AnnouncementTypeEnum::MAINTENANCE->value:
                    $this->showMaintenance($send);
                    break;
                case AnnouncementTypeEnum::EMAIl->value:
                    $this->sendEmail($send);
                    break;
                case AnnouncementTypeEnum::SMS->value:
                    $this->sendSMS($send);
                    break;
            }
        }
    }

    private function siteNotification(AnnouncementUser $notification)
    {

        NewNotificationEvent::dispatch($notification);
    }

    private function showMaintenance(AnnouncementUser $maintenance)
    {

        NewMaintenanceEvent::dispatch($maintenance);
    }

    private function sendEmail(AnnouncementUser $email)
    {

        EmailServices::sendAnnouncementEmail($email);
    }

    private function sendSMS(AnnouncementUser $sms)
    {

        SMSService::sendSMS($sms->user->phone, $sms->content);
        /*if (substr($sms->user->phone, 0, 3) === '+90') {

            $phone = explode(" ", $sms->user->phone);
            try {

                NetGsmServices::sendSms($phone[1], $sms->content);
            }
            catch (\Exception $e) {

                Log::error($e->getMessage());
            }
        }
        else {

            $phone = str_replace(" ", "", $sms->user->phone);
            try {

                $twilio = new TwilioServices();
                $twilio->send($phone, $sms->content);
            }
            catch (\Exception $e) {

                Log::error($e->getMessage());
            }
        }*/
    }
}
