<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('amount');
            $table->foreignId('shortTerm_id')->nullable()->references('id')->on('devices_short_terms')->onDelete('cascade'); 
            $table->foreignId('longTerm_id')->nullable()->references('id')->on('devices_long_terms')->onDelete('cascade');
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
        Schema::dropIfExists('accessories');
    }
}
