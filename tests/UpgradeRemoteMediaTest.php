<?php

namespace Spatie\UpgradeTool\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class UpgradeRemoteMediaTest extends TestCase
{
    /** @var @string */
    protected $s3BaseDirectory;

    public function setUp()
    {
        parent::setUp();

        if (! $this->canTestS3()) {
            $this->markTestSkipped('Skipping S3 tests because no S3 env variables found');
        }

        $this->s3BaseDirectory = self::getS3BaseTestDirectory();
    }

    public function tearDown()
    {
        $this->cleanUpS3();

        parent::tearDown();
    }

    /** @test */
    public function it_can_handle_s3_with_the_default_settings()
    {
        $this->setUpS3();

        Artisan::call('upgrade-media', ['disk' => 's3']);

        $this->assertTrue(Storage::disk('s3_disk')->has("{$this->s3BaseDirectory}/1/conversion/white-cube-thumb.png"));
        $this->assertFalse(Storage::disk('s3_disk')->has("{$this->s3BaseDirectory}/1/conversion/thumb.png"));
    }

    /** @test */
    public function it_can_handle_s3_with_already_converted_files()
    {
        $this->setUpS3();

        Artisan::call('upgrade-media', ['disk' => 's3']);

        $this->assertTrue(Storage::disk('s3_disk')->has("{$this->s3BaseDirectory}/already-version-7/conversion/white-cube-thumb.png"));
        $this->assertFalse(Storage::disk('s3_disk')->has("{$this->s3BaseDirectory}/already-version-7/conversion/white-cube-white-cube-thumb.png"));
    }

    /** @test */
    public function it_can_handle_s3_with_custom_directories()
    {
        $this->setUpS3();

        Artisan::call('upgrade-media', ['disk' => 's3']);

        $this->assertTrue(Storage::disk('s3_disk')->has("{$this->s3BaseDirectory}/not-default-path/c/white-cube-thumb.png"));
        $this->assertFalse(Storage::disk('s3_disk')->has("{$this->s3BaseDirectory}/not-default-path/c/thumb.png"));
    }

    protected function cleanUpS3()
    {
        collect(Storage::disk('s3_disk')->allDirectories(self::getS3BaseTestDirectory()))->each(function ($directory) {
            Storage::disk('s3_disk')->deleteDirectory($directory);
        });
    }

    protected function setUpS3()
    {
        $this->cleanUpS3();

        Storage::put('test-image.png', file_get_contents('tests/test-directory/test-image.png'));

        Storage::makeDirectory('1/conversions');
        Storage::makeDirectory('already-version-7/conversions');
        Storage::makeDirectory('not-default-path/c');

        Storage::copy('test-image.png', '1/white-cube.png');
        Storage::copy('test-image.png', 'already-version-7/white-cube.png');
        Storage::copy('test-image.png', 'not-default-path/white-cube.png');

        Storage::copy('test-image.png', '1/conversions/thumb.png');
        Storage::copy('test-image.png', 'already-version-7/conversions/white-cube-thumb.png');
        Storage::copy('test-image.png', 'not-default-path/c/thumb.png');
    }

    public function canTestS3()
    {
        return ! empty(getenv('S3_ACCESS_KEY_ID'));
    }

    public static function getS3BaseTestDirectory(): string
    {
        return md5(getenv('TRAVIS_BUILD_ID') . app()->version() . phpversion());
    }
}
