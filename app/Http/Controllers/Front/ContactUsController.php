<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUs\ContactUsStoreRequest;
use App\Models\ContactUs;
use App\Services\ContactUsServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactUsController extends Controller
{

    public function store(ContactUsStoreRequest $request)
    {
        $contact_us = ContactUsServices::create($request->validated());

        return redirect()->back()->with([
            'notification' => [
                'title' => 'Success',
                'message' => 'Contact Us has been sent successfully',
                'data' => $contact_us
            ]
        ]);
    }
}
