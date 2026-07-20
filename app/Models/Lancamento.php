<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    // 💡 Adicione isso para permitir o cadastro dos campos no banco:
    protected $fillable = [
        'categoria_id',
        'descricao',
        'valor',
        'data_emissao'
    ];

    // O seu relacionamento que já estava certinho:
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }
}