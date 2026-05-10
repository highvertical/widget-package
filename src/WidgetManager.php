<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage;

use Highvertical\WidgetPackage\Exceptions\InvalidWidgetException;
use Highvertical\WidgetPackage\Exceptions\InvalidWidgetOutputException;
use Highvertical\WidgetPackage\Exceptions\WidgetNotFoundException;
use Highvertical\WidgetPackage\Widgets\Widget;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;

class WidgetManager
{
    /**
     * @var array<string, class-string<Widget>>
     */
    private array $widgets = [];

    /**
     * @param  array<string, class-string<Widget>>  $widgets
     */
    public function __construct(
        private readonly Container $container,
        array $widgets = []
    ) {
        $this->registerMany($widgets);
    }

    /**
     * @param  class-string<Widget>  $widgetClass
     */
    public function register(string $alias, string $widgetClass): static
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
     * @param  array<string, class-string<Widget>>  $widgets
     */
    public function registerMany(array $widgets): static
    {
        foreach ($widgets as $alias => $widgetClass) {
            $this->register($alias, $widgetClass);
        }

        return $this;
    }

    /**
     * @param  class-string<Widget>  $widgetClass
     */
    public function registerWidget(string $alias, string $widgetClass): static
    {
        return $this->register($alias, $widgetClass);
    }

    public function has(string $alias): bool
    {
        return array_key_exists($this->normalizeAlias($alias), $this->widgets);
    }

    /**
     * @return array<string, class-string<Widget>>
     */
    public function all(): array
    {
        return $this->widgets;
    }

    /**
     * @param  array<string, mixed>  $params
     */
    public function render(string $alias, array $params = []): HtmlString
    {
        return $this->normalizeOutput($this->make($alias)->render($params));
    }

    public function make(string $alias): Widget
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

    protected function normalizeOutput(mixed $output): HtmlString
    {
        if ($output instanceof HtmlString) {
            return $output;
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
            'Widget output must be a scalar, null, HtmlString, Renderable, or stringable object. [%s] given.',
            is_object($output) ? get_class($output) : gettype($output)
        ));
    }

    protected function normalizeAlias(mixed $alias): string
    {
        $normalizedAlias = trim((string) $alias);

        if ($normalizedAlias === '') {
            throw new InvalidWidgetException('Widget alias must be a non-empty string.');
        }

        return $normalizedAlias;
    }
}
