<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests;

use Highvertical\WidgetPackage\Providers\WidgetServiceProvider;
use Highvertical\WidgetPackage\Tests\Fixtures\Widgets\ViewWidget;
use Highvertical\WidgetPackage\WidgetManager;
use Illuminate\Support\ServiceProvider;

class PackageRegistrationTest extends TestCase
{
    public function test_it_registers_the_widget_manager_singleton(): void
    {
        $manager = $this->app->make(WidgetManager::class);

        $this->assertInstanceOf(WidgetManager::class, $manager);
        $this->assertSame($manager, $this->app->make('widget.manager'));
        $this->assertSame($manager, $this->app->make('widget-package.manager'));
    }

    public function test_it_merges_the_default_configuration(): void
    {
        $this->assertSame([], config('widget-package.widgets'));
    }

    public function test_it_registers_widgets_from_the_package_specific_config_key(): void
    {
        config()->set('widget-package.widgets', [
            'preferred-widget' => ViewWidget::class,
        ]);

        $this->app->forgetInstance(WidgetManager::class);

        $manager = $this->app->make(WidgetManager::class);

        $this->assertTrue($manager->has('preferred-widget'));
    }

    public function test_it_loads_the_package_view_namespace(): void
    {
        $this->assertTrue($this->app['view']->exists('widget-package::components.widget'));
    }

    public function test_it_registers_publishable_configuration_and_views(): void
    {
        $configPublishes = ServiceProvider::pathsToPublish(
            WidgetServiceProvider::class,
            'widget-package-config'
        );

        $viewPublishes = ServiceProvider::pathsToPublish(
            WidgetServiceProvider::class,
            'widget-package-views'
        );

        $this->assertContains(
            config_path('widget-package.php'),
            array_values($configPublishes)
        );

        $this->assertContains(
            resource_path('views/vendor/widget-package'),
            array_values($viewPublishes)
        );
    }

    public function test_it_does_not_publish_files_automatically(): void
    {
        $this->assertFileDoesNotExist(config_path('widget-package.php'));
        $this->assertFileDoesNotExist(resource_path('views/vendor/widget-package/components/widget.blade.php'));
    }

    public function test_legacy_widget_config_is_not_merged_in_v2(): void
    {
        $this->assertNull(config('widgets'));
    }
}
