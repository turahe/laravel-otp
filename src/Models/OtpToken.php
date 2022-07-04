<?php

namespace Turahe\Otp\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

/**
 * Turahe\Otp\Models\OtpVerification
 *
 * @property string $id
 * @property string $identity
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $expired
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification expired()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification whereExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpVerification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OtpToken extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'otp_verifications';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

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

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        });
    }
}
