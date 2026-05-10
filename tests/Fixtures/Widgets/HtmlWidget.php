<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;
use Illuminate\Support\HtmlString;

class HtmlWidget extends Widget
{
    public function render(array $params = []): HtmlString
    {
        return new HtmlString('<strong>Safe HTML</strong>');
    }
}
