<?php

namespace Turahe\Otp\Services;

use Carbon\Carbon;
use Turahe\Otp\Models\OtpToken as Model;

class Otp
{
    /**
     * @param string $identity
     * @param int $expired
     * @return Model
     */
    public function generate(string $identity, int $expired = 10): Model
    {
        Model::where('identity', $identity)->delete();

        $token = mt_rand(100000, 999999);

        return Model::create([
            'identity' => $identity,
            'token'    => $token,
            'expired'  => Carbon::now()->addMinutes($expired)
        ]);
    }

    /**
     * @param string $identity
     * @param string $token
     * @return bool
     */
    public function validate(string $identity, string $token): bool
    {
        $otp = Model::where(['identity' => $identity, 'token' => $token])
            ->where('expired', '>', Carbon::now())
            ->first();

        if ($otp) {
            return $otp->delete();
        }

        return false;
    }
}
