<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagamentoParcela extends Model
{
    protected $table = 'pagamentoparcela';
    protected $fillable = [
        'parcela_id',
        'valor_pago',
        'data_pagamento'
    ];

    // O seu relacionamento que já estava certinho:
    public function parcela()
    {
        return $this->belongsTo(Parcela::class);
    }
}

 