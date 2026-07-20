<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Token extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'empresa_id',
        'token',
        'device_name',
        'ip_address',
        'expires_at',
        'last_used_at',
        'revoked'
    ];


    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked' => 'boolean'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }


}