<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PageHeader extends Component
{
    /**
     * The page title.
     *
     * @var string
     */
    public $title;

    /**
     * The icon class.
     *
     * @var string
     */
    public $icon;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param string $icon
     * @return void
     */
    public function __construct($title, $icon = 'bi-folder')
    {
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.page-header');
    }
}
