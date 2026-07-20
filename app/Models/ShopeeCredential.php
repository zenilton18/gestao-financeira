<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShopeeCredential extends Model
{
    protected $fillable = [
        'shop_id',
        'shop_name',
        'access_token',
        'refresh_token',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'is_active',
    ];

    protected $casts = [
        'access_token_expires_at' => 'datetime',
        'refresh_token_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Token de acesso expirado?
     */
    public function accessTokenExpired(): bool
    {
        return now()->greaterThanOrEqualTo($this->access_token_expires_at);
    }

    /**
     * Expira nos próximos minutos?
     */
    public function accessTokenWillExpire(int $minutes = 5): bool
    {
        return now()->addMinutes($minutes)
            ->greaterThanOrEqualTo($this->access_token_expires_at);
    }

    /**
     * Refresh Token expirado?
     */
    public function refreshTokenExpired(): bool
    {
        if (!$this->refresh_token_expires_at) {
            return false;
        }

        return now()->greaterThanOrEqualTo($this->refresh_token_expires_at);
    }
}