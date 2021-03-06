<?php

namespace KodiCMS\Users\database\seeds;

use Illuminate\Database\Seeder;
use KodiCMS\Users\Model\Role;
use KodiCMS\Users\Model\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        \DB::table('user_meta')->truncate();
        \DB::table('role_user')->truncate();

        $roles = Role::pluck('id')->all();
        $maxRolesToAttach = count($roles) > 4 ? 4 : count($roles);

        $faker = \Faker\Factory::create();
        $totalUsers = 5;

        $user = User::create([
            'email' => 'admin@site.com',
            'password' => 'password',
            'name' => 'admin',
            'locale' => 'ru',
        ]);

        $user->roles()->sync($roles);

        $user = User::create([
            'email' => 'admin_en@site.com',
            'password' => 'password',
            'name' => 'admin_en',
            'locale' => 'en',
        ]);

        $user->roles()->sync([1, 2, 3]);

        $usedEmails = $usedUsernames = [];

        for ($i = 0; $i < $totalUsers; $i++) {
            do {
                $email = strtolower($faker->email);
            } while (in_array($email, $usedEmails));

            $usedEmails[] = $email;

            do {
                $username = strtolower($faker->userName);
            } while (in_array($username, $usedUsernames));

            $usedUsernames[] = $username;

            $user = User::create([
                'email' => $email,
                'password' => 'password',
                'name' => $username,
                'locale' => $faker->randomElement(['ru', 'en']),
            ]);

            $user->roles()->attach($faker->randomElements($roles, rand(1, $maxRolesToAttach)));
        }
    }
}
