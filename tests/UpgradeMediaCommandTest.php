<?php

namespace Spatie\UpgradeTool\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class UpgradeMediaCommandTest extends TestCase
{

    /** @test */
    public function it_can_do_a_dry_run()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-media', ['disk' => 'local', '--dry-run' => 'default']);

        $output = Artisan::output();

        $this->assertFileExists('tests/test-directory/1/conversions/thumb.png');
        $this->assertFileNotExists('tests/test-directory/1/conversions/white-cube-thumb.png');

        $this->assertFileExists('tests/test-directory/already-version-7/conversions/white-cube-thumb.png');

        $this->assertFileExists('tests/test-directory/not-default-path/c/thumb.png');
        $this->assertFileNotExists('tests/test-directory/not-default-path/c/white-cube-thumb.png');

        $this->assertContains('The file `1/conversions/thumb.png` would become `1/conversions/white-cube-thumb.png`', $output);
        $this->assertContains('The file `not-default-path/c/thumb.png` would become `not-default-path/c/white-cube-thumb.png`', $output);
        $this->assertNotContains('The file `already-version-7/conversions/white-cube-thumb.png`', $output);
    }

    /** @test */
    public function it_can_rename_files_saved_with_the_default_settings()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-media', ['disk' => 'local']);

        $this->assertFileExists('tests/test-directory/1/conversions/white-cube-thumb.png');
        $this->assertFileNotExists('tests/test-directory/1/conversions/thumb.png');
    }

    /** @test */
    public function it_does_not_change_the_already_converted_files()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-media', ['disk' => 'local']);

        $this->assertFileExists('tests/test-directory/already-version-7/conversions/white-cube-thumb.png');
        $this->assertFileNotExists('tests/test-directory/already-version-7/conversions/white-cube-white-cube-thumb.png');
    }

    /** @test */
    public function it_can_rename_files_in_custom_named_directories()
    {
        $this->resetTestFolderStructure();

        Artisan::call('upgrade-media', ['disk' => 'local']);

        $this->assertFileExists('tests/test-directory/not-default-path/c/white-cube-thumb.png');
        $this->assertFileNotExists('tests/test-directory/not-default-path/c/thumb.png');
    }

    protected function resetTestFolderStructure()
    {
        collect(Storage::disk('local')->allDirectories('/'))->each(function ($directory) {
            Storage::disk('local')->deleteDirectory($directory);
        });

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
}
