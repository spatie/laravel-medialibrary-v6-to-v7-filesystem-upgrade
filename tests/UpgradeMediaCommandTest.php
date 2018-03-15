<?php

namespace Spatie\UpgradeTool\Tests;

use Illuminate\Support\Facades\Artisan;

class UpgradeMediaCommandTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_rename_files_saved_with_the_default_settings()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-tool', ['location' => 'tests/Media']);

        $this->assertFileExists('tests/Media/1/conversions/white-cube-thumb.png');
        $this->assertFileNotExists('tests/Media/1/conversions/thumb.png');
    }

    /** @test */
    public function it_does_not_change_the_already_converted_files()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-tool', ['location' => 'tests/Media']);

        $this->assertFileExists('tests/Media/already-version-7/conversions/white-cube-thumb.png');
        $this->assertFileNotExists('tests/Media/already-version-7/conversions/white-cube-white-cube-thumb.png');
    }

    /** @test */
    public function it_can_rename_files_in_custom_named_directories()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-tool', ['location' => 'tests/Media']);

        $this->assertFileExists('tests/Media/not-default-path/c/white-cube-thumb.png');
        $this->assertFileNotExists('tests/Media/not-default-path/c/thumb.png');
    }

    protected function resetTestFolderStructure()
    {
        $this->removeDir('tests/Media');

        mkdir('tests/Media');

        mkdir('tests/Media/1');
        mkdir('tests/Media/already-version-7');
        mkdir('tests/Media/not-default-path');

        mkdir('tests/Media/1/conversions');
        mkdir('tests/Media/already-version-7/conversions');
        mkdir('tests/Media/not-default-path/c');

        copy('tests/test-image.png', 'tests/Media/1/white-cube.png');
        copy('tests/test-image.png', 'tests/Media/already-version-7/white-cube.png');
        copy('tests/test-image.png', 'tests/Media/not-default-path/white-cube.png');

        copy('tests/test-image.png', 'tests/Media/1/conversions/thumb.png');
        copy('tests/test-image.png', 'tests/Media/already-version-7/conversions/white-cube-thumb.png');
        copy('tests/test-image.png', 'tests/Media/not-default-path/c/thumb.png');
    }

    protected function removeDir($directory)
    {
        if (! file_exists($directory)) {
            return;
        }

        $this->removeFilesFrom('tests/Media/1/conversions');
        $this->removeFilesFrom('tests/Media/already-version-7/conversions');
        $this->removeFilesFrom('tests/Media/not-default-path/c');

        rmdir('tests/Media/1/conversions');
        rmdir('tests/Media/already-version-7/conversions');
        rmdir('tests/Media/not-default-path/c');

        $this->removeFilesFrom('tests/Media/1');
        $this->removeFilesFrom('tests/Media/already-version-7');
        $this->removeFilesFrom('tests/Media/not-default-path');

        rmdir('tests/Media/1');
        rmdir('tests/Media/already-version-7');
        rmdir('tests/Media/not-default-path');

        rmdir($directory);
    }

    protected function removeFilesFrom($directory)
    {
        collect(scandir($directory))
            ->reject(function ($file) {
                return $file === '.' || $file === '..';
            })
            ->each(function ($file) use ($directory) {
                if (is_file("{$directory}/{$file}")) {
                    unlink("{$directory}/{$file}");
                }
            });
    }
}
