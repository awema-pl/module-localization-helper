<?php

namespace AwemaPL\LocalizationHelper\Tests;

use AwemaPL\LocalizationHelper\LocalizationHelperServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LocalizationHelperServiceProvider::class
        ];
    }
}
