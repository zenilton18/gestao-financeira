<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{

    protected $fillable = [
        'payment_method',
        'shipping_fee',
        'discount_amount',
        'tracking_number',
        'shipping_carrier',
        'shipping_address',

        'shopee_order_id',
        'status',
        'order_date',

        'total_amount',
        'shopee_commission',
        'shopee_fee',

        'product_cost',
        'profit',
        'margin_percent',

        'buyer_username',

        'raw_data'

    ];


    protected $casts = [
        'raw_data'=>'array',
        'order_date'=>'datetime',
        'shipping_address'=>'array'
    ];



    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}