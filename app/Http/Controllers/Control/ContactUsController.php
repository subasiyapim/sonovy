<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('contact_us_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contact_us = ContactUs::advancedFilter();

        return inertia('Control/ContactUs/Index', compact('contact_us'));

    }

    /**
     * Display the specified resource.
     */
    public function show(ContactUs $contact_us)
    {
        abort_if(Gate::denies('contact_us_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($contact_us, Response::HTTP_OK);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactUs $contact_us)
    {
        abort_if(Gate::denies('contact_us_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contact_us->delete();

        return redirect()->back();
    }
}
