<?php

namespace Highvertical\WidgetPackage\Console\Commands;

use Illuminate\Console\Command;

class PublishWidgetConfig extends Command
{
    protected $signature = 'widget:publish-config';
    protected $description = 'Publish the configuration for the Widget package';

    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => 'Highvertical\WidgetPackage\Providers\WidgetServiceProvider',
            '--tag' => 'config',
        ]);

        $this->info('Widget package configuration published successfully!');
    }
}
