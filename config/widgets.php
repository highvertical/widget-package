<?php

return [
    'widgets' => [
        // Example: 'weather' => \App\Widgets\WeatherWidget::class,
    ],
    'fallback_message' => '<div class="widget-error">Widget unavailable</div>', // New option
    'disable_cache' => env('WIDGET_DISABLE_CACHE', false),
];
