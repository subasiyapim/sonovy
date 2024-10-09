<?php

namespace App\Http\Controllers\Control;

use App\Enums\PaymentProcessTypeEnum;
use App\Enums\UserGenderEnum;
use App\Enums\UserTitleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCompetencyRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\Panel\UserResource;
use App\Models\Artist;
use App\Models\BankAccount;
use App\Models\Product;
use App\Models\Country;
use App\Models\Label;
use App\Models\User;
use App\Services\CountryServices;
use App\Services\EarningService;
use App\Services\LocaleService;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\TimezoneService;
use App\Services\UserServices;
use App\Services\UserVerifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = Auth::user();
        $hasAdmin = $user->roles()->pluck('code')->contains('admin');
        $query = User::with('roles', 'sub_users', 'parent_user', 'country', 'state', 'city');

        // If 'sub_users' is present in the request, get users with sub_user IDs
        $subUserId = $request->get('sub_users');

        if ($hasAdmin) {
            // Admin: get all users or filter by sub_user ID
            $users = $subUserId ? $query->where('parent_id', $subUserId)->advancedFilter() : $query->advancedFilter();
        } else {
            // Non-admin: filter by parent_id
            $users = $query->where('parent_id', $subUserId ?? $user->id)->advancedFilter();
        }

        // Resource kullanarak kullanıcıları döndür
        return inertia('Control/Users/Index', [
            'users' => UserResource::collection($users),
            'roles' => RoleService::getRolesFromInputFormat()
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = CountryServices::get();

        $permissions = PermissionService::getGroupedPermissions();

        $roles = RoleService::getRolesFromInputFormat();

        return inertia('Control/Users/Create', compact('countries', 'permissions', 'roles'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->except(['role_id', 'password', 'artists', 'labels', 'platforms', 'commission_rate']);
        $data['parent_id'] = auth()->id();
        $data['password'] = bcrypt($request->password);
        $data['commission_rate'] = $request->commission_rate ? preg_replace('/\s+/', '',
            $request->commission_rate) : null;

        $user = User::create($data);

        $user->roles()->sync($request->role_id);

//        if ($request->access_all_artists == 0 && $request->artists) {
//            $user->permittedArtists()->sync($request->artists);
//        }
//        if ($request->access_all_labels == 0 && $request->labels) {
//            $user->permittedLabels()->sync($request->labels);
//        }
//        if ($request->access_all_platforms == 0 && $request->platforms) {
//            $user->permittedPlatforms()->sync($request->platforms);
//        }

        UserVerifyService::generate($user);

        return to_route('dashboard.users.index')
            ->with(['notification' => __('panel.notification_created', ['model' => __('panel.user.title_singular')])]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->loadMissing('roles', 'country', 'state', 'city', 'bankAccounts', 'site', 'birthPlace', 'parent_user',
            'sub_users', 'payments', 'orders');

        $genders = UserGenderEnum::getTitles();
        $titles = UserTitleEnum::getTitles();
        $payments = $user->payments()->whereNot('process_type',
            PaymentProcessTypeEnum::APPROVED_ADVANCE->value)->with('account')->advancedFilter();
        $advances = $user->payments()->where('process_type',
            PaymentProcessTypeEnum::APPROVED_ADVANCE->value)->with('account')->advancedFilter();
        $orders = $user->orders()->advancedFilter();
        $balance = EarningService::balance($user->id);
        $earnings = EarningService::earningsOfTheUser($user);


        $sub_users = User::where('parent_id', '=', $user->id)->get();
        $labels = Label::with('country')->where('added_by', '=', $user->i)->get();
        $artists = Artist::where('added_by', '=', $user->i)->get();
        $products = Product::with('artists', 'label', 'publishedCountries', 'songs')->where('added_by', '=',
            $user->i)->get();


        return inertia('Control/Users/Show',
            compact('user', 'genders', 'titles', 'payments', 'advances', 'orders', 'balance', 'earnings', 'sub_users',
                'labels', 'artists', 'broadcasts'));

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

        return inertia('Control/Users/Edit',
            compact('user', 'counties', 'permissions', 'roles', 'timezones', 'localize_list'));


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->except(['role_id', 'password', 'artists', 'labels', 'platforms', 'commission_rate']);
        $data['commission_rate'] = $request->commission_rate ? preg_replace('/\s+/', '',
            $request->commission_rate) : null;

        if ($request->validated()['password']) {
            $data['password'] = bcrypt($request->password);
        }

//        if ($request->access_all_artists == 0 && $request->artists) {
//            $user->permittedArtists()->sync($request->artists);
//        }
//        if ($request->access_all_labels == 0 && $request->labels) {
//            $user->permittedLabels()->sync($request->labels);
//        }
//        if ($request->access_all_platforms == 0 && $request->platforms) {
//            $user->permittedPlatforms()->sync($request->platforms);
//        }

        $user->roles()->sync($request->role_id);
        $user->update($data);

        return to_route('dashboard.users.index')
            ->with(['notification' => __('panel.notification_updated', ['model' => __('panel.user.title_singular')])]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();


        return to_route('dashboard.users.index')
            ->with(['notification' => __('panel.notification_deleted', ['model' => __('panel.user.title_singular')])]);
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

}
