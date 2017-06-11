<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wish', function(Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('reference');
            $table->integer('price');
            $table->date('date');
            $table->integer('list_id')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned();

            $table->foreign('list_id')->references('id')->on('list');
            $table->foreign('location_id')->references('id')->on('location');
            $table->foreign('category_id')->references('id')->on('category');

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
        Schema::dropIfExists('wish');
    }
}
