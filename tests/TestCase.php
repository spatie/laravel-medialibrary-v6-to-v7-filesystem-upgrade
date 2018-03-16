<?php

namespace Spatie\UpgradeTool\Tests;

use \Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\MediaLibrary\Filesystem\DefaultFilesystem;

class TestCase extends BaseTestCase
{
    /**
     * @var \Spatie\MediaLibrary\Filesystem\DefaultFilesystem
     */
    protected $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = $this->app->make(DefaultFilesystem::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
            \Spatie\UpgradeTool\UpgradeToolServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => 'tests/Media',
        ]);

        $app->bind('path.public', function () {
            return 'tests/Media';
        });
    }
}
