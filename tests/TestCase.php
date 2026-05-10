<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests;

use Highvertical\WidgetPackage\Providers\WidgetServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            WidgetServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['view']->addNamespace('widget-package-tests', __DIR__.'/Fixtures/views');
    }
}
