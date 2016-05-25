<?php

namespace KodiCMS\Users\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BackendRedirectIfAuthenticated
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth = Auth::guard('backend');

        if ($auth->check() and $auth->user()->hasRole('login')) {
            return redirect(backend_url());
        }

        return $next($request);
    }
}
