<?php

namespace KodiCMS\Users\Providers;

use KodiCMS\Users\Http\Middleware\RedirectIfAuthenticated;
use KodiCMS\Users\Model\Permission;
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
     * {@inheritdoc}
     */
    public function register()
    {
        Permission::register('users', 'user', [
            'list',
            'create',
            'edit',
            'view_permissions',
            'change_roles',
            'change_password',
            'delete',
        ]);

        Permission::register('users', 'role', [
            'list',
            'create',
            'edit',
            'change_permissions',
            'delete',
        ]);
    }

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

        $gate->before(function (User $user, $ability) {
            \Profiler::append('Requested permissions', $ability, 0);

            if ($user->hasRole('administrator')) {
                return true;
            }
        });

        if (\Schema::hasTable('permissions')) {
            // Dynamically register permissions with Laravel's Gate.
            foreach ($this->getPermissions() as $permission) {
                $gate->define($permission->key, function (User $user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }
    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPermissions()
    {
        $permissions = Permission::with('roles')->get();

        Permission::syncPermissions($permissions);
        return $permissions;
    }
}
