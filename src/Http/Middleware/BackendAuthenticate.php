<?php

namespace KodiCMS\Users\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Lang;

class BackendAuthenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth = Auth::guard('backend');

        if ($auth->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('backend.auth.login'));
        }

        if (! $auth->user()->hasRole('login')) {
            $auth->logout();
        }

        $locale = $auth->user()->getLocale();

        Carbon::setLocale($locale);
        Lang::setLocale($locale);

        return $next($request);
    }
}
