<?php

namespace App\Http\Controllers\Control;

use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCompetencyRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserShowResource;
use App\Models\BankAccount;
use App\Models\Country;
use App\Models\Label;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\CountryServices;
use App\Services\EarningService;
use App\Services\LocaleService;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\TimezoneService;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validator = Validator::make($request->all(), [
            'status' => ['nullable', Rule::enum(UserStatusEnum::class)],
            'role' => ['nullable', 'exists:roles,id'],
            'period' => ['nullable', 'in:[day,week,month,year]'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('control.catalog.products.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();


        $user = Auth::user();
        $hasAdmin = $user->roles()->pluck('code')->contains('admin');
        $query = User::with('roles', 'parent', 'children', 'country', 'district', 'city');

        $children = $request->get('children');

        if ($hasAdmin) {
            $users = $children ? $query->where('parent_id', $children)->advancedFilter() : $query->advancedFilter();
        } else {
            $users = $query->where('parent_id', $children ?? $user->id)->advancedFilter();
        }
        $countryCodes = CountryServices::getCountryPhoneCodes();

        $statistics = [
            'users' => UserServices::statistics('users', $validated['period'] ?? 'month'),
            'active_users' => UserServices::statistics('active_users', $validated['period'] ?? 'month'),
        ];

        return inertia('Control/Users/Index', [
            'users' => UserResource::collection($users)->resource,
            'roles' => RoleService::getRolesFromInputFormat(),
            'statuses' => UserStatusEnum::getTitles(),
            'statistics' => $statistics,
            'countryCodes' => $countryCodes
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $countries = getDataFromInputFormat(CountryServices::get(), 'id', 'name', 'emoji');
        $languages = getDataFromInputFormat(CountryServices::get(), 'id', 'language', 'emoji');

        return inertia('Control/Users/Create', compact('countries', 'languages'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->except(['password', 'commission_rate']);

        $data['password'] = bcrypt($request->password);
        $data['commission_rate'] = $request->commission_rate
            ? preg_replace('/\s+/', '', $request->commission_rate)
            : null;

        try {
            $user = User::create($data);
            $role_id = Role::where('code', 'user')->first()->id;
            $user->roles()->sync($role_id);
        } catch (\Exception $e) {

            return to_route('dashboard.users.index')
                ->withErrors([
                    'notification' => __('control.notification_error'.': '.$e->getMessage())
                ]);
        }

        return to_route('dashboard.users.index')
            ->with([
                'notification' => __('control.notification_created', ['model' => __('control.user.title_singular')])
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!session('admin_id')) {
            abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $languages = getDataFromInputFormat(CountryServices::get(), 'id', 'language', 'emoji');

        $user->load('roles', 'country', 'city', 'district', 'parent', 'children');

        $tabs = [
            'pricing',
            'contracts',
            'balances',
            'invoices',
            'activities',
            'flags',
            'relations',
            'authorisations',

        ];

        $tab = request()->has('slug') ? request()->input('slug') : 'profile';;

        $response = new UserShowResource($user, $tab);
        $countryCodes = CountryServices::getCountryPhoneCodes();
        //dd($response->resolve());
        return inertia(
            'Control/Users/Show',
            [
                'user' => $response->resolve(),
                'tab' => $tab,
                'tabs' => $tabs,
                'countries' => $countries,
                'languages' => $languages,
                'countryCodes' => $countryCodes
            ]
        );
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $counties = CountryServices::get();
        $permissions = PermissionService::getGroupedPermissions();
        $roles = RoleService::getRolesFromInputFormat();
        $timezones = TimezoneService::getFromInputFormat();
        $localize_list = LocaleService::getLocalizationListFromInputFormat();
        $user['bank_accounts'] = BankAccount::with('country')->where('user_id', $user->id)->get();
        $user['earnings'] = EarningService::earningsOfTheUser($user);


        $user->load([
            'roles',
            'country',
            'state',
            'city',
            'bankAccounts.country',
            'site',
            'competency'
        ]);

        if ($user->phone) {
            $country = Country::find($user->country_id ?? 228);
            $user->phone = "+".$country->phone_code.$user->phone;
        }

        return inertia(
            'Control/Users/Edit',
            compact('user', 'counties', 'permissions', 'roles', 'timezones', 'localize_list')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->except(['password', 'artists', 'labels', 'platforms', 'commission_rate']);
        $data['commission_rate'] = $request->commission_rate ? preg_replace(
            '/\s+/',
            '',
            $request->commission_rate
        ) : null;

        if ($request->validated()['password']) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return to_route('control.user-management.users.index')
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();


        return to_route('dashboard.users.index')
            ->with([
                'notification' => __('control.notification_deleted', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function search(Request $request)
    {

        $searchedUsers = UserServices::search($request->search);

        return response()->json($searchedUsers, Response::HTTP_OK);
    }

    public function competency(UserCompetencyRequest $request, User $user)
    {
        $user->competency()->delete();
        $user->competency()->create($request->validated());
    }

    public function toggleStatus(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            $flags = $user->flags ?? [];

            $status = $user->status == UserStatusEnum::ACTIVE
                ? UserStatusEnum::PENDING_APPROVAL
                : UserStatusEnum::ACTIVE;

            $flag = [
                'created_at' => now()->format('d-m-Y H:i'),
                'status' => $status->value,
                'reason' => $request->reason,
                'created_by' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                ],
            ];

            array_unshift($flags, $flag);

            $user->update(
                [
                    'status' => $status->value,
                    'flags' => $flags,
                ]
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return back()
                ->withErrors([
                    'notification' => __('control.notification_error'.': '.$e->getMessage())
                ]);
        }

        return back()
            ->with([
                'notification' => __('control.notification_updated', ['model' => __('control.user.title_singular')])
            ]);
    }

    public function switchToUser(Request $request)
    {
        abort_if(Gate::denies('user_switch'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        session(['admin_id' => auth()->id()]);

        if (!session()->has('admin_id')) {
            abort(500, 'Oturum saklanamadı.');
        }

        Auth::loginUsingId($request->user_id);

        return redirect()->back()->with('success', 'Kullanıcı moduna geçildi.');
    }

    public function switchBackToAdmin()
    {
        $adminId = session('admin_id');

        abort_if(!$adminId, Response::HTTP_FORBIDDEN, 'Yönetici oturumu bulunamadı.');

        Auth::loginUsingId($adminId);
        session()->forget('admin_id');

        return redirect()->back()->with('success', 'Yönetici moduna geri dönüldü.');
    }

    public function assignToProducts(Request $request, User $user)
    {
        $request->validate([
            'products' => 'required|array',
        ]);

        $assignedProducts = Product::whereIn('id', $request->products)
            ->get()
            ?->each(function ($product) use ($user) {
                $product->update(['created_by' => $user->id]);
            });
        return response()->json([
            "success" => true,
            "products" => $assignedProducts,
            "message" => 'Yayınlar başarıyla atanmıştır.'
        ]);
    }

    public function assignToLabels(Request $request, User $user)
    {
        $request->validate([
            'labels' => 'required|array',
        ]);

        $assignedLabels = Label::whereIn('id', $request->labels)
            ->get()
            ?->each(function ($label) use ($user) {
                $label->update(['created_by' => $user->id]);
            });
        return response()->json([
            "success" => true,
            "labels" => $assignedLabels,
            "message" => 'Şirketler başarıyla atanmıştır.'
        ]);
    }

    public function addToChildren(Request $request, User $user)
    {
        $request->validate([
            'children' => 'required|array',
        ]);

        $assingedUsers = User::whereIn('id', $request->children)
            ->get()
            ?->each(function ($child) use ($user) {
                $child->update(['parent_id' => $user->id]);
            });
        return response()->json([
            "success" => true,
            "user" => $assingedUsers,
            "message" => 'Alt kullanıcılar başarıyla atanmıştır.'
        ]);
        // return redirect()->back()->with('success', 'Alt kullanıcılar başarıyla atanmıştır.');
    }

    public function assignToCommissionRate(Request $request, User $user)
    {
        $request->validate([
            'commission_rate' => 'required|numeric',
        ]);

        $user->update(['commission_rate' => $request->commission_rate]);

        return redirect()->back()->with('success', 'Komisyon oranı başarıyla atanmıştır.');
    }

    public function assignToPaymentThreshold(Request $request, User $user)
    {
        $request->validate([
            'payment_threshold' => 'required|numeric',
        ]);

        $user->update(['payment_threshold' => $request->payment_threshold]);

        return redirect()->back()->with('success', 'Ödeme eşiği başarıyla atanmıştır.');
    }

    public function togglePermissions(Request $request, User $user)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,id',
        ]);

        $user->permissions()->toggle($request->permission);

        return redirect()->back()->with('success', 'İzinler başarıyla güncellenmiştir.');

    }
}
