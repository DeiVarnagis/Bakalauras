<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesLongTermTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices_long_terms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string("serialNumber")->unique();
            $table->integer('amount');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('state')->default(0);
            $table->tinyInteger('action')->default(0);
            $table->string('src')->nullable()->default(null);
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
        Schema::dropIfExists('devices_long_terms');
    }
}
