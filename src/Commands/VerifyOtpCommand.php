<?php

namespace Webekspres\FonnteOtp\Commands;

use Illuminate\Console\Command;
use Webekspres\FonnteOtp\Services\FonnteOtpService;

class VerifyOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:verify-otp {phone} {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify OTP for a phone number';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FonnteOtpService $fonnteOtp)
    {
        $phone = $this->argument('phone');
        $code = $this->argument('code');

        try {
            $this->info("Verifying OTP for {$phone}...");

            $isValid = $fonnteOtp->verify($phone, $code);

            if ($isValid) {
                $this->info('OTP verified successfully!');
            } else {
                $this->error('Invalid OTP.');
            }
        } catch (\Exception $e) {
            $this->error('Error verifying OTP: ' . $e->getMessage());
        }

        return 0;
    }
}