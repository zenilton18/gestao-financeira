<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'centro_custo',
        'produto_id',
        'quantidade',
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'numero_parcela',
        'total_parcelas',
        'grupo_id',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento'  => 'date',
        'quantidade'      => 'integer',
        'valor'           => 'float',
    ];

    /**
     * Escopo para filtrar apenas lançamentos financeiros normais (exclui vendas de produtos do saldo).
     */
    public function scopeApenasFinanceiro($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('centro_custo')
                ->orWhere('centro_custo', '!=', 'venda_produtos');
            })
            ;
    }

    /**
     * Escopo para filtrar apenas vendas de produtos.
     */
    public function scopeApenasVendasProdutos($query)
    {
        return $query->where('centro_custo', 'venda_produtos');
    }
}