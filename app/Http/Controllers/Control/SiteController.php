<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteStoreRequest;
use App\Models\Site;
use App\Models\User;
use App\Services\MediaServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        $userId = $request->id ?? auth()->id();
        try {

            $user = User::find($userId);

            if (!$user->uuid) {
                $user->update(
                    [
                        'uuid' => Str::uuid()
                    ]
                );
            }

            $data['user_id'] = $user->id;

            $site = Site::updateOrCreate(
                ['domain' => $data['domain']],
                $data
            );

            if ($request->hasFile('logo')) {
                $file_name = Str::slug($site->domain.'-logo');
                MediaServices::upload($site, $request->file('logo'), $file_name, $file_name, 'sites', 'sites');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with(['error' => 'Error: '.$e->getMessage()]);
        }

        DB::commit();

        return redirect()->back()
            ->with(['notification' => __('panel.notification_created', ['model' => __('panel.site.title_singular')])]);
    }
}
