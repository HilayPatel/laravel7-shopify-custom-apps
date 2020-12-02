<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_orders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('order_id')->unique('shopify_orders_order_id_pk');
            $table->string('email', 255)->nullable()->index('email_idx');
            $table->double('total_price')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('total_tax')->nullable()->default(0);
            $table->string('currency', 6)->nullable();
            $table->double('total_discounts')->nullable()->default(0);
            $table->string('order_name', 128)->nullable()->comment('#100001');
            $table->dateTime('cancelled_at')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('discount_code', 128)->nullable();
            $table->double('discount_amount')->nullable()->default(0);
            $table->longText('note')->nullable();
            $table->string('source_name', 64)->nullable();
            $table->string('fulfillment_status', 32)->nullable();
            $table->string('country_code', 16)->nullable();
            $table->string('checkout_id', 32)->nullable();
            $table->text('note_attributes')->nullable();
            $table->smallInteger('deleted')->nullable();
            $table->softDeletes()->comment('1:internal delete, 2:shopify delete');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->string('billing_name', 150)->nullable();
            $table->string('billing_zip', 20)->nullable();
            $table->string('selling', 100)->nullable();
            $table->text('tags')->nullable();
            $table->string('financial_status', 32)->nullable();
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
        Schema::dropIfExists('shopify_orders');
    }
}
