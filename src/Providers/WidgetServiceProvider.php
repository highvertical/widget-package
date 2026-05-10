<?php

namespace Highvertical\WidgetPackage\Providers;

use Highvertical\WidgetPackage\WidgetManager;
use Highvertical\WidgetPackage\View\Components\WidgetComponent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/widget-package.php', 'widget-package');
        $this->mergeConfigFrom(__DIR__ . '/../../config/widget-package.php', 'widgets');

        $this->app->singleton(WidgetManager::class, function ($app) {
            $configuredWidgets = (array) $app['config']->get('widget-package.widgets', array());

            if ($configuredWidgets === array()) {
                $configuredWidgets = (array) $app['config']->get('widgets.widgets', array());
            }

            return new WidgetManager(
                $app,
                $configuredWidgets
            );
        });

        $this->app->alias(WidgetManager::class, 'widget.manager');
        $this->app->alias(WidgetManager::class, 'widget-package.manager');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'widget-package');

        Blade::component('widget-package', WidgetComponent::class);
        Blade::include('widget-package::components.widget', 'widgetPackage');
        Blade::directive('widget', function ($expression) {
            return "<?php echo app('widget.manager')->render({$expression}); ?>";
        });

        if ($this->app->runningInConsole()) {
            $this->publishes(array(
                __DIR__ . '/../../config/widget-package.php' => config_path('widget-package.php'),
            ), 'widget-package-config');

            $this->publishes(array(
                __DIR__ . '/../../config/widgets.php' => config_path('widgets.php'),
            ), 'widget-config');

            $this->publishes(array(
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/widget-package'),
            ), 'widget-package-views');
        }
    }
}
