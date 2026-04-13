<?php

namespace Asharif88\FilamentPlotly\Widgets;

use Asharif88\FilamentPlotly\Concerns\CanDeferLoading;
use Asharif88\FilamentPlotly\Concerns\CanFilter;
use Asharif88\FilamentPlotly\Concerns\HasBeforeContent;
use Asharif88\FilamentPlotly\Concerns\HasChartOverlay;
use Asharif88\FilamentPlotly\Concerns\HasContentHeight;
use Asharif88\FilamentPlotly\Concerns\HasFooter;
use Asharif88\FilamentPlotly\Concerns\HasHeader;
use Asharif88\FilamentPlotly\Concerns\HasLoadingIndicator;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PlotlyWidget extends Widget implements HasSchemas
{
    use CanDeferLoading;
    use CanFilter;
    use CanPoll;
    use HasBeforeContent;
    use HasChartOverlay;
    use HasContentHeight;
    use HasFooter;
    use HasHeader;
    use HasLoadingIndicator;
    use InteractsWithSchemas;

    protected static ?string $chartId = null;

    // @phpstan-ignore-next-line
    protected string $view = 'filament-plotly::widgets.plotly-widget';

    protected ?array $chartData = null;

    public ?array $chartConfig = null;

    public ?array $chartLayout = null;

    public function mount(): void
    {
        if (method_exists($this, 'getFiltersSchema')) {
            $this->getFiltersSchema()->fill();
        }

        if (! $this->getDeferLoading()) {
            $this->chartData = $this->getChartData();
            $this->chartConfig = $this->getChartConfig();
            $this->chartLayout = $this->getChartLayout();
            $this->readyToLoad = true;
        }
    }

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

    /**
     * Return plain JS to execute once the Plotly chart is fully initialised.
     * The chart DOM element is available as `el` inside the script.
     * Override in subclasses to replace the tryAttach polling pattern.
     */
    protected function getOnChartReadyScript(): ?string
    {
        return null;
    }

    //    /**
    //     * Return HTML/Blade content to render *inside* the chart's wire:ignore
    //     * container. Useful for overlay loaders and annotation panels that need to
    //     * sit on top of the chart without absolute-positioning hacks.
    //     */
    //    protected function getChartOverlayContent(): null | string | Htmlable | View
    //    {
    //        return null;
    //    }

    /**
     * Return Plotly layout properties to merge before every stream reset
     * (Plotly.react call at the start of a new SSE stream). This prevents the
     * class of bug where relayout-applied properties are lost when the chart
     * is re-rendered with a fresh layout object.
     */
    protected function getStreamingLayoutPatch(): array
    {
        return [];
    }

    /**
     * Map Plotly JS event names to public methods on this widget.
     *
     * Each entry fires two things when the Plotly event occurs:
     *   1. $wire.method(payload)  — calls the mapped method on this widget directly.
     *   2. window event "plotly:{event}" — so sibling Livewire components (e.g. a
     *      table) can react with #[On('plotly:plotly_click')] without coupling to
     *      this widget.
     *
     * Only safe, serialisable fields are forwarded (x, y, z, text, customdata,
     * curveNumber, pointIndex, traceName). Store your record ID in `customdata`
     * when building trace data and read it back here.
     *
     * Supported events: plotly_click, plotly_hover, plotly_unhover,
     *   plotly_selected, plotly_deselect, plotly_relayout, plotly_restyle,
     *   plotly_doubleclick, plotly_legendclick, plotly_legenddoubleclick.
     *
     * Example:
     *   return ['plotly_click' => 'onChartClick'];
     *
     *   public function onChartClick(array $data): void
     *   {
     *       $recordId = $data['points'][0]['customdata'] ?? null;
     *       // open slide-over, update table, etc.
     *   }
     */
    protected function getPlotlyEventListeners(): array
    {
        return [];
    }

    /**
     * Return header actions for the widget. Override in your widget to provide actions.
     * Should return an array of Filament\Actions\Action (and/or ActionGroup) instances.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Return optional alignment for header actions. Override to change alignment.
     */
    protected function getHeaderActionsAlignment(): ?Alignment
    {
        return null;
    }

    /**
     * Return footer actions for the widget. Override in your widget to provide actions.
     * Should return an array of Filament\Actions\Action (and/or ActionGroup) instances.
     */
    protected function getFooterActions(): array
    {
        return [];
    }

    /**
     * Return optional alignment for footer actions. Override to change alignment.
     */
    protected function getFooterActionsAlignment(): ?Alignment
    {
        return null;
    }

    public function updateChartData(): void
    {
        if ($this->chartData !== $this->getChartData()) {

            $this->chartData = $this->getChartData();
            if (! $this->dropdownOpen) {
                $this
                    ->dispatch('updateChartData', chartData: $this->chartData)
                    ->self();
            }
        }
    }
}
