<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderItem extends Model
{

    protected $fillable=[

        'order_id',
        'shopee_item_id',
        'product_name',
        'quantity',
        'price',
        'product_id',
        'cost',
        'profit',
        'raw_data'

    ];


    protected $casts=[
        'raw_data'=>'array'
    ];



    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    

}