<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonial\TestimonialStoreRequest;
use App\Http\Requests\Testimonial\TestimonialUpdateRequest;
use App\Models\Testimonial;
use App\Services\TestimonialServices;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('testimonial_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $testimonials = Testimonial::advancedFilter();

        return inertia('Control/Testimonials/Index', compact('testimonials'));

    }


    public function create()
    {

        return inertia('Control/Testimonials/Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TestimonialStoreRequest $request)
    {
        $testimonial = TestimonialServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Testimonial created successfully',
                'data' => $testimonial
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial)
    {
        abort_if(Gate::denies('testimonial_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($testimonial->load('media'), Response::HTTP_OK);
    }

    public function edit(Testimonial $testimonial)
    {

        abort_if(Gate::denies('testimonial_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return inertia('Control/Testimonials/Edit', compact('testimonial'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestimonialUpdateRequest $request, Testimonial $testimonial)
    {

        TestimonialServices::update($testimonial, $request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Testimonial updated successfully',
                'data' => $testimonial
            ]
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:255'
        ]);


        $search = $request->input('search');

        $testimonials = TestimonialServices::search($search);

        return response()->json($testimonials, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        abort_if(Gate::denies('testimonial_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $testimonial->delete();

        return redirect()->back();
    }
}
