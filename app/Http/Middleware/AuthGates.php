<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;


class AuthGates
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $permissions = $this->getUserPermissions($user);

            $this->initializeGates($permissions);
        }

        return $next($request);
    }

    /**
     * Kullanıcının tüm izinlerini al.
     */
    protected function getUserPermissions($user): array
    {
        return Cache::remember("user_permissions_{$user->id}", now()->addMinutes(10), function () use ($user) {
            return $user->roles()
                ->with('permissions:id,code')
                ->get()
                ->pluck('permissions.*.code')
                ->flatten()
                ->unique()
                ->toArray();
        });
    }


    protected function initializeGates(array $permissions): void
    {
        foreach ($permissions as $permission) {
            Gate::define($permission, fn(User $user) => true);
        }
    }

}
