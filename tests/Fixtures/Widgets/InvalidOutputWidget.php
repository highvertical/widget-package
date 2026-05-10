<?php

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class InvalidOutputWidget extends Widget
{
    public function render(array $params = array())
    {
        return array('invalid' => true);
    }
}
