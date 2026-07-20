<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'descricao',
        'operacao'
    ];
    
    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class);
    }
}
