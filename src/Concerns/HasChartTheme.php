<?php

namespace Asharif88\FilamentPlotly\Concerns;

trait HasChartTheme
{
    /**
     * Return Plotly layout properties to apply when the page is in dark mode.
     * These are merged into the current layout via Plotly.relayout whenever the
     * dark class is toggled on <html>.
     */
    protected function getDarkThemeLayout(): array
    {
        return [];
    }

    /**
     * Return Plotly layout properties to apply when the page is in light mode.
     * These are merged into the current layout via Plotly.relayout whenever the
     * dark class is toggled on <html>.
     */
    protected function getLightThemeLayout(): array
    {
        return [];
    }
}
