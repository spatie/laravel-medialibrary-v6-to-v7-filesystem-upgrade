<?php

namespace Spatie\UpgradeTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Storage;
use Tightenco\Collect\Support\Collection;

class UpgradeMediaCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'upgrade-media 
    {disk? : Relative path to the location of your media}
    {location? : Relative path to the location of your media}
    {--dry-run : List files that will be renamed without renaming them}
    {--force : Force the operation to run when in production}';

    protected $description = 'Update the names of the outdated files';

    /** @var bool */
    protected $isDryRun = false;

    /** @var string */
    protected $disk;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (! $this->confirmToProceed()) {
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
            }

            $progressBar->advance();
        });

        $progressBar->finish();

        $this->info('All done!');
    }

    public function getMediaToBeRenamed($location): Collection
    {
        return Collection::make(Storage::disk($this->disk)->allFiles($location))
            ->map(function ($file) {
                if ($original = $this->getOriginal($file)) {
                    $currentPath = pathinfo($file, PATHINFO_DIRNAME);
                    $currentFile = pathinfo($file, PATHINFO_BASENAME);
                    $originalName = pathinfo($original, PATHINFO_FILENAME);

                    if (strpos($currentFile, $originalName) === false) {
                        return [
                            'current' => $file,
                            'replacement' => "{$currentPath}/{$originalName}-{$currentFile}",
                        ];
                    }
                }

                return null;
            })
            ->filter(function ($file) {
                return $file;
            });
    }

    protected function getOriginal(string $filePath): ?string
    {
        $path = pathinfo($filePath, PATHINFO_DIRNAME);

        $path = Collection::make(explode('/', $path));

        $path->pop();

        if($path->count() < 1){
            return null;
        }

        $oneLevelHigher = $path->implode('/');

        $original = Storage::files($oneLevelHigher);

        if (count($original) > 0) {
            return $original[0];
        }

        return null;
    }
}
