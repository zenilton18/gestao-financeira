<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Pedido extends Model
{

    use HasFactory;



    protected $table = 'pedidos';



    protected $fillable = [

        'origem',
'custo_total',
'lucro_bruto',
        'pedido_externo',

        'status',

        'status_marketplace',

        'nome_cliente',

        'usuario_cliente',

        'valor_produtos',

        'valor_frete',

        'valor_desconto',

        'valor_total',

        'taxas_marketplace',

        'valor_repasse',

        'transportadora',

        'codigo_rastreio',

        'endereco_entrega',

        'dados_marketplace',

        'data_pedido'

    ];




    protected $casts = [

        'endereco_entrega'=>'array',

        'dados_marketplace'=>'array',

        'data_pedido'=>'datetime'

    ];



    public function itens()
    {

        return $this->hasMany(
            PedidoItem::class
        );

    }
    


}