<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('charge_id');
            $table->boolean('test')->default(0);
            $table->string('status', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('terms', 255)->nullable();
            $table->string('type', 255);
            $table->decimal('price');
            $table->string('interval', 255)->nullable();
            $table->decimal('capped_amount')->nullable();
            $table->integer('trial_days')->nullable();
            $table->timestamp('billing_on')->nullable();
            $table->timestamp('activated_on')->nullable();
            $table->timestamp('trial_ends_on')->nullable();
            $table->timestamp('cancelled_on')->nullable();
            $table->timestamp('expires_on')->nullable();
            $table->unsignedInteger('plan_id')->nullable()->index('charges_plan_id_foreign');
            $table->string('description', 255)->nullable();
            $table->bigInteger('reference_charge')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('user_id')->index('charges_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges');
    }
}
