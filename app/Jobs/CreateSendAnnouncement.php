<?php

namespace App\Jobs;

use App\Enums\AnnouncementReceiversEnum;
use App\Models\Announcement;
use App\Models\AnnouncementUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateSendAnnouncement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cdt = date('Y-m-d H:i:s'); // Current DateTime
        $announcement = Announcement::where('status', 'NEW')->whereNull('from')
            ->where(function ($query) use ($cdt) {
                $query->where('from', '<=', $cdt)
                    ->orWhereNull('to', '=>', $cdt);
            })->latest()->first();


        if ($announcement) {
            if ($announcement->receivers == AnnouncementReceiversEnum::ALL->value) {
                $users = User::select('id')->get();
                foreach ($users as $user) {
                    $types = $announcement->type;
                    foreach ($types as $type) {
                        try{
                            $announcementUser = AnnouncementUser::create([
                                'announcement_id' => $announcement->id,
                                'user_id' => $user->id,
                                'type' => $type,
                                'status' => 'NEW',
                                'content' => $announcement->content
                            ]);
                        }
                        catch (\Exception $exception){
                            Log::error($exception->getMessage());
                        }

                    }
                }
            }
        }
    }
}
