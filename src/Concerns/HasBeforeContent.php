<?php

namespace Asharif88\FilamentPlotly\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasBeforeContent
{
    protected static ?string $beforeContent = null;

    /**
     * Retrieves the Content used in the class.
     */
    protected function getBeforeContent(): null | string | Htmlable | View
    {
        return static::$beforeContent;
    }
}
