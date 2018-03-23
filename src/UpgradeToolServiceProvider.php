<?php

namespace Spatie\MedialibraryV7UpgradeTool;

use Illuminate\Support\ServiceProvider;
use Spatie\MedialibraryV7UpgradeTool\Commands\UpgradeMediaCommand;

class UpgradeToolServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('command.upgrade-media', UpgradeMediaCommand::class);

        $this->commands([
            'command.upgrade-media',
        ]);
    }
}
