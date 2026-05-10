<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

class HtmlableWidget extends Widget
{
    public function render(array $params = []): Htmlable
    {
        return new class implements Htmlable
        {
            public function toHtml(): string
            {
                return '<em>Unexpected raw HTML</em>';
            }
        };
    }
}
