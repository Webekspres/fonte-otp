<?php

namespace Webekspres\FonnteOtp\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Webekspres\FonnteOtp\Models\OtpCode;
use Webekspres\FonnteOtp\Events\OtpGenerated;
use Webekspres\FonnteOtp\Events\OtpSent;
use Webekspres\FonnteOtp\Events\OtpVerified;
use Webekspres\FonnteOtp\Exceptions\InvalidPhoneNumberException;
use Webekspres\FonnteOtp\Exceptions\OtpSendingFailedException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class FonnteOtpService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $token;
    protected int $expiryMinutes;
    protected string $messageTemplate;
    protected array $messageVariables;
    protected int $maxRetries;
    protected int $retryDelay;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = Config::get('fonnte-otp.base_url');
        $this->token = Config::get('fonnte-otp.token');
        $this->expiryMinutes = Config::get('fonnte-otp.otp_expiry', 5);
        $this->messageTemplate = Config::get('fonnte-otp.message_template', 'Kode OTP Anda adalah {code}');
        $this->messageVariables = Config::get('fonnte-otp.message_variables', []);
        $this->maxRetries = Config::get('fonnte-otp.max_retries', 3);
        $this->retryDelay = Config::get('fonnte-otp.retry_delay', 1000); // in milliseconds
    }

    /**
     * Send OTP to a phone number
     *
     * @param string $phoneNumber
     * @param array $variables Optional variables to customize the message
     * @return array
     * @throws InvalidPhoneNumberException
     * @throws OtpSendingFailedException
     */
    public function send(string $phoneNumber, array $variables = []): array
    {
        // Validate phone number
        $phoneNumber = $this->validatePhoneNumber($phoneNumber);

        // Generate OTP
        $otp = $this->generateOtp();

        // Save OTP to database
        $otpModel = OtpCode::updateOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'code' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes($this->expiryMinutes),
            ]
        );

        // Fire event
        Event::dispatch(new OtpGenerated($phoneNumber, $otp));

        // Send OTP via Fonnte API with retry logic
        $response = $this->sendViaFonnteWithRetry($phoneNumber, $otp, $variables);

        if ($response['success']) {
            // Fire event
            Event::dispatch(new OtpSent($phoneNumber, $otp));
        }

        return $response;
    }

    /**
     * Verify OTP for a phone number
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function verify(string $phoneNumber, string $otp): bool
    {
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);

        $otpRecord = OtpCode::where('phone_number', $phoneNumber)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return false;
        }

        $isValid = Hash::check($otp, $otpRecord->code);

        if ($isValid) {
            // Delete the OTP record after successful verification
            $otpRecord->delete();
            
            // Fire event
            Event::dispatch(new OtpVerified($phoneNumber, $otp));
        }

        return $isValid;
    }

    /**
     * Generate a random 6-digit OTP
     *
     * @return string
     */
    protected function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP via Fonnte API with retry logic
     *
     * @param string $phoneNumber
     * @param string $otp
     * @param array $variables
     * @return array
     * @throws OtpSendingFailedException
     */
    protected function sendViaFonnteWithRetry(string $phoneNumber, string $otp, array $variables = []): array
    {
        $attempt = 0;
        
        while ($attempt < $this->maxRetries) {
            try {
                return $this->sendViaFonnte($phoneNumber, $otp, $variables);
            } catch (RequestException $e) {
                $attempt++;
                
                if ($attempt >= $this->maxRetries) {
                    throw new OtpSendingFailedException("Failed to send OTP after {$this->maxRetries} attempts: " . $e->getMessage());
                }
                
                // Wait before retrying
                usleep($this->retryDelay * 1000);
            } catch (\Exception $e) {
                throw new OtpSendingFailedException("Failed to send OTP: " . $e->getMessage());
            }
        }
        
        return [
            'success' => false,
            'error' => 'Max retries exceeded',
        ];
    }

    /**
     * Send OTP via Fonnte API
     *
     * @param string $phoneNumber
     * @param string $otp
     * @param array $variables
     * @return array
     * @throws OtpSendingFailedException
     */
    protected function sendViaFonnte(string $phoneNumber, string $otp, array $variables = []): array
    {
        try {
            $message = $this->formatMessage($otp, $variables);

            $response = $this->client->post("{$this->baseUrl}/send", [
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'form_params' => [
                    'target' => $phoneNumber,
                    'message' => $message,
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => $responseData,
            ];
        } catch (\Exception $e) {
            throw new OtpSendingFailedException("Failed to send OTP: " . $e->getMessage());
        }
    }

    /**
     * Format the message with OTP and variables
     *
     * @param string $otp
     * @param array $variables
     * @return string
     */
    protected function formatMessage(string $otp, array $variables = []): string
    {
        // Start with the base template
        $message = $this->messageTemplate;
        
        // Replace the OTP placeholder
        $message = str_replace('{code}', $otp, $message);
        
        // Replace expiry time placeholder
        $message = str_replace('{expiry}', $this->expiryMinutes, $message);
        
        // Merge with default variables
        $allVariables = array_merge($this->messageVariables, $variables);
        
        // Replace custom variables
        foreach ($allVariables as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        
        return $message;
    }

    /**
     * Validate and format phone number
     *
     * @param string $phoneNumber
     * @return string
     * @throws InvalidPhoneNumberException
     */
    protected function validatePhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);

        // Check if it's a valid Indonesian phone number format
        if (!preg_match('/^(\+62|62|0)[2-9]\d{7,11}$/', $phoneNumber)) {
            throw new InvalidPhoneNumberException("Invalid phone number format: {$phoneNumber}");
        }

        return $phoneNumber;
    }

    /**
     * Format phone number to international format
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters except +
        $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Convert to international format
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '+62' . substr($phoneNumber, 1);
        } elseif (str_starts_with($phoneNumber, '62')) {
            $phoneNumber = '+62' . substr($phoneNumber, 2);
        }

        return $phoneNumber;
    }
}