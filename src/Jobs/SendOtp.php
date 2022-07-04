<?php

namespace Turahe\Otp\Jobs;

use Illuminate\Support\Facades\Mail;

/**
 * Class SendOtp.
 * @package App\Jobs
 */
class SendOtp
{
    /**
     * @var string
     */
    public $email;
    /**
     * @var
     */
    public $otp;

    /**
     * SendOtp constructor.
     * @param string $email
     * @param $otp
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
