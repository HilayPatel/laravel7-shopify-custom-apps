<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 255);
            $table->string('name', 255);
            $table->decimal('price');
            $table->string('interval', 255)->nullable();
            $table->decimal('capped_amount')->nullable();
            $table->string('terms', 255)->nullable();
            $table->integer('trial_days')->nullable();
            $table->boolean('test')->default(0);
            $table->boolean('on_install')->default(0);
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
        Schema::dropIfExists('plans');
    }
}
