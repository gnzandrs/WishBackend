<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WishStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wish_status', function(Blueprint $table)
        {
            $table->integer('wish_id')->unsigned();
            $table->foreign('wish_id')->references('id')->on('wish');
            $table->integer('user_taken')->unsigned()->nullable();
            $table->foreign('user_taken')->references('id')->on('user');
            $table->integer('status');

            $table->date('date')->nullable();

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
        Schema::dropIfExists('wish_status');
    }
}
