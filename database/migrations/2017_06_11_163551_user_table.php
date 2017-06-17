<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
          $table->increments('id');
          $table->string('username')->unique();
          $table->string('name');
          $table->string('lastname');
          $table->string('email')->unique();
          $table->enum('type', ['user' ,'admin']);
          $table->string('genre');
          $table->boolean('active')->default(true);
          $table->string('password');
          $table->string('slug')->nullable();
                $table->integer('city_id')->unsigned();
                $table->foreign('city_id')->references('id')->on('city');
          $table->rememberToken();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
