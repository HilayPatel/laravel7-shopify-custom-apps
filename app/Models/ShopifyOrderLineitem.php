<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Shopify Order Lineitem Model
 * @author kayliongmac11air
 *
 */
class ShopifyOrderLineitem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shopify_order_lineitems';
    
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
        'price',
        'quantity',
        'sku',
        'variant_name',
        'fulfillable_quantity',
        'fulfillment_status',
        'vendor',
        'tax_price',
        'tax_rate',
        'discount_value'
    ];
}
