<?php

namespace Highvertical\WidgetPackage\Tests;

use Highvertical\WidgetPackage\Providers\WidgetServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, string>
     */
    protected function getPackageProviders($app)
    {
        return array(
            WidgetServiceProvider::class,
        );
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['view']->addNamespace('widget-package-tests', __DIR__ . '/Fixtures/Views');
    }
}
