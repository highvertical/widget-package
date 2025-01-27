<?php

namespace Highvertical\WidgetPackage\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Highvertical\WidgetPackage\WidgetManager;
use Highvertical\WidgetPackage\Console\Commands\PublishWidgetConfig;

class WidgetServiceProvider extends ServiceProvider
{
    public function register()
    {  
        // Register the WidgetManager as a singleton
        $this->app->singleton('widget.manager', function ($app) {
            return new WidgetManager();
        });

        // Merge the package configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/widgets.php', 'widgets');

        // Register the Artisan command
        $this->commands([
            PublishWidgetConfig::class,
        ]);
    }

    public function boot()
    {
        if (config('widgets.disable_cache', false)) {
            $this->clearViewCache();
        }

        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../../config/widgets.php' => config_path('widgets.php'),
        ], 'widget-config');

        // Load views if the directory exists
        if (is_dir(__DIR__.'/../../resources/views/widgets')) {
            $this->loadViewsFrom(__DIR__.'/../../resources/views/widgets', 'widgets');
        } else {
            \Log::warning('Widgets view directory not found: ' . __DIR__.'/../../resources/views/widgets');
        }

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'widget-package');
        $this->registerWidgets();

        // Register Blade directive for widgets
        Blade::directive('widget', function ($expression) {
            return "<?php echo app('widget.manager')->render($expression); ?>";
        });
    }

    protected function registerWidgets()
    {
        $widgetConfig = config('widgets.widgets');


        foreach ($widgetConfig as $alias => $widgetClass) {
            if (class_exists($widgetClass)) {
                $this->app['widget.manager']->registerWidget($alias, $widgetClass);
            }
        }

        // Support for modular applications (e.g., nwidart/laravel-modules)
        if (class_exists(\Nwidart\Modules\Facades\Module::class)) {
            // Only proceed if the package is available
            foreach (\Nwidart\Modules\Facades\Module::allEnabled() as $module) {
                $moduleConfigPath = module_path($module->getName(), 'Config/widgets.php');
                if (file_exists($moduleConfigPath)) {
                    $moduleWidgetConfig = require $moduleConfigPath;
                    foreach ($moduleWidgetConfig['widgets'] as $alias => $widgetClass) {
                        if (class_exists($widgetClass)) {
                            $this->app['widget.manager']->registerWidget($alias, $widgetClass);
                        }
                    }
                }
            }
        }
    }

    // clear the package cache
    protected function clearViewCache()
    {
        app('files')->deleteDirectory(storage_path('framework/views'));
    }
}
