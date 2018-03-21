<?php

namespace Spatie\UpgradeTool;

use Illuminate\Support\ServiceProvider;
use Spatie\UpgradeTool\Commands\UpgradeMediaCommand;

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
