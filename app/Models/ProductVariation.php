<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [

        'product_id',

        'shopee_model_id',

        'nome',

        'sku',

        'preco',

        'estoque'
    ];

    protected $casts = [

        'preco' => 'decimal:2'
    ];

    public function produto()
    {
        return $this->belongsTo(Product::class);
    }
}