<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MercadoLivreConnection extends Model
{
    use HasFactory;

    protected $table = 'mercado_livre_connections';

    protected $fillable = [
        'user_id',
        'mercadolivre_user_id',
        'nickname',
        'access_token',
        'refresh_token',
        'expires_at',
        'token_type',
        'scope',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Usuário do MGF dono da conexão.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o Access Token ainda é válido.
     *
     * Consideramos expirado alguns minutos antes
     * para evitar chamadas com token prestes a vencer.
     */
    public function tokenExpired(): bool
    {
        if (!$this->expires_at) {
            return true;
        }

        return now()->addMinutes(5)->greaterThanOrEqualTo(
            $this->expires_at
        );
    }
}