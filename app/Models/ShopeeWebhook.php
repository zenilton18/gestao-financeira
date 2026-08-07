<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopeeWebhook extends Model
{
    protected $fillable = [

        'msg_id',
        'order_sn',
        'shop_id',
        'status',
        'code',
        'payload',
        'processed',

    ];


    protected $casts = [

        'payload' => 'array',
        'processed' => 'boolean',

    ];
}