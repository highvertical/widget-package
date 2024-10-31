<?php
namespace Highvertical\WidgetPackage;

use Illuminate\Support\Facades\Cache;
use Exception;

class WidgetManager
{
    protected $widgets = [];

    public function __construct()
    {
        //
    }

    public function registerWidget($alias, $widget)
    {
        $this->widgets[$alias] = $widget;
    }

    public function render($alias, $params = [])
    {
        try {
            if (!isset($this->widgets[$alias])) {
                throw new Exception("Widget [{$alias}] not found.");
            }

            $widget = app($this->widgets[$alias]);

            if (!method_exists($widget, 'render')) {
                throw new Exception("Widget [{$alias}] must have a render method.");
            }

            $output = $widget->render($params);

            // Convert output to a serializable format if necessary
            if (is_object($output)) {
                $output = method_exists($output, '__toString') ? (string) $output : json_encode($output);
            }

            return $output;
        } catch (Exception $e) {
            // Log the error for easier debugging
            \Log::error("Error rendering widget [{$alias}]: " . $e->getMessage(), [
                'alias' => $alias,
                'params' => $params,
                'exception' => $e,
            ]);

            // Optionally, return a fallback message or empty string
            return config('widgets.fallback_message', "<!-- Error rendering widget [{$alias}] -->");
        }
    }

}
