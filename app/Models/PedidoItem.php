<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PedidoItem extends Model
{

    use HasFactory;



    protected $fillable = [

        'pedido_id',

        'marketplace_item_id',

        'marketplace_model_id',
        'product_variation_id',

        'nome_produto',

        'sku_marketplace',

        'variacao',

        'quantidade',

        'preco_unitario',

        'valor_total',

        'produto_id',

        'custo',

        'lucro',

        'dados_marketplace'

    ];



    protected $casts = [

        'dados_marketplace' => 'array'

    ];



    public function pedido()
    {

        return $this->belongsTo(
            Pedido::class
        );

    }



    public function produto()
    {

        return $this->belongsTo(
            Product::class
        );

        
    }
    public function productVariation()
{
    return $this->belongsTo(
        ProductVariation::class
    );
}


}