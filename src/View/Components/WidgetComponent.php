<?php

namespace Highvertical\WidgetPackage\View\Components;

use Illuminate\View\Component;

class WidgetComponent extends Component
{
    /**
     * @var string
     */
    public $alias;

    /**
     * @var array<string, mixed>
     */
    public $data;

    /**
     * @param  string  $alias
     * @param  array<string, mixed>  $data
     * @return void
     */
    public function __construct($alias, array $data = array())
    {
        $this->alias = $alias;
        $this->data = $data;
    }

    /**
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('widget-package::components.widget', array(
            'alias' => $this->alias,
            'data' => $this->data,
        ));
    }
}
