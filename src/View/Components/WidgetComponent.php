<?php

declare(strict_types=1);

namespace Highvertical\WidgetPackage\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WidgetComponent extends Component
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly string $alias,
        public readonly array $data = []
    ) {}

    public function render(): View
    {
        return view('widget-package::components.widget', [
            'alias' => $this->alias,
            'data' => $this->data,
        ]);
    }
}
