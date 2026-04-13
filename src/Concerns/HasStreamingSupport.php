<?php

namespace Asharif88\FilamentPlotly\Concerns;

trait HasStreamingSupport
{
    protected function getStreamUrl(): ?string
    {
        return null;
    }

    protected function getStreamParams(): array
    {
        return [];
    }

    /**
     * JS called per SSE message. Receives (d, el) where d is the parsed event
     * data and el is the chart DOM element. Return null to use the default
     * Plotly.restyle behaviour.
     */
    protected function getOnStreamMessageScript(): ?string
    {
        return null;
    }

    /**
     * JS called when the stream signals completion (d.done is truthy).
     * `el` (the chart DOM element) is available in scope.
     */
    protected function getOnStreamDoneScript(): ?string
    {
        return null;
    }
}
