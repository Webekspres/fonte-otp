<?php

namespace Webekspres\FonnteOtp\Facades;

use Illuminate\Support\Facades\Facade;
use Webekspres\FonnteOtp\Services\FonnteOtpService;

/**
 * @method static array send(string $phoneNumber, array $variables = [])
 * @method static bool verify(string $phoneNumber, string $otp)
 * 
 * @see \Webekspres\FonnteOtp\Services\FonnteOtpService
 */
class FonnteOtp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fonnte-otp';
    }
}