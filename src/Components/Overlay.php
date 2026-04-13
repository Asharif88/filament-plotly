<?php

namespace Asharif88\FilamentPlotly\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Overlay extends Component
{
    public function __construct(
        public $chartOverlay,
    ) {}

    /**
     * Renders the view for the header component.
     */
    public function render(): View
    {
        return view('filament-plotly::widgets.components.overlay');
    }
}
