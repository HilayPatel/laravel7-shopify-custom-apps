<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyOrderLineitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_order_lineitems', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('order_id');
            $table->bigInteger('lineitem_id')->nullable()->unique('shopify_order_lineitems_lineitem_id_pk');
            $table->bigInteger('variant_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->double('price')->nullable();
            $table->integer('quantity')->nullable();
            $table->longText('sku')->nullable();
            $table->longText('variant_name')->nullable();
            $table->integer('fulfillable_quantity')->nullable();
            $table->string('fulfillment_status', 32)->nullable();
            $table->string('vendor', 32)->nullable();
            $table->double('tax_price')->nullable()->default(0);
            $table->double('tax_rate')->nullable()->default(0);
            $table->double('discount_value')->nullable()->default(0);
            $table->timestamps();
            $table->index(['order_id', 'lineitem_id', 'variant_id', 'product_id'], 'shopify_order_lineitems_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopify_order_lineitems');
    }
}
