<?php

namespace Turahe\Otp\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtp extends Mailable implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The content instance.
     *
     * @var
     */
    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $otp = $this->content->token;

        return $this->markdown('otp::emails.otp')
            ->subject($otp .' '.__('your code verification') . ' - ' . config('app.name', 'Rumah berkat'))
            ->with([
                'otp'  => $otp,
            ]);
    }
}
