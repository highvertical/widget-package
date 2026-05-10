<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\Tests\Fixtures\Widgets;

use Highvertical\WidgetPackage\Widgets\Widget;
use Illuminate\Contracts\View\View;

class ViewWidget extends Widget
{
    public function render(array $params = []): View
    {
        return view('widget-package-tests::greeting', [
            'name' => isset($params['name']) ? $params['name'] : 'Guest',
        ]);
    }
}
