<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Shopify Order Fulfillment Model
 * @author kayliongmac11air
 *
 */
class ShopifyOrderFulfillment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shopify_order_fulfillments';
    
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
    
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    //protected $dateFormat = 'U';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'lineitem_id',
        'variant_id',
        'product_id',
        'transaction_id',
        'price',
        'quantity',
        'sku',
        'variant_name',
        'fulfillable_quantity',
        'fulfillment_status',
        'vendor',
        'tax_price',
        'tax_rate',
        'created_at',
        'updated_at',
        'status',
        'tracking_number',
        'fulfillment_id',
        'discount_value',
        'redeem_source',
        'fulfill_source'
    ];
}
