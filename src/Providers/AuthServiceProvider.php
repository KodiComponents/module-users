<?php

namespace KodiCMS\Users\Providers;

use KodiCMS\Users\ACL;
use KodiCMS\Users\Http\Middleware\RedirectIfAuthenticated;
use KodiCMS\Users\Model\User;
use Illuminate\Routing\Router;
use KodiCMS\Users\Http\Middleware\Authenticate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @param Router                                  $router
     */
    public function boot(GateContract $gate, Router $router)
    {
        $router->middleware('backend.auth', Authenticate::class);
        $router->middleware('backend.guest', RedirectIfAuthenticated::class);

        parent::registerPolicies($gate);

        $this->app['config']->set('auth.model', User::class);

        $this->app->singleton('acl', function () use ($gate) {
            return new ACL(config('permissions', []), $gate);
        });
    }
}
