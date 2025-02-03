<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccount\BankAccountStoreRequest;
use App\Models\BankAccount;
use App\Models\System\Country;
use App\Services\CountryServices;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BankController extends Controller
{
    public function index()
    {
        return BankAccount::with('country')->where('user_id', auth()->id())->get();
    }

    public function create()
    {
        $countries = CountryServices::get();

        return inertia('Control/BankAccount/Create', compact('countries'));
    }

    public function store(BankAccountStoreRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = isset($request->user_id) ? $request->user_id : auth()->id();

        $bankAccount =  BankAccount::create($data);

        return response()->json([
            'notification' => [
                'account' => $bankAccount,
                'message' => __(
                    'control.notification_created',
                    ['model' => __('control.bank_account.title_singular')]
                )
            ]
        ], Response::HTTP_OK);
    }

    public function edit(BankAccount $bankAccount)
    {
        $countries = CountryServices::get();
        $bankAccount->load('country');

        return inertia('Control/BankAccount/Edit', compact('bankAccount', 'countries'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                'country_id' => ['required', Rule::exists(Country::class, 'id')],
                'name' => 'required|string|max:255',
                'iban' => 'required|string|max:255',
                'swift' => 'nullable|string|max:255',
            ]
        );

        $bankAccount->update($request->all());

        return response()->json($bankAccount, Response::HTTP_OK);
        // return redirect()
        //     ->back()
        //     ->with([
        //         'notification' => __('control.notification_updated',
        //             ['model' => __('control.bank_account.title_singular')])
        //     ]);

    }

    public function delete(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()
            ->back()
            ->with([
                'notification' => __(
                    'control.notification_deleted',
                    ['model' => __('control.bank_account.title_singular')]
                )
            ]);
    }
}
