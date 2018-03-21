<?php

namespace Spatie\UpgradeTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\ConfirmableTrait;

class UpgradeMediaCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'upgrade-media 
    {disk? : Disk to use}
    {location? : Relative path to the location of your media}
    {--d|dry-run : List files that will be renamed without renaming them}
    {--f|force : Force the operation to run when in production}';

    protected $description = 'Update the names of the version 6 files of spatie/laravel-medialibrary';

    protected $isDryRun = false;

    /** @var string */
    protected $disk;

    public function handle(): void
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        if (! $this->confirmToProceed('This action changes the name of your existing media!', $this->warnWhenNoForce())) {
            return;
        }

        $this->isDryRun = $this->option('dry-run');

        $this->disk = $this->argument('disk') ?? config('medialibrary.default_filesystem');

        $location = $this->argument('location') ?? '/';

        $mediaFilesToChange = $this->getMediaToBeRenamed($location);

        $progressBar = $this->output->createProgressBar($mediaFilesToChange->count());

        $mediaFilesToChange->each(function ($file) use ($progressBar) {
            if ($this->isDryRun) {
                $this->comment("The file `{$file['current']}` would become `{$file['replacement']}`");
            }

            if (! $this->isDryRun) {
                Storage::disk($this->disk)->move($file['current'], $file['replacement']);

                $progressBar->advance();
            }
        });

        if (! $this->isDryRun) {
            $progressBar->finish();

            $this->output->newLine();
        }

        $this->info('All done!');
    }

    protected function getMediaToBeRenamed($location): Collection
    {
        return Collection::make(Storage::disk($this->disk)->allFiles($location))
            ->filter(function ($file) {
                return $this->getOriginal($file);
            })
            ->filter(function ($file) {
                $original = $this->getOriginal($file);
                $currentFile = pathinfo($file, PATHINFO_BASENAME);
                $originalName = pathinfo($original, PATHINFO_FILENAME);

                return strpos($currentFile, $originalName) === false;
            })
            ->map(function ($file) {
                $original = $this->getOriginal($file);
                $currentPath = pathinfo($file, PATHINFO_DIRNAME);
                $currentFile = pathinfo($file, PATHINFO_BASENAME);
                $originalName = pathinfo($original, PATHINFO_FILENAME);

                return [
                    'current' => $file,
                    'replacement' => "{$currentPath}/{$originalName}-{$currentFile}",
                ];
            });
    }

    protected function getOriginal(string $filePath): ?string
    {
        $path = pathinfo($filePath, PATHINFO_DIRNAME);

        $oneLevelHigher = dirname($path);

        if ($oneLevelHigher === '.') {
            return null;
        }

        $original = Storage::files($oneLevelHigher);

        if (count($original) < 1) {
            return null;
        }

        return $original[0];
    }

    protected function warnWhenNoForce()
    {
        return function () {
            return true;
        };
    }
}
