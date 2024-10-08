<?php

namespace App\Http\Controllers\Control;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentProcessTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Resources\Panel\OrderResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Feature;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Services\IyzicoServices;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use function Spatie\LaravelPdf\Support\pdf;

class  OrderController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('invoice_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orders = OrderResource::collection(Order::with('user')->advancedFilter());

        return inertia('Control/Orders/Index', compact('orders'));
    }

    public function store(OrderStoreRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        $user = auth()->user();

        if (isset($data['plan_id']) && !empty($data['plan_id'])) {

            $model = Plan::active()->with('items')->find($data['plan_id']);

            if ($data['payment_period'] == 'monthly') {

                $currentDate = new DateTime();
                $payment_period = $currentDate->modify('+1 month');
                $payment_amount = $model->monthly_price;
            } else {

                $currentDate = new DateTime();
                $payment_period = $currentDate->modify('+1 year');
                $payment_amount = $model->annual_price;
            }
            $result = $this->doPayment(
                $request->payment_type,
                $payment_amount,
                $payment_period,
                $model,
                'Plan',
                $data,
                $user
            );

            if ($result['status']) {
                return to_route('dashboard.profile.detail')
                    ->with([
                        'notification' => [
                            'message' => __('panel.order.notification_payment_successful'),
                            'type' => 'success'
                        ]
                    ]);
            } else {
                return to_route('dashboard.profile.detail')
                    ->with([
                        'notification' => [
                            'message' => $result['message'],
                            'type' => 'error'
                        ]
                    ]);
            }
        } else {

            foreach ($data['features'] as $feature) {

                $model = Feature::active()->with('item')->find($feature['id']);

                if ($model->period->value == '1') { // One Time
                    $payment_period = null;
                } elseif ($model->period->value == '2') { // Monthly

                    $currentDate = new DateTime();
                    $payment_period = $currentDate->modify('+1 month');
                } else { //($db_feature->period == '3') { Annually
                    $currentDate = new DateTime();
                    $payment_period = $currentDate->modify('+1 year');
                }

                $payment_amount = $model->amount;

                $result = $this->doPayment(
                    $request->payment_type,
                    $payment_amount,
                    $payment_period,
                    $model,
                    'Feature',
                    $data,
                    $user
                );

                if ($result['status'] === true) {
                    return to_route('dashboard.profile.detail')
                        ->with([
                            'notification' => [
                                'message' => __('panel.order.notification_payment_successful'),
                                'type' => 'success'
                            ]
                        ]);
                } elseif ($result['status'] === false) {
                    return to_route('dashboard.profile.detail')
                        ->with([
                            'notification' => [
                                'message' => $result['message'],
                                'type' => 'error'
                            ]
                        ]);
                } else { // Redirect

                    $html = $result['html'];
                    return view('IyzicoPaymentSubmit', compact('html'));
                }
            }
        }
    }

    private function doPayment($payment_type, $amount, $expiration, $model, $modelType, $data, $user): array
    {

        if ($payment_type == 1) { // Payment with User Balance
            if ($user->balance > $amount) {

                $user->orders()->create([
                    'model_id' => $model->id,
                    'model_type' => get_class($model),
                    'amount' => $amount,
                    'invoice_date' => new DateTime(),
                    'expiration_date' => $expiration,
                    'status' => OrderStatusEnum::PAID->value,
                    'payment_service' => 'Balance',
                    'payment_info' => ['Balance' => Auth::user()->balance - (float) $amount],
                    'plan' => $model->toArray(),
                ]);

                $user->payments()->create([
                    'process_type' => PaymentProcessTypeEnum::SALES->value,
                    'status' => PaymentStatusEnum::APPROVED->value,
                    'amount' => $amount,
                    'payment_type' => PaymentTypeEnum::CASH->value,
                ]);

                return ['status' => true];
            } else {

                return ['status' => false, 'message' => __('panel.order.notification_payment_successful')];
            }
        } else { // Payment With Card

            $order = $user->orders()->create([
                'model_id' => $model->id,
                'model_type' => get_class($model),
                'amount' => $amount,
                'expiration_date' => $expiration,
                'payment_service' => 'Iyzico',
                'plan' => $model->toArray()
            ]);

            // Separate Firstname Lastname
            $user_first_name = $user->name;
            $user_last_name = $user->name;
            if (strpos($user->name, ' ') !== false) {

                $user_fullname = explode(' ', $user->name);
                $user_first_name = $user_fullname[0];
                $user_last_name = $user_fullname[1];
            }

            // Separate Card Expire Year and Month
            $expire_date = explode('/', $data['expiration_date']);

            $result = IyzicoServices::init3DPayment(
                $data['amount'],
                $order->id, // ConversionId
                'B'.$order->id, // BasketId
                $user->id,
                $user->email,
                $user_first_name,
                $user_last_name,
                $user->phone,
                '1234567890', //Todo:will add to user table
                'Some address, No, Apartment, unit', //Todo:will add to user table
                'Turkey', //Todo:will add to user table
                'Istanbul', //Todo:will add to user table
                '12345', //Todo:will add to user table
                $data['card_holder'],
                $data['card_number'],
                $expire_date[0],
                $expire_date[1],
                $data['cvv'],
                $model->id,
                'BasketItem'.$model->id, // BasketItemName
                $modelType
            );

            if ($result->getStatus() == 'success') {

                $html = $result->getHtmlContent();
                return ['status' => 'redirect', 'html' => $html];
            } else {

                return ['status' => false, 'message' => $result->getErrorMessage()];
            }
        }
    }

    public function pdf(Order $order)
    {
        // abort_if(Gate::denies('platform_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $name = 'invoice-'.$order->id.'-'.Carbon::parse($order->invoice_date)->format('d-m-Y').'.pdf';

        $company = Setting::whereIn('key', ['project', 'email', 'phone', 'address'])->get([
            'value', 'key'
        ])->pluck('value', 'key');

        return pdf()
            ->view('pdfs.invoice',
                [
                    'order' => new OrderResource($order->load('user')),
                    'company' => $company,
                    'billing' => Order::getUserBillings($order)
                ])
            ->name($name);

        // return pdf()
        //     ->view('pdf.invoice', compact('invoice'))
        //     ->name('invoice-2023-04-10.pdf')
        //     ->download();
    }
}
