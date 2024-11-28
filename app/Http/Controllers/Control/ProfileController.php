<?php

namespace App\Http\Controllers\Control;

use App\Enums\UserTitleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\InvoiceInfoUpdateRequest;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Models\AnnouncementUser;
use App\Models\Country;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\User;
use App\Services\CountryServices;
use App\Services\LanguageServices;
use App\Services\LocaleService;
use App\Services\MediaServices;
use App\Services\TimezoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::with('bankAccounts.country', 'site')->find(Auth::id());

        if ($user->phone) {
            $country = Country::find($user->country_id ?? 228);
            $user->phone = "+".$country->phone_code.$user->phone;
        }

        $languages = LanguageServices::getActiveLanguagesFromInputFormat();
        $localize_list = LocaleService::getLocalizationListFromInputFormat();
        $timezones = TimezoneService::getFromInputFormat();
        $titles = enumToSelectInputFormat(UserTitleEnum::getTitles(), true);
        $plans = Plan::active()->with('items')->get();
        $features = Feature::active()->with('item')->get();
        $available_plan_items = Auth::user()->availablePlanItemsCount();

        foreach ($available_plan_items as $key => $value) {
            $available_plan_items[$key] = [
                'name' => __('control.plan_item.plan_items.'.$key),
                'count' => $value,
            ];
        }

        return inertia('Control/Profile/Index', compact('user', 'timezones', 'languages',
            'countries', 'titles', 'localize_list', 'plans', 'features', 'available_plan_items'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $data = $request->except(['tab', 'image']);
        if (isset($data['phone'])) {
            $data['phone'] = explode(' ', $data['phone'])[1] ?? null;
        }

        $user = User::find($request->id ?? Auth::id());
        $user->update($data);

        if ($request->hasFile('image')) {
            $file_name = Str::slug($user->name, '-'.uniqid());
            MediaServices::upload($user, $request->file('image'), $file_name, $file_name, 'users', 'users');
        }

        if ($request->has('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function updateBillInfo(InvoiceInfoUpdateRequest $request)
    {
        $data = $request->all();

        $user = User::find(Auth::id());
        $user->bill_info = $data;
        $user->save();

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function updateUserBillInfo(InvoiceInfoUpdateRequest $request)
    {
        //TODO PERMISSON CHECK
        $data = $request->all();

        $user = User::find($request->id);
        $user->bill_info = $data;
        $user->save();

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function addCreditCard(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'card_number' => 'required|numeric|digits:16',
            'card_holder' => 'required|string',
            'expiration_date' => 'required|string',
            'cvv' => 'required|string',
        ]);

        $validation->setAttributeNames(
            [
                'card_number' => __('control.profile.credit_card.form.card_number'),
                'card_holder' => __('control.profile.credit_card.form.card_holder'),
                'expiration_date' => __('control.profile.credit_card.form.expiration_date'),
                'cvv' => __('control.profile.credit_card.form.cvv'),
            ]
        );

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation);
        }

        $user = User::find(Auth::id());
        $credit_cards = $user->credit_cards;
        $new_credit_card = $request->only(['card_number', 'card_holder', 'expiration_date', 'cvv']);
        $new_credit_card['is_default'] = 0;

        $credit_cards[] = $new_credit_card;

        $user->update(['credit_cards' => $credit_cards]);

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function setDefaultCreditCard(Request $request)
    {
        $index = $request->index;

        $user = User::find(Auth::id());
        $credit_cards = $user->credit_cards;

        foreach ($credit_cards as $key => $card) {
            $credit_cards[$key]['is_default'] = $key == $index ? 1 : 0;
        }

        $user->update(['credit_cards' => $credit_cards]);

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function updateCreditCard(Request $request)
    {
        $index = $request->index;

        $validation = Validator::make($request->all(), [
            'card_number' => 'required|numeric|digits:16',
            'card_holder' => 'required|string',
            'expiration_date' => 'required|string',
            'cvv' => 'required|string',
        ]);

        $validation->setAttributeNames(
            [
                'card_number' => __('control.profile.credit_card.form.card_number'),
                'card_holder' => __('control.profile.credit_card.form.card_holder'),
                'expiration_date' => __('control.profile.credit_card.form.expiration_date'),
                'cvv' => __('control.profile.credit_card.form.cvv'),
            ]
        );

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation);
        }

        $user = User::find(Auth::id());
        $credit_cards = $user->credit_cards;
        $credit_cards[$index] = $request->only(['card_number', 'card_holder', 'expiration_date', 'cvv']);
        $credit_cards[$index]['is_default'] = $credit_cards[$index]['is_default'] ?? 0;

        $user->update(['credit_cards' => $credit_cards]);

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated',
                    ['model' => __('control.profile.credit_card.title')])
            ]);
    }

    public function deleteCreditCard(Request $request)
    {
        $index = $request->index;

        $user = User::find(Auth::id());
        $credit_cards = $user->credit_cards;
        unset($credit_cards[$index]);

        $user->update(['credit_cards' => array_values($credit_cards)]);

        return redirect()->back()
            ->with([
                'notification' => __('control.notification_updated',
                    ['model' => __('control.profile.credit_card.title')])
            ]);
    }

    public function deleteNotifications()
    {
        AnnouncementUser::where('user_id', Auth::id())->delete();

        return response()->json(['status' => true]);
    }
}
