<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\HelpCenter\VideoStoreRequest;
use App\Http\Requests\HelpCenter\VideoUpdateRequest;
use App\Models\HelpCenterVideo;
use App\Services\HelpCenterVideoServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpCenterVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('help_center_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $videos = HelpCenterVideo::advancedFilter();

        return inertia('Control/HelpCenter/Video_List', compact('videos'));

    }


    public function create()
    {

        return inertia('Control/HelpCenter/Video_Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(VideoStoreRequest $request)
    {

        $video = HelpCenterVideoServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'HelpCenter created successfully',
                'data' => $video
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(HelpCenterVideo $video)
    {
        abort_if(Gate::denies('help_center_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($video, Response::HTTP_OK);
    }

    public function edit(HelpCenterVideo $video)
    {

        abort_if(Gate::denies('help_center_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return inertia('Control/HelpCenter/Video_Edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VideoUpdateRequest $request, HelpCenterVideo $video)
    {

        HelpCenterVideoServices::update($video, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'HelpCenter updated successfully',
                'data' => $video
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $videos = HelpCenterVideoServices::search($search);

        return response()->json($videos, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HelpCenterVideo $video)
    {
        abort_if(Gate::denies('help_center_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $video->delete();

        return redirect()->back();
    }
}
