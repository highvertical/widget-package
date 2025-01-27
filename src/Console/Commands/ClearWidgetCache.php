<?php

namespace Highvertical\WidgetPackage\Console\Commands;

use Illuminate\Console\Command;

class ClearWidgetCache extends Command
{
    protected $signature = 'widget:clear-cache';
    protected $description = 'Clear the Blade cache for widgets';

    public function handle()
    {
        $this->call('view:clear');
        $this->info('Widget Blade cache cleared successfully!');
    }
}
