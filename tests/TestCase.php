<?php

namespace Webekspres\FonnteOtp\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'Webekspres\FonnteOtp\Providers\FonnteOtpServiceProvider',
        ];
    }
}