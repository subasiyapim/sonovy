<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AnnouncementUser;
use App\Models\Integration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SendNotificationSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $announcements = [
            [
                'name' => 'Announcement 1',
                'type' => ['site'],
                'content' => 'Announcement 1 Content, Please Check.',
                'receivers' => 'all'
            ],
            [
                'name' => 'Announcement 2',
                'type' => ['maintenance'],
                'content' => 'Maintenance Message, Please Check.',
                'receivers' => 'all'
            ],
            [
                'name' => 'Announcement 3',
                'type' => ['site'],
                'content' => 'Specific Notification for you',
                'receivers' => 'selected'
            ]
        ];
        $notifications = [
            [
                'announcement_id' => 1,
                'user_id' => 1,
                'type' => 'site',
                'status' => 'NEW',
                'content' => 'Announcement 1 Content, Please Check.'
            ],
            [
                'announcement_id' => 1,
                'user_id' => 2,
                'type' => 'site',
                'status' => 'NEW',
                'content' => 'Announcement 1 Content, Please Check.'
            ],
            [
                'announcement_id' => 2,
                'user_id' => 1,
                'type' => 'maintenance',
                'status' => 'NEW',
                'content' => 'Maintenance Message, Please Check.'
            ],
            [
                'announcement_id' => 2,
                'user_id' => 2,
                'type' => 'maintenance',
                'status' => 'NEW',
                'content' => 'Maintenance Message, Please Check.'
            ],
            [
                'announcement_id' => 3,
                'user_id' => 1,
                'type' => 'site',
                'status' => 'NEW',
                'content' => 'Specific Notification for you'
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        foreach ($notifications as $notification) {
            AnnouncementUser::firstOrCreate($notification);
        }
    }
}
