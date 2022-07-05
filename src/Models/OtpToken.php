<?php

namespace Turahe\Otp\Models;

use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{
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
}
