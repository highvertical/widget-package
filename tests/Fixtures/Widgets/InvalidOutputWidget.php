<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;

class InvalidOutputWidget extends Widget
{
    public function render(array $params = []): array
    {
        return ['invalid' => true];
    }
}
