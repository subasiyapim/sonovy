<?php

namespace App\Http\Controllers\Control;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentProcessTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\AdvanceYourselfRequest;
use App\Http\Requests\Payment\PaymentYourselfRequest;
use App\Http\Requests\Payment\RequestPaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\EarningService;
use App\Services\IyzicoServices;
use App\Services\MediaServices;
use App\Services\PaymentService;
use App\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

class  PaymentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payments = Auth::user()->payments()->orderByDesc('id')->advancedFilter();
        $balance = Number::currency(EarningService::balance(), 'USD', app()->getLocale());
        $pendingPayment = Number::currency(PaymentService::getPendingPayment(), 'USD', app()->getLocale());
        $account = BankAccount::where('user_id', Auth::id())->first();
        $minPaymentRequest = Number::format(Setting::where('key', 'min_payment_request')->first()?->value ?? 100, 2, 2,
            app()->getLocale());
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');

        return inertia(
            'Control/Finance/Payment/Index',
            [
                'payments' => PaymentResource::collection($payments)->resource,
                'balance' => $balance,
                'pendingPayment' => $pendingPayment,
                'account' => $account,
                'minPaymentRequest' => $minPaymentRequest,
                'countries' => $countries,
                'created_at' => Carbon::parse($request->created_at)->format('d.m.Y')
            ]
        );
    }

    public function store(RequestPaymentRequest $request)
    {
        Payment::create(
            [
                'user_id' => Auth::id(),
                'process_type' => $request->process_type,
                'status' => PaymentStatusEnum::PENDING->value,
                'amount' => $request->amount,
                'account_id' => isset($request->account_id) ? $request->account_id : null,
            ]
        );

        return to_route('dashboard.finance-and-earnings.index')
            ->with([
                'notification' => [
                    'message' => $request->process_type == 4
                        ? 'Anans talebiniz başarıyla alındı. En kısa sürede işleme alınacaktır.'
                        : 'Ödeme talebiniz başarıyla alındı. En kısa sürede işleme alınacaktır.',
                    'type' => 'success'
                ]
            ]);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return to_route('dashboard.payments.index')
            ->with([
                'notification' => [
                    'message' => 'Ödeme talebi başarıyla silindi.',
                    'type' => 'success'
                ]
            ]);
    }

    public function iyzico_callback(Request $request)
    {

        if ($request->status == 'success') {

            $order = Order::where('id', $request->conversationId)->first();
            if ($order) {

                $result = IyzicoServices::confirm3DPayment($request->paymentId, $request->conversationId);
                if ($result->getStatus() == 'success') {

                    $order->payment_info = [
                        'paymentId' => $result->getPaymentId(),
                        'authCode' => $result->getAuthCode()
                    ];
                    $order->status = OrderStatusEnum::PAID->value;
                    $order->save();

                    $user = auth()->user();
                    $user->payments()->create([
                        'process_type' => PaymentProcessTypeEnum::SALES->value,
                        'status' => PaymentStatusEnum::APPROVED->value,
                        'amount' => $order->amount,
                        'payment_type' => PaymentTypeEnum::CASH->value,
                    ]);

                    return to_route('dashboard.profile.detail')
                        ->with([
                            'notification' => [
                                'message' => __('control.order.notification_payment_successful'),
                                'type' => 'success'
                            ]
                        ]);
                } else {

                    return to_route('dashboard.profile.detail')
                        ->with(['notification' => ['message' => $result->getErrorMessage(), 'type' => 'error']]);
                }
            } else {

                return to_route('dashboard.profile.detail')
                    ->with([
                        'notification' => [
                            'message' => __('control.order.notification_order_not_found'),
                            'type' => 'error'
                        ]
                    ]);
            }
        } else {

            return to_route('dashboard.profile.detail')
                ->with([
                    'notification' => [
                        'message' => __('control.order.notification_payment_callback_error'),
                        'type' => 'error'
                    ]
                ]);
        }
    }
}
