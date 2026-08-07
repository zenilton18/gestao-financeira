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
];
}