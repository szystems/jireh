<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatsCard extends Component
{
    /**
     * The card title.
     *
     * @var string
     */
    public $title;

    /**
     * The card value.
     *
     * @var mixed
     */
    public $value;

    /**
     * The card icon.
     *
     * @var string
     */
    public $icon;

    /**
     * The card color.
     *
     * @var string
     */
    public $color;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param mixed $value
     * @param string $icon
     * @param string $color
     * @return void
     */
    public function __construct($title, $value, $icon = 'bi-bar-chart', $color = 'primary')
    {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.stats-card');
    }
}
