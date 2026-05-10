<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Widgets;

abstract class Widget
{
    /**
     * Render the widget payload.
     *
     * Return a Blade view, HtmlString, or plain string.
     * Plain strings are escaped by the package before output.
     *
     * @param  array<string, mixed>  $params
     */
    abstract public function render(array $params = []): mixed;
}
