<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_image', function(Blueprint $table) {
          $table->increments('id');
          $table->string('path');
          $table->string('thumb_path');
          $table->integer('user_id')->unsigned();

          $table->foreign('user_id')->references('id')->on('user');

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
        Schema::dropIfExists('user_image');
    }
}
