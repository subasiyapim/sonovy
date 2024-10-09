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
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\IyzicoServices;
use App\Services\MediaServices;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class  PaymentController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment_requests = Payment::with('user.country', 'account')
            ->when($request->has('d') && $request->d[0] && $request->d[1], function ($query) use ($request) {
                $query->whereBetween('created_at',
                    [Carbon::parse($request->d[0])->format('Y-m-d'), Carbon::parse($request->d[1])->format('Y-m-d')]);
            })
            ->when($request->has('u_id') && $request->u_id, function ($query) use ($request) {
                $query->where('user_id', $request->u_id);
            })
            ->where('process_type', PaymentProcessTypeEnum::MONEY_TRANSFER->value)
            ->advancedFilter();


        if ($request->has('u_id') && $request->u_id) {
            $user = User::where('id', $request->u_id)->first();
        } else {
            $user = [];
        }

        return inertia('Control/Payments/PaymentIndex', compact('payment_requests', 'user'));
    }

    public function advanceIndex(Request $request): \Inertia\Response
    {
        abort_if(Gate::denies('payment_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $advance_requests = Payment::with('user.country')
            ->when($request->has('d') && $request->d[0] && $request->d[1], function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->d[0], $request->d[1]]);
            })
            ->when($request->has('u_id') && $request->u_id, function ($query) use ($request) {
                $query->where('user_id', $request->u_id);
            })
            ->where('process_type', PaymentProcessTypeEnum::APPROVED_ADVANCE->value)
            ->advancedFilter();

        if ($request->has('u_id') && $request->u_id) {
            $user = User::where('id', $request->u_id)->first();
        } else {
            $user = [];
        }

        return inertia('Control/Payments/AdvanceIndex', compact('advance_requests', 'user'));
    }

    public function confirmPayments(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->id);

        $commission_calculate = PaymentService::calculateCommission($request->amount);

        $payment->update([
            'amount' => $commission_calculate['amount'],
            'commission_rate' => $commission_calculate['commission_rate'],
            'commission' => $commission_calculate['commission'],
            'status' => PaymentStatusEnum::APPROVED->value,
            'payment_date' => now(),
        ]);

        return to_route('dashboard.payments.index')
            ->with([
                'notification' => [
                    'message' => 'Ödeme talebi başarıyla onaylandı.',
                    'type' => 'success'
                ]
            ]);
    }

    public function confirmAdvance(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->id);

        $payment->update([
            'status' => PaymentStatusEnum::APPROVED->value,
        ]);

        return to_route('dashboard.payments.advance-index')
            ->with([
                'notification' => [
                    'message' => 'Avans talebi başarıyla onaylandı.',
                    'type' => 'success'
                ]
            ]);

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

    public function paymentYourself(PaymentYourselfRequest $request)
    {
        $commission_calculate = PaymentService::calculateCommission($request->amount);

        $payment = Payment::create(
            [
                'user_id' => $request->user_id,
                'process_type' => PaymentProcessTypeEnum::MONEY_TRANSFER->value,
                'status' => PaymentStatusEnum::APPROVED->value,
                'amount' => $commission_calculate['amount'],
                'payment_date' => $request->payment_date.' '.$request->payment_time,
                'commission_rate' => $commission_calculate['commission_rate'],
                'commission' => $commission_calculate['commission'],
            ]
        );

        if ($request->hasFile('receipt')) {
            MediaServices::upload($payment, $request->file('receipt'), null, null, 'payment_receipts',
                'payment_receipts');
        }

        return to_route('dashboard.payments.index')
            ->with([
                'notification' => [
                    'message' => 'Ödeme talebi başarıyla alındı. En kısa sürede işleme alınacaktır.',
                    'type' => 'success'
                ]
            ]);
    }

    public function advanceYourself(AdvanceYourselfRequest $request)
    {
        Payment::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'user_id' => $request->user_id,
                'process_type' => PaymentProcessTypeEnum::APPROVED_ADVANCE->value,
                'status' => PaymentStatusEnum::APPROVED->value,
                'amount' => $request->amount,
                'payment_date' => now(),
            ]
        );

        return redirect()->back()
            ->with([
                'notification' => [
                    'message' => 'Avans talebi başarıyla alındı. En kısa sürede işleme alınacaktır.',
                    'type' => 'success'
                ]
            ]);
    }

    public function rejectPayments(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->id);
        $payment->update(['status' => PaymentStatusEnum::REJECTED->value,]);

        return to_route('dashboard.payments.index')
            ->with([
                'notification' => [
                    'message' => 'Ödeme talebi başarıyla reddedildi.',
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
