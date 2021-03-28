<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeiviceLendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_lends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->foreignId('shortTerm_id')->nullable()->references('id')->on('devices_short_terms')->onDelete('cascade');
            $table->foreignId('longTerm_id')->nullable()->references('id')->on('devices_long_terms')->onDelete('cascade');
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
        Schema::dropIfExists('deivice_lends');
    }
}
