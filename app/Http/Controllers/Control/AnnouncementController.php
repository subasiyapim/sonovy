<?php

namespace App\Http\Controllers\Control;

use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\AnnouncementStoreRequest;
use App\Http\Requests\Announcement\AnnouncementUpdateRequest;
use App\Models\Announcement;
use App\Models\AnnouncementTemplate;
use App\Models\User;
use App\Services\AnnouncementServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class  AnnouncementController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('announcement_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcements = Announcement::advancedFilter();

        return inertia('Control/Management/Announcements/index', compact('announcements'));
    }

    public function create()
    {
        abort_if(Gate::denies('announcement_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcement_types = AnnouncementTypeEnum::getKeyTitleArray();
        $announcement_receivers = AnnouncementReceiversEnum::getKeyTitleArray();

        return inertia(
            'Control/Announcements/Create',
            compact('announcement_types', 'announcement_receivers')
        );
    }

    public function store(AnnouncementStoreRequest $request): \Illuminate\Http\RedirectResponse
    {

        $announcement = AnnouncementServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Announcement created successfully',
                'data' => $announcement
            ]
        ]);
    }

    public function show(Announcement $announcement)
    {
        abort_if(Gate::denies('announcement_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($announcement, Response::HTTP_OK);
    }

    public function edit(Announcement $announcement)
    {
        abort_if(Gate::denies('announcement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $announcement->load('template');
        $announcement_types = AnnouncementTypeEnum::getKeyTitleArray();
        $announcement_receivers = AnnouncementReceiversEnum::getKeyTitleArray();

        $selected = null;
        $exceptions = null;
        if ($announcement->receivers == AnnouncementReceiversEnum::SELECTED->value) {

            $users = DB::table('announcement_user')
                ->where('announcement_id', $announcement->id)
                ->pluck('user_id')->toArray();
            $selected = User::whereIn('id', $users)->get(['id', 'name', 'email']);
        } elseif ($announcement->receivers == AnnouncementReceiversEnum::ALL_BUT->value) {

            $users = DB::table('announcement_user')
                ->where('announcement_id', $announcement->id)
                ->pluck('user_id')->toArray();
            $exceptions = User::whereNotIn('id', $users);
        }

        return inertia('Control/Announcements/Edit', compact(
            'announcement',
            'announcement_types',
            'announcement_receivers',
            'selected',
            'exceptions'
        ));
    }

    public function update(AnnouncementUpdateRequest $request, Announcement $announcement)
    {

        AnnouncementServices::update($announcement, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Announcement updated successfully',
                'data' => $announcement
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $search = $request->input('search');

        $announcements = AnnouncementServices::search($search);

        return response()->json($announcements, Response::HTTP_OK);
    }

    public function destroy(Announcement $announcement)
    {
        abort_if(Gate::denies('announcement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcement->delete();

        return redirect()->back();
    }

    //Kullanıcının duyurularının tamamını silme
    public function destroyAll()
    {
        abort_if(Gate::denies('announcement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();

        $user->announcements()->delete();

        return redirect()->back();
    }
}
