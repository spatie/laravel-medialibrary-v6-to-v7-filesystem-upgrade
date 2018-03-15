<?php

namespace Spatie\UpgradeTool\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Tightenco\Collect\Support\Collection;

class UpgradeMediaCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'upgrade-media 
    {location : Relative path to the location of your media}
    {--dry-run : List files that will be renamed without renaming them}
    {--force : Force the operation to run when in production}';

    protected $description = 'Update the names of the outdated files';

    /** @var bool */
    protected $isDryRun = false;

    public function __construct()
    {
        parent::__construct();

        $this->fileTree = collect();
    }

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->isDryRun = $this->option('dry-run');

        $mediaFilesToChange = $this->getMediaToBeRenamed($this->argument('location'));

        $progressBar = $this->output->createProgressBar($mediaFilesToChange->count());

        $mediaFilesToChange->each(function ($file) use ($progressBar) {
            if ($this->isDryRun) {
                $this->comment("The file `{$file['current']}` would become `{$file['replacement']}`");
            }

            if (! $this->isDryRun) {
                rename($file['current'], $file['replacement']);
            }

            $progressBar->advance();
        });

        $progressBar->finish();

        $this->info('All done!');
    }

    public function getMediaToBeRenamed(string $pathToMedia): Collection
    {
        $fileTree = $this->createFileTree($pathToMedia);

        return $fileTree
            ->flatten()
            ->filter(function ($file) {
                return $file;
            })
            ->map(function ($file) {
                $replacementParts = explode(' => ', $file);

                return [
                    'current' => $replacementParts[0],
                    'replacement' => $replacementParts[1],
                ];
            });
    }

    protected function createFileTree(string $directory): ?Collection
    {
        if (! is_dir($directory)) {
            $this->error('The given string is not a directory');

            return null;
        }

        $directoryContent = Collection::make(scandir($directory));

        return $directoryContent
            ->reject(function ($content) {
                return $content === '.' || $content === '..';
            })
            ->map(function ($content) use ($directory) {
                $fullPath = "{$directory}/{$content}";

                if (is_dir($fullPath)) {
                    return $this->createFileTree($fullPath);
                }

                $original = $this->getOriginal($fullPath);

                if ($original) {
                    $name = collect(explode('.', $original));

                    $name->pop();

                    $name = $name->implode('.');
                    if (strpos($content, $name) === false) {
                        return [
                            "{$fullPath} => {$directory}/{$name}-{$content}",
                        ];
                    }
                }

                return null;
            });
    }

    protected function getOriginal(string $filePath): ?string
    {
        if (! file_exists($filePath)) {
            return null;
        }

        if (! is_file($filePath)) {
            return null;
        }

        $path = Collection::make(explode('/', $filePath));

        $path->pop();

        $path->pop();

        $oneLevelHigher = $path->implode('/');

        $original = Collection::make(scandir($oneLevelHigher))
            ->reject(function ($file) {
                return $file === '.' || $file === '..';
            })
            ->filter(function ($file) use ($oneLevelHigher) {
                return (is_file("{$oneLevelHigher}/{$file}"));
            });

        if ($original->count() > 0) {
            return $original->first();
        }

        return null;
    }
}
