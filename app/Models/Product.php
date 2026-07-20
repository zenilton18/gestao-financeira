<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [

        'shop_id',

        'shopee_item_id',

        'nome',

        'sku',

        'marca',

        'categoria_id',

        'imagem',

        'status',

        'possui_variacao',

        'estoque_total',

        'peso',

        'comprimento',

        'largura',

        'altura',

        'preco_custo',

        'preco_venda',

        'codigo_barras',

        'codigo_interno',

        'estoque_minimo',

        'localizacao',

        'observacoes',
    ];

    protected $casts = [

        'possui_variacao' => 'boolean',

        'preco_custo' => 'decimal:2',

        'preco_venda' => 'decimal:2',
    ];

    public function variacoes()
    {
        return $this->hasMany(ProductVariation::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}