<?php

namespace Spatie\Skeleton\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Tightenco\Collect\Support\Collection;

class UpgradeMediaCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'medialibrary:upgrade-media 
    {--dry-run : List files that will be removed without removing them}
    {--force : Force the operation to run when in production}';

    protected $description = 'Update the names of the outdated files';

    /** @var bool */
    protected $isDryRun = false;

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

        $mediaFilesToChange = $this->getMediaToBeRenamed();

        $progressBar = $this->output->createProgressBar($mediaFilesToChange->count());

        $this->errorMessages = [];

        $mediaFiles->each(function (Media $media) use ($progressBar) {
            try {
                $this->fileManipulator->createDerivedFiles(
                    $media, array_wrap($this->option('only')), $this->option('only-missing')
                );
            } catch (Exception $exception) {
                $this->errorMessages[$media->id] = $exception->getMessage();
            }

            $progressBar->advance();
        });

        $progressBar->finish();

        if (count($this->errorMessages)) {
            $this->warn('All done, but with some error messages:');

            foreach ($this->errorMessages as $mediaId => $message) {
                $this->warn("Media id {$mediaId}: `{$message}`");
            }
        }

        $this->info('All done!');
    }

    public function getMediaToBeRenamed(): Collection
    {

    }
}