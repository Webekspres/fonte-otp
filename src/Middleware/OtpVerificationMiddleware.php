<?php

namespace Webekspres\FonnteOtp\Middleware;

use Closure;
use Webekspres\FonnteOtp\Services\FonnteOtpService;

class OtpVerificationMiddleware
{
    protected $fonnteOtp;

    public function __construct(FonnteOtpService $fonnteOtp)
    {
        $this->fonnteOtp = $fonnteOtp;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $phone = $request->input('phone');
        $otp = $request->input('otp');

        if (!$phone || !$otp) {
            return response()->json([
                'message' => 'Phone number and OTP are required.',
            ], 422);
        }

        $isValid = $this->fonnteOtp->verify($phone, $otp);

        if (!$isValid) {
            return response()->json([
                'message' => 'Invalid OTP.',
            ], 401);
        }

        return $next($request);
    }
}