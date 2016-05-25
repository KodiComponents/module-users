<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->rename('backend_users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('backend_users');
    }
}
