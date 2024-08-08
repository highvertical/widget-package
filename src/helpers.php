<?php

use Highvertical\WidgetPackage\WidgetManager;

if (! function_exists('register_widget')) {
    function register_widget($alias, $widget)
    {
        app(WidgetManager::class)->registerWidget($alias, $widget);
    }
}
