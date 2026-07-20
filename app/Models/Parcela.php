<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    protected $fillable = [
        'lancamento_id',
        'situacao_id',
        'numero_parcela',
        'valor',
        'data_vencimento'
    ];

    // O seu relacionamento que já estava certinho:
    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class);
    }
    public function pagamento()
    {
        return $this->hasMany(PagamentoParcela::class);
    }
}
