<?php

namespace KodiCMS\Users\Providers;

use Event;
use KodiCMS\Support\ServiceProvider;
use KodiCMS\Users\Console\Commands\DeleteExpiredReflinksCommand;
use KodiCMS\Users\Model\Permission;
use KodiCMS\Users\Model\Role;
use KodiCMS\Users\Model\User;
use KodiCMS\Users\Observers\RoleObserver;
use KodiCMS\Users\Observers\UserObserver;

class ModuleServiceProvider extends ServiceProvider
{

    public function boot()
    {
        User::observe(new UserObserver);
        Role::observe(new RoleObserver);

        Event::listen('view.navbar.right.after', function () {
            echo view('users::parts.navbar')->render();
        });

        Event::listen('view.menu', function ($navigation) {
            echo view('users::parts.navigation')->render();
        }, 999);

        $this->registerPermissions();
    }

    public function register()
    {
        $this->registerAliases([
            'Reflinks' => \KodiCMS\Users\Facades\Reflinks::class,
            'BackendGate' => \KodiCMS\Users\Facades\BackendGate::class,
        ]);

        $this->registerReflinksBroker();
        $this->registerTokenRepository();
        $this->registerConsoleCommand(DeleteExpiredReflinksCommand::class);
    }

    /**
     * Register the reflink broker instance.
     *
     * @return void
     */
    protected function registerReflinksBroker()
    {
        $this->app->singleton('reflinks', function ($app) {
            $tokens = $app['reflink.tokens'];

            return new \KodiCMS\Users\Reflinks\ReflinksBroker($tokens);
        });
    }

    /**
     * Register the token repository implementation.
     * @return void
     */
    protected function registerTokenRepository()
    {
        $this->app->singleton('reflink.tokens', function ($app) {
            $key    = $app['config']['app.key'];
            $expire = 60;

            return new \KodiCMS\Users\Reflinks\ReflinkTokenRepository($key, $expire);
        });
    }

    public function contextBackend()
    {
        $this->registerGatePermissions();
        $this->extendBladeTags();

        $navigation = \Navigation::getPages()->findById('system');

        $navigation->setFromArray([
            [
                'id' => 'users',
                'title' => 'users::core.title.list',
                'url' => route('backend.user.list'),
                'permissions' => 'users.index',
                'priority' => 200,
                'icon' => 'user',
            ],
            [
                'id' => 'roles',
                'title' => 'users::role.title.list',
                'url' => route('backend.role.list'),
                'permissions' => 'roles.index',
                'priority' => 300,
                'icon' => 'group',
            ],
        ]);
    }

    private function registerPermissions()
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

    private function registerGatePermissions()
    {
        $gate = app('backend.gate');

        $gate->before(function (User $user, $ability) {
            \Profiler::append('Requested permissions', $ability, 0);

            if ($user->hasRole('administrator')) {
                return true;
            }
        });

        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            $gate->define($permission->key, function (User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
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

    private function extendBladeTags()
    {
        \Blade::directive('can', function ($expression) {
            return "<?php if (app('backend.gate')->check{$expression}): ?>";
        });

        \Blade::directive('elsecan', function ($expression) {
            return "<?php elseif (app('backend.gate')->check{$expression}): ?>";
        });

        \Blade::directive('cannot', function ($expression) {
            return "<?php if (app('backend.gate')->denies{$expression}): ?>";
        });

        \Blade::directive('elsecannot', function ($expression) {
            return "<?php elseif (app('backend.gate')->denies{$expression}): ?>";
        });
    }
}
