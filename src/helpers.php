<?php

declare(strict_types=1);

use Highvertical\WidgetPackage\WidgetManager;
use Illuminate\Support\HtmlString;

if (! function_exists('widgetPackage')) {
    /**
     * Preferred v2.x helper API.
     *
     * @param  array<string, mixed>  $data
     */
    function widgetPackage(string $alias, array $data = []): HtmlString
    {
        return app(WidgetManager::class)->render($alias, $data);
    }
}
