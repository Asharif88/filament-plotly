<?php

namespace Asharif88\FilamentPlotly\Components;

use Illuminate\View\Component;

class Chart extends Component
{
    public function __construct(
        public $chartId,
        public $chartData,
        public $chartLayout,
        public $chartConfig,
        public $contentHeight,
        public $pollingInterval,
        public $loadingIndicator,
        public $deferLoading,
        public $readyToLoad,
        public $beforeContent,
    ) {}

    /**
     * Renders a view for the chart component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament-plotly::widgets.components.chart');
    }
}
