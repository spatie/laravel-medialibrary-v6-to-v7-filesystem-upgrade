<?php

namespace Spatie\UpgradeTool\Tests;

use \Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
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
            'root' => 'tests/test-directory',
        ]);

        $s3Configuration = [
            'driver' => 's3',
            'key' => getenv('S3_ACCESS_KEY_ID'),
            'secret' => getenv('S3_SECRET_ACCESS_KEY'),
            'region' => getenv('S3_BUCKET_REGION'),
            'bucket' => getenv('S3_BUCKET_NAME'),
        ];

        $app['config']->set('filesystems.disks.s3_disk', $s3Configuration);
        $app['config']->set(
            'medialibrary.s3.domain',
            'https://' . $s3Configuration['bucket'] . '.s3.amazonaws.com'
        );

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        $app->bind('path.public', function () {
            return 'tests/test-directory';
        });
    }
}
