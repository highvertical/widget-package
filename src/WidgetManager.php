<?php

namespace Highvertical\WidgetPackage;

use Highvertical\WidgetPackage\Exceptions\InvalidWidgetException;
use Highvertical\WidgetPackage\Exceptions\InvalidWidgetOutputException;
use Highvertical\WidgetPackage\Exceptions\WidgetNotFoundException;
use Highvertical\WidgetPackage\Widgets\Widget;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;

class WidgetManager
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array<string, string>
     */
    protected $widgets = array();

    /**
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @param  array<string, string>  $widgets
     */
    public function __construct(Container $container, array $widgets = array())
    {
        $this->container = $container;
        $this->registerMany($widgets);
    }

    /**
     * @param  string  $alias
     * @param  string  $widgetClass
     * @return $this
     */
    public function register($alias, $widgetClass)
    {
        $normalizedAlias = $this->normalizeAlias($alias);

        if (! class_exists($widgetClass)) {
            throw new InvalidWidgetException(sprintf('Widget class [%s] does not exist.', $widgetClass));
        }

        if (! is_subclass_of($widgetClass, Widget::class)) {
            throw new InvalidWidgetException(sprintf('Widget class [%s] must extend [%s].', $widgetClass, Widget::class));
        }

        $this->widgets[$normalizedAlias] = $widgetClass;

        return $this;
    }

    /**
     * @param  array<string, string>  $widgets
     * @return $this
     */
    public function registerMany(array $widgets)
    {
        foreach ($widgets as $alias => $widgetClass) {
            $this->register($alias, $widgetClass);
        }

        return $this;
    }

    /**
     * @param  string  $alias
     * @param  string  $widgetClass
     * @return $this
     */
    public function registerWidget($alias, $widgetClass)
    {
        return $this->register($alias, $widgetClass);
    }

    /**
     * @param  string  $alias
     */
    public function has($alias): bool
    {
        return array_key_exists($this->normalizeAlias($alias), $this->widgets);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->widgets;
    }

    /**
     * @param  string  $alias
     * @param  array<string, mixed>  $params
     */
    public function render($alias, array $params = array()): HtmlString
    {
        return $this->normalizeOutput($this->make($alias)->render($params));
    }

    /**
     * @param  string  $alias
     */
    public function make($alias): Widget
    {
        $normalizedAlias = $this->normalizeAlias($alias);

        if (! array_key_exists($normalizedAlias, $this->widgets)) {
            throw new WidgetNotFoundException(sprintf('Widget [%s] is not registered.', $normalizedAlias));
        }

        $widget = $this->container->make($this->widgets[$normalizedAlias]);

        if (! $widget instanceof Widget) {
            throw new InvalidWidgetException(sprintf('Resolved widget [%s] must extend [%s].', get_class($widget), Widget::class));
        }

        return $widget;
    }

    /**
     * @param  mixed  $output
     */
    protected function normalizeOutput($output): HtmlString
    {
        if ($output instanceof Htmlable) {
            return new HtmlString($output->toHtml());
        }

        if ($output instanceof Renderable) {
            return new HtmlString($output->render());
        }

        if (is_scalar($output) || $output === null) {
            return new HtmlString(e((string) $output));
        }

        if (is_object($output) && method_exists($output, '__toString')) {
            return new HtmlString(e((string) $output));
        }

        throw new InvalidWidgetOutputException(sprintf(
            'Widget output must be a scalar, null, Htmlable, Renderable, or stringable object. [%s] given.',
            is_object($output) ? get_class($output) : gettype($output)
        ));
    }

    /**
     * @param  mixed  $alias
     */
    protected function normalizeAlias($alias): string
    {
        $normalizedAlias = trim((string) $alias);

        if ($normalizedAlias === '') {
            throw new InvalidWidgetException('Widget alias must be a non-empty string.');
        }

        return $normalizedAlias;
    }
}
