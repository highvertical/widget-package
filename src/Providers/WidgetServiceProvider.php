<?php

namespace Highvertical\WidgetPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Highvertical\WidgetPackage\Console\Commands\PublishWidgetConfig;
use Highvertical\WidgetPackage\WidgetManager;
use Illuminate\Support\Facades\Blade;

class WidgetServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the WidgetManager as a singleton
        $this->app->singleton('widget.manager', function ($app) {
            return new WidgetManager($app['cache.store']);
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
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../../config/widgets.php' => config_path('widgets.php'),
        ], 'widget-config');

        // Load views and translations if any
        $this->loadViewsFrom(__DIR__.'/../../resources/views/widgets', 'widgets');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'widget-package');

        // Load routes from the package
        //$this->loadRoutesFrom(__DIR__ . '/../../../../routes/web.php');

        // Register widgets
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
            foreach (\Nwidart\Modules\Facades\Module::allEnabled() as $module) {
                $moduleConfigPath = module_path($module->getName(), 'Config/widgets.php');
                if (file_exists($moduleConfigPath)) {
                    $modules = \Nwidart\Modules\Facades\Module::allEnabled();

                    foreach ($modules as $module) {
                        $moduleConfigPath = module_path($module->getName(), 'Config/widgets.php');
                        if (File::exists($moduleConfigPath)) {
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
        }
    }
}
