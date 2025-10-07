<?php

namespace Webekspres\FonnteOtp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class OtpRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $this->throttleKey($request);
        $maxAttempts = Config::get('fonnte-otp.rate_limit.max_attempts', 3);
        $decayMinutes = Config::get('fonnte-otp.rate_limit.decay_minutes', 10);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return Response::json([
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return 'otp-' . sha1($request->input('phone'));
    }
}