<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class PlainTextWidget extends Widget
{
    public function render(array $params = []): string
    {
        return isset($params['content']) ? $params['content'] : '';
    }
}
