<?php

namespace App\Http\Controllers\Control;

use App\Enums\IntegrationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integration\IntegrationUpdateRequest;
use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('integration_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $integrations = Integration::when($request->has('type'), function ($query) use ($request) {
            $query->where('type', $request->get('type'));
        })->advancedFilter();

        $types = array_merge([['label' => 'Tümü', 'value' => null]], IntegrationTypeEnum::getTitlesFromInputFormat());

        return inertia('Control/Integrations/Index', compact('integrations', 'types'));
    }

    public function edit(Integration $integration)
    {
        abort_if(Gate::denies('integration_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $types = IntegrationTypeEnum::getTitlesFromInputFormat();

        return inertia('Control/Integrations/Edit', compact('integration', 'types'));
    }

    public function update(IntegrationUpdateRequest $request, Integration $integration)
    {
        $integration->update($request->validated());
        Cache::forget('platform');

        return redirect()
            ->route('dashboard.integrations.index')
            ->with([
                'notification' => [
                    'type' => 'success',
                    'title' => 'Success!',
                    'message' => 'Integration updated successfully.'
                ]
            ]);
    }
}
