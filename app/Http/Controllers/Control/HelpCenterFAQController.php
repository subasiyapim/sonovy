<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\HelpCenter\FAQStoreRequest;
use App\Http\Requests\HelpCenter\FAQUpdateRequest;
use App\Models\HelpCenterFAQ;
use App\Services\HelpCenterFAQServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpCenterFAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('help_center_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $faqs = HelpCenterFAQ::advancedFilter();

        return inertia('Control/HelpCenter/FAQ_List', compact('faqs'));

    }


    public function create()
    {

        return inertia('Control/HelpCenter/FAQ_Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(FAQStoreRequest $request)
    {

        $faq = HelpCenterFAQServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'HelpCenter created successfully',
                'data' => $faq
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(HelpCenterFAQ $faq)
    {
        abort_if(Gate::denies('help_center_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($faq, Response::HTTP_OK);
    }

    public function edit(HelpCenterFAQ $faq)
    {

        abort_if(Gate::denies('help_center_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return inertia('Control/HelpCenter/FAQ_Edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FAQUpdateRequest $request, HelpCenterFAQ $faq)
    {

        HelpCenterFAQServices::update($faq, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'HelpCenter updated successfully',
                'data' => $faq
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $faqs = HelpCenterFAQServices::search($search);

        return response()->json($faqs, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HelpCenterFAQ $faq)
    {
        abort_if(Gate::denies('help_center_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $faq->delete();

        return redirect()->back();
    }
}
