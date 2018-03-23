<?php

namespace Spatie\MedialibraryV7UpgradeTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\ConfirmableTrait;

class UpgradeMediaCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'upgrade-media 
    {disk? : Disk to use}
    {--d|dry-run : List files that will be renamed without renaming them}
    {--f|force : Force the operation to run when in production}';

    protected $description = 'Update the names of the version 6 files of spatie/laravel-medialibrary';

    /** @var string */
    protected $disk;

    /** @var string */
    protected $isDryRun;

    /** @var \Illuminate\Support\Collection */
    protected $mediaFilesToChange;

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->isDryRun = $this->option('dry-run') ?? false;

        $this->disk = $this->argument('disk') ?? config('medialibrary.default_filesystem');

        $this
            ->getMediaFilesToBeRenamed()
            ->renameMediaFiles();

        $this->info('All done!');
    }

    protected function getMediaFilesToBeRenamed(): self
    {
        $this->mediaFilesToChange = collect(Storage::disk($this->disk)->allFiles())
            ->filter(function (string $file): bool {
                return $this->hasOriginal($file);
            })
            ->filter(function (string $file): bool {
                return $this->needsToBeConverted($file);
            })
            ->map(function (string $file): array {
                return $this->getReplaceArray($file);
            });

        return $this;
    }

    protected function renameMediaFiles()
    {
        if ($this->mediaFilesToChange->count() === 0){
            $this->info('There are no files to convert.');
        }

        if ($this->isDryRun) {
            $this->info('This is a dry-run and will not actually rename the files');
        }

        $this->mediaFilesToChange->each(function (array $filePaths) {
            if (! $this->isDryRun) {
                Storage::disk($this->disk)->move($filePaths['current'], $filePaths['replacement']);
            }

            $this->comment("The file `{$filePaths['current']}` has become `{$filePaths['replacement']}`");
        });
    }

    protected function hasOriginal(string $filePath): bool
    {
        $path = pathinfo($filePath, PATHINFO_DIRNAME);

        $oneLevelHigher = dirname($path);

        if ($oneLevelHigher === '.') {
            return false;
        }

        $original = Storage::disk($this->disk)->files($oneLevelHigher);

        if (count($original) !== 1) {
            return false;
        }

        return true;
    }

    protected function needsToBeConverted(string $file): bool
    {
        $currentFile = pathinfo($file);

        $original = $this->getOriginal($currentFile['dirname']);

        return strpos($currentFile['basename'], $original) === false;
    }

    protected function getReplaceArray(string $file): array
    {
        $currentFile = pathinfo($file);

        $currentFilePath = $currentFile['dirname'];

        $original = $this->getOriginal($currentFilePath);

        return [
            'current' => $file,
            'replacement' => "{$currentFilePath}/{$original}-{$currentFile['basename']}",
        ];

    }

    protected function getOriginal(string $filePath): string
    {
        $oneLevelHigher = dirname($filePath);

        $original = Storage::disk($this->disk)->files($oneLevelHigher);

        return pathinfo($original[0], PATHINFO_FILENAME);
    }
}
