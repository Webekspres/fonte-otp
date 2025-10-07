<?php

namespace Webekspres\FonnteOtp\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpSent
{
    use Dispatchable, SerializesModels;

    public string $phoneNumber;
    public string $otp;

    public function __construct(string $phoneNumber, string $otp)
    {
        $this->phoneNumber = $phoneNumber;
        $this->otp = $otp;
    }
}