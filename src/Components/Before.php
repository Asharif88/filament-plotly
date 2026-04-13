<?php

namespace Asharif88\FilamentPlotly\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Before extends Component
{
    public function __construct(
        public $beforeContent,
    ) {}

    /**
     * Renders the view for the header component.
     */
    public function render(): View
    {
        return view('filament-plotly::widgets.components.before');
    }
}
