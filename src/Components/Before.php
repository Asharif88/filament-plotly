<?php

namespace Asharif88\FilamentPlotly\Components;

use Illuminate\View\Component;

class Before extends Component
{
    public function __construct(
        public $beforeContent,
    ) {}

    /**
     * Renders the view for the header component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament-plotly::widgets.components.before');
    }
}
