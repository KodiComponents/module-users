<?php

namespace KodiCMS\Users\Providers;

use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use KodiCMS\Users\Http\Middleware\BackendAuthenticate;
use KodiCMS\Users\Http\Middleware\BackendRedirectIfAuthenticated;
use KodiCMS\Users\Model\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * {@inheritdoc}
     * @param Router                                  $router
     */
    public function register()
    {
        /** @var Route $router */
        $router = $this->app['router'];

        $router
            ->middleware('backend.auth', BackendAuthenticate::class)
            ->middleware('backend.guest', BackendRedirectIfAuthenticated::class);

        $this->app->singleton('backend.gate', function ($app) {
            return new Gate($app, function () use ($app) {
                return $app['auth']->guard('backend')->user();
            });
        });
    }

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);
        $this->registerGuard();
    }

    private function registerGuard()
    {
        $config = $this->app['config'];

        $config->set('auth.guards.backend', [
            'driver' => 'session',
            'provider' => 'backend_users'
        ]);

        $config->set('auth.providers.backend_users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);
    }
}
