<?php

namespace App\View\Components;

use Illuminate\View\Component;

// A thumbnail image wrapper for displaying an intervetntion image url
class ThumbnailImage extends Component
{
    public $template = "";
    public $src = "";

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($src, $template="large")
    {
        $this->src = $src;
        $this->template = $template;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.thumbnail-image');
    }
}
