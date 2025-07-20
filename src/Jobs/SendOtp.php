<?php

namespace Turahe\Otp\Jobs;

use Illuminate\Support\Facades\Mail;

/**
 * Class SendOtp.
 */
class SendOtp
{
    /**
     * @var string
     */
    public $email;

    public $otp;

    /**
     * SendOtp constructor.
     */
    public function __construct(string $email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new \Turahe\Otp\Mail\SendOtp($this->otp));
    }
}
