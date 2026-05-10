<?php

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
     * @return mixed
     */
    abstract public function render(array $params = array());
}
