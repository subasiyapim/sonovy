<?php

namespace App\Http\Controllers\Control;


use App\Enums\PaymentProcessTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;


class  SubscriptionManagementController extends Controller
{

    public function index(Request $request)
    {

        return inertia('Control/SubscriptionManagement/Index',
            [

            ]
        );

    }


}
