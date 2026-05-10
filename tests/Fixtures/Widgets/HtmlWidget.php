<?php

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;
use Illuminate\Support\HtmlString;

class HtmlWidget extends Widget
{
    public function render(array $params = array())
    {
        return new HtmlString('<strong>Safe HTML</strong>');
    }
}
