<?php

namespace KodiCMS\Users\database\seeds;

use Illuminate\Database\Seeder;
use KodiCMS\Users\Model\Permission;
use KodiCMS\Users\Model\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        Permission::truncate();

        $roles = [
            [
                'name' => 'login',
                'label' => 'Login privileges, granted after account confirmation.',
            ],
            [
                'name' => 'administrator',
                'label' => 'Administrative user, has access to everything.',
            ],
            [
                'name' => 'developer',
                'label' => '',
            ],
        ];

        foreach ($roles as $data) {
            Role::create($data);
        }
    }
}
