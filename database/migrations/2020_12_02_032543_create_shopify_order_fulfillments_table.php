<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyOrderFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_order_fulfillments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('order_id');
            $table->bigInteger('lineitem_id')->nullable();
            $table->bigInteger('variant_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('transaction_id')->comment('cross order redeems.');
            $table->double('price')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('sku')->nullable();
            $table->longText('variant_name')->nullable();
            $table->integer('fulfillable_quantity')->nullable();
            $table->string('fulfillment_status', 32)->nullable();
            $table->string('vendor', 32)->nullable();
            $table->double('tax_price')->nullable()->default(0);
            $table->double('tax_rate')->nullable()->default(0);
            $table->string('status', 32)->nullable();
            $table->string('tracking_number', 256)->nullable();
            $table->bigInteger('fulfillment_id')->nullable();
            $table->double('discount_value')->nullable()->default(0);
            $table->string('redeem_source', 30)->default('');
            $table->string('fulfill_source', 30)->default('');
            $table->timestamps();
            $table->index(['order_id', 'lineitem_id', 'variant_id', 'product_id'], 'shopify_order_fulfillments_index');
            $table->index(['vendor', 'created_at'], 'vendor_idx');
            $table->unique(['fulfillment_id', 'lineitem_id'], 'shopify_order_fulfillments_unq_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopify_order_fulfillments');
    }
}
