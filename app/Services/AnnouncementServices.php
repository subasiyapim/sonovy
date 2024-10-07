<?php

namespace App\Services;

use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use App\Models\Announcement;
use App\Models\AnnouncementUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnouncementServices
{
    public static function create(array $data): mixed
    {

        DB::beginTransaction();

        try {
            $announcement = Announcement::create($data);

            switch ($data['receivers']) {
                case AnnouncementReceiversEnum::ALL->value:
                    //$users = User::select('id')->get();
                    //$announcement->users()->sync($users);

                    break;
                case AnnouncementReceiversEnum::SELECTED->value:
                    //$announcement->users()->sync($data['selected']);

                    foreach ($data['selected'] as $user) {
                        foreach ($data['type'] as $type) {
                            AnnouncementUser::create([
                                'announcement_id' => $announcement->id,
                                'user_id' => $user,
                                'type' => $type,
                                'status' => 'NEW',
                                'content' => $announcement->content
                            ]);
                        }
                    }
                    break;
                case AnnouncementReceiversEnum::ALL_BUT->value:
                    $users = User::select('id')->whereNotIn('id', $data['exceptions'])->get();
                    //$announcement->users()->sync($users);
                    foreach ($users as $user) {
                        foreach ($data['type'] as $type) {
                            AnnouncementUser::create([
                                'announcement_id' => $announcement->id,
                                'user_id' => $user->id,
                                'type' => $type,
                                'status' => 'NEW'
                            ]);
                        }
                    }
                    break;
            }

            DB::commit();

            return $announcement;

        } catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }
    }

    public static function update(Announcement $announcement, array $data): mixed
    {

        DB::beginTransaction();

        try {

            $announcement->update($data);

            switch ($data['receivers']) {
                case AnnouncementReceiversEnum::ALL->value:
                    //$users = User::select('id')->get();
                    //$announcement->users()->sync($users);

                    break;
                case AnnouncementReceiversEnum::SELECTED->value:
                    //$announcement->users()->sync($data['selected']);
                    foreach ($data['receivers'] as $user) {
                        foreach ($data['type'] as $type) {
                            AnnouncementUser::create([
                                'announcement_id' => $announcement->id,
                                'user_id' => $user,
                                'type' => $type,
                                'status' => 'NEW'
                            ]);
                        }
                    }
                    break;
                case AnnouncementReceiversEnum::ALL_BUT->value:
                    $users = User::select('id')->whereNotIn('id', $data['exceptions'])->get();
                    //$announcement->users()->sync($users);
                    foreach ($users as $user) {
                        foreach ($data['type'] as $type) {
                            AnnouncementUser::create([
                                'announcement_id' => $announcement->id,
                                'user_id' => $user->id,
                                'type' => $type,
                                'status' => 'NEW'
                            ]);
                        }
                    }
                    break;
            }

            DB::commit();

            return $announcement;

        } catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }

    }

    public static function get()
    {
        return Announcement::active()->get();
    }

    public static function search($search): mixed
    {
        return Announcement::where('name', 'like', '%' . $search . '%')->get();
    }
}
