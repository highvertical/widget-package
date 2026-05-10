<?php

use Highvertical\WidgetPackage\WidgetManager;
use Illuminate\Support\HtmlString;

if (! function_exists('widget_package_manager')) {
    function widget_package_manager(): WidgetManager
    {
        return app(WidgetManager::class);
    }
}

if (! function_exists('widget_package_render')) {
    /**
     * @param  string  $alias
     * @param  array<string, mixed>  $data
     */
    function widget_package_render($alias, array $data = array()): HtmlString
    {
        return widget_package_manager()->render($alias, $data);
    }
}

if (! function_exists('widgetPackage')) {
    /**
     * Preferred v1.x helper API.
     *
     * @param  string  $alias
     * @param  array<string, mixed>  $data
     */
    function widgetPackage($alias, array $data = array()): HtmlString
    {
        return widget_package_render($alias, $data);
    }
}

if (! function_exists('widget_package_register')) {
    /**
     * @param  string  $alias
     * @param  string  $widgetClass
     */
    function widget_package_register($alias, $widgetClass): WidgetManager
    {
        return widget_package_manager()->register($alias, $widgetClass);
    }
}

if (! function_exists('widget')) {
    /**
     * Legacy helper kept for v1.x compatibility.
     *
     * @param  string  $alias
     * @param  array<string, mixed>  $data
     */
    function widget($alias, array $data = array()): HtmlString
    {
        return widgetPackage($alias, $data);
    }
}

if (! function_exists('register_widget')) {
    /**
     * Legacy helper kept for v1.x compatibility.
     *
     * @param  string  $alias
     * @param  string  $widgetClass
     */
    function register_widget($alias, $widgetClass): WidgetManager
    {
        return widget_package_register($alias, $widgetClass);
    }
}
