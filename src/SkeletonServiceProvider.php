<?php

namespace Spatie\UpgradeTool;

use Illuminate\Support\ServiceProvider;
use Spatie\UpgradeTool\Commands\UpgradeMediaCommand;

class SkeletonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('command.upgrade-tool', UpgradeMediaCommand::class);

        $this->commands([
            'command.upgrade-tool',
        ]);
    }
}
