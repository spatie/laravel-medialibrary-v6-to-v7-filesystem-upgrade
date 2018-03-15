<?php

namespace Spatie\UpgradeTool\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Tightenco\Collect\Support\Collection;

class UpgradeMediaCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'upgrade-tool 
    {location : Relative path to the location of your media}
    {--dry-run : List files that will be renamed without renaming them}
    {--force : Force the operation to run when in production}';

    protected $description = 'Update the names of the outdated files';

    /** @var bool */
    protected $isDryRun = false;

    /** @var array */
    protected $errorMessages;

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

        $this->errorMessages = [];

        $mediaFilesToChange->each(function ($file) use ($progressBar) {
            try {
                var_dump($file);
            } catch (Exception $exception) {
                $this->errorMessages[$file] = $exception->getMessage();
            }

            $progressBar->advance();
        });

        $progressBar->finish();

        if (count($this->errorMessages)) {
            $this->warn('All done, but with some error messages:');

            foreach ($this->errorMessages as $fileName => $message) {
                $this->warn("Media file ({$fileName}): `{$message}`");
            }
        }

        $this->info('All done!');
    }

    public function getMediaToBeRenamed(string $pathToMedia): Collection
    {
        $fileTree = $this->createFileTree($pathToMedia);

        return $fileTree;
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

                return $fullPath;
            });
    }
}
