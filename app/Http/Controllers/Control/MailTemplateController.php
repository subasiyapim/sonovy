<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailTemplate\MailTemplateStoreRequest;
use App\Http\Requests\MailTemplate\MailTemplateUpdateRequest;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('mail_template_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailTemplates = MailTemplate::with('translations')->advancedFilter();

        return inertia('Control/MailTemplates/Index', [
            'mailTemplates' => $mailTemplates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('mail_template_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/MailTemplates/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MailTemplateStoreRequest $request)
    {
        $data = $request->except('translations');
        $data['code'] = Str::slug($request->validated()['code']);

        $translations = $request->validated()['translations'];

        foreach ($translations as $key => $translation) {
            if ($translation['subject'] == null && $translation['body'] == null) {
                unset($translations[$key]);
                continue;
            }
            $data[$key] = $translation;
        }

        MailTemplate::create($data);

        return redirect()->route('dashboard.mail-templates.index')->with([
            'notification' => [
                'text' => __('control.notification_created', ['model' => __('control.mail_template.title_singular')]),
                'type' => 'success',
            ]
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(MailTemplate $mailTemplate)
    {
        abort_if(Gate::denies('mail_template_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MailTemplate $mailTemplate)
    {
        abort_if(Gate::denies('mail_template_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/MailTemplates/Edit', [
            'mailTemplate' => $mailTemplate->load('translations'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MailTemplateUpdateRequest $request, MailTemplate $mailTemplate)
    {
        $data = $request->except('translations');

        $translations = $request->validated()['translations'];

        foreach ($translations as $key => $translation) {
            if ($translation['subject'] == null && $translation['body'] == null) {
                unset($translations[$key]);
                continue;
            }
            $data[$key] = $translation;
        }

        $mailTemplate->update($data);

        return redirect()->route('dashboard.mail-templates.index')->with([
            'notification' => [
                'text' => __('control.notification_updated', ['model' => __('control.mail_template.title_singular')]),
                'type' => 'success',
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MailTemplate $mailTemplate)
    {
        abort_if(Gate::denies('mail_template_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mailTemplate->delete();

        return redirect()->route('dashboard.mail-templates.index')->with([
            'notification' => [
                'text' => __('control.notification_deleted', ['model' => __('control.mail_template.title_singular')]),
                'type' => 'success',
            ]
        ]);

    }
}
