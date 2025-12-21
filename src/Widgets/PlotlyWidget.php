<?php

namespace Asharif88\FilamentPlotly\Widgets;

use Asharif88\FilamentPlotly\Concerns\CanDeferLoading;
use Asharif88\FilamentPlotly\Concerns\CanFilter;
use Asharif88\FilamentPlotly\Concerns\HasContentHeight;
use Asharif88\FilamentPlotly\Concerns\HasFooter;
use Asharif88\FilamentPlotly\Concerns\HasHeader;
use Asharif88\FilamentPlotly\Concerns\HasLoadingIndicator;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PlotlyWidget extends Widget implements HasSchemas
{
    use CanDeferLoading;
    use CanFilter;
    use CanPoll;
    use HasContentHeight;
    use HasFooter;
    use HasHeader;
    use HasLoadingIndicator;
    use InteractsWithSchemas;

    protected static ?string $chartId = null;

    // @phpstan-ignore-next-line
    protected string $view = 'filament-plotly::widgets.plotly-widget';

    public ?array $chartData = null;

    public ?array $chartConfig = null;

    public ?array $chartLayout = null;

    public function mount(): void
    {
        if (method_exists($this, 'getFiltersSchema')) {
            $this->getFiltersSchema()->fill();
        }

        $this->chartData = $this->getChartData();
        $this->chartConfig = $this->getChartConfig();
        $this->chartLayout = $this->getChartLayout();

        if (! $this->getDeferLoading()) {
            $this->readyToLoad = true;
        }
    }

    public function on(): void {}

    public function render(): View
    {
        return view($this->view, []);
    }

    protected function getChartId(): ?string
    {
        return static::$chartId ?? 'plotly_' . Str::random(10);
    }

    protected function getChartData(): array
    {
        return [];
    }

    protected function getChartConfig(): array
    {
        return [];
    }

    protected function getChartLayout(): array
    {
        return [];
    }

    public function updateOptions(): void
    {
        if ($this->chartData !== $this->getChartData()) {

            $this->chartData = $this->getChartData();
            if (! $this->dropdownOpen) {
                $this
                    ->dispatch('updateOptions', options: $this->chartData)
                    ->self();
            }
        }
    }
}
