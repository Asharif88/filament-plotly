<?php

namespace Asharif88\FilamentPlotly\Components;

use Illuminate\View\Component;

class Overlay extends Component
{
    public function __construct(
        public $chartOverlay,
    ) {}

    /**
     * Renders the view for the header component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament-plotly::widgets.components.overlay');
    }
}
