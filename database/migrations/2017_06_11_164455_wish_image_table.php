<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WishImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wish_image', function(Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->string('thumb_path');
            $table->integer('wish_id')->unsigned();
            $table->foreign('wish_id')->references('id')->on('wish');

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
        Schema::dropIfExists('wish_image');
    }
}
