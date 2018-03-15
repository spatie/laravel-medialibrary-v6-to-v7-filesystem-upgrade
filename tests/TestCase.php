<?php

namespace Spatie\UpgradeTool\Tests;

use \Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Spatie\UpgradeTool\UpgradeToolServiceProvider::class,
        ];
    }
}
