<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Stancl\Tenancy\Database\Models\Domain;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = request()->getHost();
        $domainArray = explode('.', $host);

        $domain = Domain::where('domain', $domainArray[0])->firstOrFail()->toArray();

        tenancy()->initialize($domain['tenant_id']);

        if (tenant() === null) {
            abort(403, 'Tenant not found');
        }

        return $next($request);
    }
}
