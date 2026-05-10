<?php

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class PlainTextWidget extends Widget
{
    public function render(array $params = array())
    {
        return isset($params['content']) ? $params['content'] : '';
    }
}
