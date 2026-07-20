<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{

    protected $fillable = [

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
        'order_date'=>'datetime'
    ];



    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}