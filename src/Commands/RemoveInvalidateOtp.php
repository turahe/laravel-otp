<?php

namespace Turahe\Otp\Commands;

use Illuminate\Console\Command;
use Turahe\Otp\Models\OtpToken;

class RemoveInvalidateOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'otp:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove invalidate otp';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (OtpToken::expired()->get() as $otp) {
            $otp->delete();
        }

        return 0;
    }
}
