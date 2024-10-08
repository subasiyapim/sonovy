<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Requests\Role\RoleUpdateRequest;
use App\Models\Role;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('role_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::with('translations')->advancedFilter();

        return inertia('Control/Roles/Index', compact('roles'));
    }

    public function create()
    {
        $permissions = PermissionService::getPermissionsFromInputFormat();
        $groupedPermissions = PermissionService::getGroupedPermissions();

        return inertia('Control/Roles/Create', compact('permissions', 'groupedPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request)
    {
        $data = $request->except('translations', 'permissions');

        foreach ($request->translations as $key => $value) {
            $data[$key] = $value;
        }
        $role = Role::create($data);

        $role->permissions()->sync($request->permissions);

        return to_route('dashboard.roles.index')
            ->with(['notification' => __('panel.notification_created', ['model' => __('panel.role.title_singular')])]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->load('permissions');

        return to_route('dashboard.roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = PermissionService::getPermissionsFromInputFormat();
        $groupedPermissions = PermissionService::getGroupedPermissions();
        $role->load('permissions', 'translations');

        return inertia('Control/Roles/Edit', compact('role', 'permissions', 'groupedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $data = $request->except('translations', 'permissions');

        foreach ($request->translations as $key => $value) {
            $data[$key] = $value;
        }

        $role->update($data);

        $role->permissions()->sync($request->permissions);

        return to_route('dashboard.roles.index')
            ->with(['notification' => __('panel.notification_updated', ['model' => __('panel.role.title_singular')])]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($role->id !== 1) {
            $role->delete();
        }

        return to_route('dashboard.roles.index')
            ->with(['notification' => __('panel.notification_deleted', ['model' => __('panel.role.title_singular')])]);

    }
}
