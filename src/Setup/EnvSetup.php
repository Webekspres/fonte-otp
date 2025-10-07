<?php

namespace Webekspres\FonnteOtp\Setup;

class EnvSetup
{
    public static function handle()
    {
        // Try to find the .env file in common locations
        $envPath = null;
        $possiblePaths = [
            dirname(__DIR__, 2) . '/.env',
            getcwd() . '/.env',
            $_SERVER['PWD'] ?? '' . '/.env'
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path)) {
                $envPath = $path;
                break;
            }
        }
        
        if (!$envPath || !file_exists($envPath)) {
            return;
        }
        
        $envContent = file_get_contents($envPath);
        
        // Check if the keys already exist
        $keysToAdd = [
            'FONNTE_TOKEN=' => 'FONNTE_TOKEN=',
            'FONNTE_OTP_EXPIRY=' => 'FONNTE_OTP_EXPIRY=5',
            'FONNTE_MESSAGE_TEMPLATE=' => 'FONNTE_MESSAGE_TEMPLATE="Kode OTP Anda adalah {code}"'
        ];
        
        $updated = false;
        
        foreach ($keysToAdd as $key => $value) {
            if (strpos($envContent, $key) === false) {
                $envContent .= "\n{$value}";
                $updated = true;
            }
        }
        
        if ($updated) {
            file_put_contents($envPath, $envContent);
            echo "Fonnte OTP environment variables added to .env file.\n";
        } else {
            echo "Fonnte OTP environment variables already exist in .env file.\n";
        }
    }
}