<?php

namespace KodiCMS\Users\Providers;

use Event;
use KodiCMS\Users\Model\User;
use KodiCMS\Users\Model\Role;
use KodiCMS\Support\ServiceProvider;
use KodiCMS\Users\Facades\Reflinks;
use KodiCMS\Users\Observers\RoleObserver;
use KodiCMS\Users\Observers\UserObserver;
use KodiCMS\Users\Reflinks\ReflinksBroker;
use KodiCMS\Users\Reflinks\ReflinkTokenRepository;
use KodiCMS\Users\Console\Commands\DeleteExpiredReflinksCommand;

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

        $this->registerNavigation();
    }

    public function register()
    {
        config()->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model'  => \KodiCMS\Users\Model\User::class,
        ]);

        $this->registerAliases([
            'Reflinks' => Reflinks::class,
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

            return new ReflinksBroker($tokens);
        });
    }

    /**
     * Register the token repository implementation.
     * @return void
     */
    protected function registerTokenRepository()
    {
        $this->app->singleton('reflink.tokens', function ($app) {
            $key = $app['config']['app.key'];
            $expire = 60;

            return new ReflinkTokenRepository($key, $expire);
        });
    }

    private function registerNavigation()
    {
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
}
