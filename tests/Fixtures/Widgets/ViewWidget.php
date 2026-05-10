<?php

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class ViewWidget extends Widget
{
    public function render(array $params = array())
    {
        return view('widget-package-tests::greeting', array(
            'name' => isset($params['name']) ? $params['name'] : 'Guest',
        ));
    }
}
