<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Shopify Order Model
 * @author kayliongmac11air
 *
 */
class ShopifyOrder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shopify_orders';
    
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
        'email',
        'created_at',
        'updated_at',
        'total_price',
        'sub_total',
        'total_tax',
        'currency',
        'total_discounts',
        'order_name',
        'cancelled_at',
        'phone',
        'discount_code',
        'discount_amount',
        'note',
        'source_name',
        'fulfillment_status',
        'country_code',
        'checkout_id',
        'note_attributes',
        'deleted',
        'deleted_at',
        'payment_method',
        'payment_reference',
        'billing_name',
        'billing_zip',
        'selling',
        'tags',
        'financial_status'
    ];
}
