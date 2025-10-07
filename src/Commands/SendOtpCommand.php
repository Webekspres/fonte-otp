<?php

namespace Webekspres\FonnteOtp\Commands;

use Illuminate\Console\Command;
use Webekspres\FonnteOtp\Services\FonnteOtpService;

class SendOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:send-otp {phone} {--variables=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send OTP to a phone number for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FonnteOtpService $fonnteOtp)
    {
        $phone = $this->argument('phone');
        $variablesOption = $this->option('variables');
        
        // Parse variables if provided
        $variables = [];
        if ($variablesOption) {
            $variables = json_decode($variablesOption, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON format for variables option');
                return 1;
            }
        }

        try {
            $this->info("Sending OTP to {$phone}...");

            $response = $fonnteOtp->send($phone, $variables);

            if ($response['success']) {
                $this->info('OTP sent successfully!');
                $this->line('Response: ' . json_encode($response['data']));
            } else {
                $this->error('Failed to send OTP.');
                $this->line('Error: ' . json_encode($response));
            }
        } catch (\Exception $e) {
            $this->error('Error sending OTP: ' . $e->getMessage());
        }

        return 0;
    }
}