<?php

namespace Turahe\Otp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Turahe\Otp\Services\Token generate(string $identity, int $expired = 10)
 * @method static \Turahe\Otp\Services\Token validate(string $identity, string $token)
 */

class Otp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'otp';
    }
}
