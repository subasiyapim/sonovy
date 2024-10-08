<?php

namespace App\Http\Controllers\Control;

use App\Enums\AnnouncementTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\Template\AnnouncementTemplateStoreRequest;
use App\Http\Requests\Announcement\Template\AnnouncementTemplateUpdateRequest;
use App\Models\AnnouncementTemplate;
use App\Services\AnnouncementTemplateServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class  AnnouncementTemplateController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('announcement_template_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcement_templates = AnnouncementTemplate::advancedFilter();

        return inertia('Control/Announcements/Templates/Index', compact('announcement_templates'));

    }

    public function create()
    {
        abort_if(Gate::denies('announcement_template_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcement_types = AnnouncementTypeEnum::getKeyTitleArray();

        return inertia('Control/Announcements/Templates/Create', compact('announcement_types'));
    }

    public function store(AnnouncementTemplateStoreRequest $request)
    {
        $announcement_template = AnnouncementTemplateServices::create($request->validated());


        return redirect()->back()
            ->with([
                'notification' => [
                    'title' => 'Success',
                    'message' => 'Announcement Template created successfully',
                    'data' => $announcement_template
                ]
            ]);

    }

    public function show(AnnouncementTemplate $announcement_template)
    {
        abort_if(Gate::denies('announcement_template_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($announcement_template, Response::HTTP_OK);
    }

    public function edit(AnnouncementTemplate $announcement_template)
    {
        abort_if(Gate::denies('announcement_template_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcement_types = AnnouncementTypeEnum::getKeyTitleArray();

        return inertia('Control/Announcements/Templates/Edit', compact('announcement_template',
            'announcement_types'));
    }

    public function update(AnnouncementTemplateUpdateRequest $request, AnnouncementTemplate $announcement_template)
    {

        AnnouncementTemplateServices::update($announcement_template, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Announcement Template updated successfully',
                'data' => $announcement_template
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);

        $announcement_templates = AnnouncementTemplateServices::search($request->search);

        return response()->json($announcement_templates, Response::HTTP_OK);
    }

    public function destroy(AnnouncementTemplate $announcement_template)
    {
        abort_if(Gate::denies('announcement_template_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $announcement_template->delete();

        return redirect()->back();
    }
}
