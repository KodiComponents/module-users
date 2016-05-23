<?php

namespace KodiCMS\Users\Http\Middleware;

use Lang;
use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class BackendAuthenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth Services
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guest()) {

            return $this->acl->denyAccess(trans('users::core.messages.auth.unauthorized'), true);
        }

        if (! auth()->user()->hasRole('login')) {
            auth()->logout();
        }

        $locale = auth()->user()->getLocale();
        Carbon::setLocale($locale);
        Lang::setLocale($locale);

        return $next($request);
    }
}
