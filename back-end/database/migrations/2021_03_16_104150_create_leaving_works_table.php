<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavingWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaving_works', function (Blueprint $table) {
        $table->id();
        $table->foreignId('owner_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->tinyInteger('state')->default(0); 
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
        Schema::dropIfExists('leaving_works');
    }
}
