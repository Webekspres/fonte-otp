<?php

namespace Webekspres\FonnteOtp\Commands;

use Illuminate\Console\Command;
use Webekspres\FonnteOtp\Setup\EnvSetup;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Fonnte OTP package and publish configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing Fonnte OTP package...');

        // Publish config
        $this->call('vendor:publish', [
            '--provider' => 'Webekspres\FonnteOtp\Providers\FonnteOtpServiceProvider',
            '--tag' => 'fonnte-otp-config'
        ]);

        // Publish migrations
        $this->call('vendor:publish', [
            '--provider' => 'Webekspres\FonnteOtp\Providers\FonnteOtpServiceProvider',
            '--tag' => 'fonnte-otp-migrations'
        ]);

        // Setup .env
        EnvSetup::handle();

        $this->info('Fonnte OTP package installed successfully!');
        $this->line('');
        $this->info('Next steps:');
        $this->line('1. Add your FONNTE_TOKEN to your .env file');
        $this->line('2. Run migrations: php artisan migrate');
        $this->line('3. You\'re ready to use the package!');

        return 0;
    }
}