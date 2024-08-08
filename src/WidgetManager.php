<?php

namespace Highvertical\WidgetPackage;

use Illuminate\Support\Facades\Cache;
use Exception;

class WidgetManager
{
    protected $widgets = [];
    protected $cache;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    public function registerWidget($alias, $widget)
    {
        $this->widgets[$alias] = $widget;
    }

    public function render($alias, $params = [])
    {
        if (!isset($this->widgets[$alias])) {
            throw new Exception("Widget [{$alias}] not found.");
        }

        $widget = app($this->widgets[$alias]);

        if (!method_exists($widget, 'render')) {
            throw new Exception("Widget [{$alias}] must have a render method.");
        }

        $cacheKey = 'widget_' . $alias . '_' . md5(serialize($params));
        $cacheEnabled = config('widgets.cache.enabled');
        $cacheTTL = config('widgets.cache.ttl');

        if ($cacheEnabled && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $output = $widget->render($params);

        if ($cacheEnabled) {
            $this->cache->put($cacheKey, $output, $cacheTTL);
        }

        return $output;
    }
}