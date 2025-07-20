<?php

namespace Turahe\Otp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{
    use HasFactory;

    protected $table = 'otp_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identity',
        'token',
        'expired',
    ];

    public function scopeExpired($query)
    {
        return $query->where('expired', '<', now());
    }
}
