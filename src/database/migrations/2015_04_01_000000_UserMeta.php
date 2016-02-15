<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserMeta extends Migration
{
    public function up()
    {
        Schema::create('user_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('key', 50)->index();
            $table->text('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_meta');
    }
}
