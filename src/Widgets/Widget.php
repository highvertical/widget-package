<?php

namespace Highvertical\WidgetPackage\Widgets;

abstract class Widget
{
    abstract public function render(array $params = []);
}
