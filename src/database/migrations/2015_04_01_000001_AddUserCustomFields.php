<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUserCustomFields extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('logins')->default(0);
            $table->integer('last_login')->nullable();
            $table->string('locale', 5)->default(config('app.locale'));
            $table->string('avatar', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->drop(['logins', 'last_login', 'locale', 'avatar']);
        });
    }
}
