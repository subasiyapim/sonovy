<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNoSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $baseDomain = env('BASE_URL');
        
        if ($host === $baseDomain) {
            if (env('APP_ENV') === 'local') {
                return redirect()->to('http://app.'.env('BASE_URL').$request->getRequestUri());
            }

            return redirect()->to('https://app.'.env('BASE_URL').$request->getRequestUri());
        }

        return $next($request);
    }
}
