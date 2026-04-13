<?php

namespace Asharif88\FilamentPlotly\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasChartOverlay
{
    protected static ?string $chartOverlay = null;

    /**
     * Retrieves the Content used in the class.
     */
    protected function getChartOverlay(): null | string | Htmlable | View
    {
        return static::$chartOverlay;
    }
}
