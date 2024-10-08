<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArtistBranches\ArtistBranchesRequest;
use App\Http\Requests\Title\TitleUpdateRequest;
use App\Models\ArtistBranch;
use App\Models\Title;
use App\Services\ArtistBranchServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ArtistBranchController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('artist_branch_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artistBranches = ArtistBranch::advancedFilter();

        return inertia('Control/ArtistBranches/Index', compact('artistBranches'));
    }

    public function store(ArtistBranchesRequest $request)
    {
        ArtistBranch::create($request->validated()["translations"]);

        return to_route('dashboard.artist-branches.index')
            ->with([
                'notification' => __('panel.notification_created',
                    ['model' => __('panel.artist-branch.title_singular')])
            ]);
    }

    public function update(ArtistBranchesRequest $request, ArtistBranch $artistBranch)
    {
        $artistBranch->update($request->validated()["translations"]);

        return to_route('dashboard.artist-branches.index')
            ->with([
                'notification' => __('panel.notification_updated',
                    ['model' => __('panel.artist-branch.title_singular')])
            ]);
    }

    public function destroy(ArtistBranch $artistBranch)
    {
        $artistBranch->delete();

        return to_route('dashboard.artist-branches.index')
            ->with([
                'notification' => __('panel.notification_deleted',
                    ['model' => __('panel.artist-branch.title_singular')])
            ]);
    }

    public function getBranches()
    {
        abort_if(Gate::denies('artist_branch_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return ArtistBranchServices::getBranchesFromInputFormat();

    }

}
