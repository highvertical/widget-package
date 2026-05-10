<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Providers;

use Highvertical\WidgetPackage\View\Components\WidgetComponent;
use Highvertical\WidgetPackage\WidgetManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/widget-package.php', 'widget-package');

        $this->app->singleton(WidgetManager::class, function ($app): WidgetManager {
            return new WidgetManager(
                $app,
                (array) $app['config']->get('widget-package.widgets', [])
            );
        });

        $this->app->alias(WidgetManager::class, 'widget.manager');
        $this->app->alias(WidgetManager::class, 'widget-package.manager');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'widget-package');

        Blade::component('widget-package', WidgetComponent::class);
        Blade::include('widget-package::components.widget', 'widgetPackage');
        Blade::directive('widget', function (string $expression): string {
            return "<?php echo app('".addslashes(WidgetManager::class)."')->render({$expression}); ?>";
        });

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__.'/../../config/widget-package.php' => config_path('widget-package.php'),
                ],
                'widget-package-config'
            );

            $this->publishes(
                [
                    __DIR__.'/../../resources/views' => resource_path('views/vendor/widget-package'),
                ],
                'widget-package-views'
            );
        }
    }
}
