# Filament Plotly.js Widget
Inspired by [Leandro Ferreira’s Apex Charts plugin](https://filamentphp.com/plugins/leandrocfe-apex-charts) & [Elemind's Echarts plugin](https://filamentphp.com/plugins/elemind-echarts) this plugin delivers plotly.js integration for Filament.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asharif88/filament-plotly.svg?style=flat-square)](https://packagist.org/packages/asharif88/filament-plotly)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/asharif88/filament-plotly/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/asharif88/filament-plotly/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/asharif88/filament-plotly/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/asharif88/filament-plotly/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/asharif88/filament-plotly.svg?style=flat-square)](https://packagist.org/packages/asharif88/filament-plotly)

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
  - [Setting a widget title](#setting-a-widget-title)
  - [Setting a widget subheading](#setting-a-widget-subheading)
  - [Setting a chart id](#setting-a-chart-id)
  - [Making a widget collapsible](#making-a-widget-collapsible)
  - [Setting a widget height](#setting-a-widget-height)
  - [Setting a widget footer](#setting-a-widget-footer)
  - [Header & Footer Actions](#header--footer-actions)
  - [Hiding header content](#hiding-header-content)
  - [Filtering chart data](#filtering-chart-data)
  - [Live updating (polling)](#live-updating-polling)
  - [Defer loading](#defer-loading)
  - [Loading indicator](#loading-indicator)
  - [Chart overlay](#chart-overlay)
  - [Post-init JS hook](#post-init-js-hook)
  - [Dark / light theme sync](#dark--light-theme-sync)
  - [Plotly event listeners](#plotly-event-listeners)
  - [Streaming support](#streaming-support)
  - [Streaming layout patch](#streaming-layout-patch)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require asharif88/filament-plotly
```

Register the plugin for the Filament Panels you want to use:

```php
use Asharif88\FilamentPlotly\FilamentPlotlyPlugin;
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentPlotlyPlugin::make()
        ]);
}
```

## Usage

Start by creating a widget with the command:

```bash
php artisan make:filament-plotly BlogPostsChart
```

The plugin uses the [Plotly.react](https://plotly.com/javascript/plotlyjs-function-reference/#plotlyreact) function to render charts.
This function takes in the chart `data`, `layout` and `config` as parameters.

You need to implement the `getChartData()` method to return an array with `data`, `layout` and `config` keys:
```php
use Asharif88\FilamentPlotly\Widgets\PlotlyWidget;

class BlogPostsChart extends PlotlyWidget 
{
    protected function getChartData(): array
    {
        return [
            'data' => [
                [
                    'x' => ['2025-07-01', '2025-07-02', '2025-07-03', '2025-07-04', '2025-07-05'],
                    'y' => [10, 15, 13, 17, 22],
                    'type' => 'scatter',
                    'mode' => 'lines+markers',
                    'name' => 'Blog Posts',
                ],
            ],
            'layout' => [
                'title' => 'Blog Posts Over Time',
                'xaxis' => [
                    'title' => 'Date',
                ],
                'yaxis' => [
                    'title' => 'Number of Posts',
                ],
            ],
            'config' => [
                'responsive' => true,
            ],
        ];
    }
}
```

Alternatively, you can set the `data`, `layout` and `config` separately by implementing the following methods:

```php
protected function getChartData(): array
{
    return [
        [
            'x' => ['2025-07-01', '2025-07-02', '2025-07-03', '2025-07-04', '2025-07-05'],
            'y' => [10, 15, 13, 17, 22],
            'type' => 'scatter',
            'mode' => 'lines+markers',
            'name' => 'Blog Posts',
        ],
    ]; 
}

protected function getChartLayout(): array
{
    return [
        'title' => 'Blog Posts Over Time',
        'xaxis' => [
            'title' => 'Date',
        ],
        'yaxis' => [
            'title' => 'Number of Posts',
        ],
    ];
}

protected function getChartConfig(): array
{
    return [
        'responsive' => true,
    ];
}
```

## Setting a widget title

You may set a widget title:

```php
protected static ?string $heading = 'Blog Posts Chart';
```

Optionally, you can use the `getHeading()` method.

## Setting a widget subheading

You may set a widget subheading:

```php
protected static ?string $subheading = 'This is a subheading';
```

Optionally, you can use the `getSubheading()` method.

## Adding custom content

You can add custom content before chart within the widget container using the `getbeforeContent()` method.

```php
public function getBeforeContent(): null|string|Htmlable|View
{
    return '...';
}
```

## Setting a chart id

You may set a chart id:

```php
protected static string $chartId = 'blogPostsChart';
```

## Making a widget collapsible

You may set a widget to be collapsible:

```php
protected static bool $isCollapsible = true;
```

You can also use the `isCollapsible()` method:

```php
protected function isCollapsible(): bool
{
    return true;
}
```

## Setting a widget height

By default, the widget height is set to `300px`. You may set a custom height:

```php
protected static ?int $contentHeight = 400; //px
```

Optionally, you can use the `getContentHeight()` method.

```php
protected function getContentHeight(): ?int
{
    return 400;
}
```

## Setting a widget footer

You may set a widget footer:

```php
protected static ?string $footer = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
```

You can also use the `getFooter()` method:

Custom view:

```php
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

protected function getFooter(): null|string|Htmlable|View
{
    return view('custom-footer', ['text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.']);
}
```

```html
<!--resources/views/custom-footer.blade.php-->
<div>
    <p class="text-danger-500">{{ $text }}</p>
</div>
```

Html string:

```php
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
protected function getFooter(): null|string|Htmlable|View
{
    return new HtmlString('<p class="text-danger-500">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>');
}
```

## Header & Footer Actions

You can register Filament actions to appear in the widget header or footer. Override `getHeaderActions()` / `getFooterActions()` on your widget to return an array of `Filament\Actions\Action` (or `ActionGroup`) instances. These actions are rendered by Filament's actions component and respect alignment settings.

Example — simple header actions:

```php
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;

protected function getHeaderActions(): array
{
    return [
        Action::make('refresh')
            ->label('Refresh')
            ->icon('heroicon-o-refresh')
            ->action('updateOptions')
            ->button(),

        Action::make('download')
            ->label('Download')
            ->url(route('reports.export'))
            ->color('secondary'),
    ];
}

protected function getHeaderActionsAlignment(): ?Alignment
{
    return Alignment::End; // align header actions to the right
}
```

Example — footer actions:

```php
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;

protected function getFooterActions(): array
{
    return [
        Action::make('details')
            ->label('Details')
            ->url(route('reports.details'))
            ->button(),
    ];
}

protected function getFooterActionsAlignment(): ?Alignment
{
    return Alignment::Center; // center footer actions
}
```

Notes:

- You may return `ActionGroup` instances if you need grouped or dropdown actions.
- Header actions are rendered next to any filter controls defined on the widget.
- Footer actions render above the footer content returned by `getFooter()`.

## Hiding header content

You can hide header content by **NOT** providing these

- $heading
- getHeading()
- $subheading
- getSubheading()
- getOptions()

## Filtering chart data

You can set up chart filters to change the data shown on chart. Commonly, this is used to change the time period that
chart data is rendered for.

### Filter schema

You may use components from the [Schemas](https://filamentphp.com/docs/4.x/schemas/overview#available-components) to
create custom filters.
You need to use `HasFiltersSchema` trait and implement the `filtersSchema()` method to define the filter form schema:


```php
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;  
use Asharif88\FilamentPlotly\Widgets\PlotlyWidget;

class BlogPostsChart extends PlotlyWidget 
{
    use HasFiltersSchema;
    
    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->default('Blog Posts Chart'),
                
            DatePicker::make('date_start')  
                ->default('2025-07-01'),
    
            DatePicker::make('date_end')
                ->default('2025-07-31'),
        ]);
    }
    
    /**
    * Use this method to update the chart options when the filter form is submitted.
    */
    public function updatedInteractsWithSchemas(string $statePath): void
    {
        $this->updateOptions();
    }
}
```

The data from the custom filter is available in the `$this->filters` array. You can use the active filter values within
your `getChartData()` method:

```php
protected function getChartData(): array
{
    $title = $this->filters['title'];
    $dateStart = $this->filters['date_start'];
    $dateEnd = $this->filters['date_end'];

    return [
        //chart options
    ];
}
```

### Single select

To set a default filter value, set the `$filter` property:

```php
public ?string $filter = 'today';
```

Then, define the `getFilters()` method to return an array of values and labels for your filter:

```php
protected function getFilters(): ?array
{
    return [
        'today' => 'Today',
        'week' => 'Last week',
        'month' => 'Last month',
        'year' => 'This year',
    ];
}
```

You can use the active filter value within your `getOptions()` method:

```php
protected function getOptions(): array
{
    $activeFilter = $this->filter;

    return [
        //chart options
    ];
}
```

## Live updating (polling)

By default, chart widgets refresh their data every 5 seconds.

To customize this, you may override the `$pollingInterval` property on the class to a new interval:

```php
protected static ?string $pollingInterval = '10s';
```

Alternatively, you may disable polling altogether:

```php
protected static ?string $pollingInterval = null;
```

## Defer loading

This can be helpful when you have slow queries and you don't want to hold up the entire page load:

```php
protected static bool $deferLoading = true;

protected function getChartData(): array
{
    //showing a loading indicator immediately after the page load
    if (!$this->readyToLoad) {
        return [];
    }

    //slow query
    sleep(2);

    return [
        //chart options
    ];
}
```

## Loading indicator

You can change the loading indicator:

```php
protected static ?string $loadingIndicator = 'Loading...';
```

You can also use the `getLoadingIndicator()` method:

```php
use Illuminate\Contracts\View\View;
protected function getLoadingIndicator(): null|string|View
{
    return view('custom-loading-indicator');
}
```

```html
<!--resources/views/custom-loading-indicator.blade.php-->
<div>
    <p class="text-danger-500">Loading...</p>
</div>
```

## Chart overlay

Use `getChartOverlay()` to render HTML **inside** the chart container — directly on top of the Plotly canvas. This is the right place for progress bars, custom annotation panels, and loader overlays, because the content sits naturally inside the chart div without requiring `document.getElementById` or absolute-positioning tricks.

```php
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

protected function getChartOverlay(): null|string|Htmlable|View
{
    return view('charts.my-overlay');
}
```

```html
{{-- resources/views/charts/my-overlay.blade.php --}}
<div id="stream-progress"
     style="position:absolute;top:0;left:0;right:0;display:none"
     class="h-1 bg-primary-500">
</div>
```

> The overlay is rendered inside a `wire:ignore` container, so it is set once on mount and then controlled by JavaScript. It will not be re-rendered by Livewire on subsequent updates.

## Post-init JS hook

Override `getOnChartReadyScript()` to run plain JavaScript the moment the Plotly chart is fully initialised. Inside the script, `el` refers to the chart DOM element.

```php
protected function getOnChartReadyScript(): ?string
{
    return <<<'JS'
        // `el` is the Plotly chart DOM element
        el.on('plotly_afterplot', () => {
            document.getElementById('stream-progress').style.display = 'none';
        });
    JS;
}
```

## Dark / light theme sync

Use the `HasChartTheme` concern to keep Plotly's visual theme in sync with Filament's dark/light mode automatically. The Alpine component watches the `dark` class on `<html>` via a `MutationObserver` and calls `Plotly.relayout()` whenever the mode changes — no `getFooter()` workaround needed.

```php
use Asharif88\FilamentPlotly\Concerns\HasChartTheme;
use Asharif88\FilamentPlotly\Widgets\PlotlyWidget;

class RevenueChart extends PlotlyWidget
{
    use HasChartTheme;

    protected function getDarkThemeLayout(): array
    {
        return [
            'paper_bgcolor' => '#1e293b',
            'plot_bgcolor'  => '#1e293b',
            'font'          => ['color' => '#f1f5f9'],
        ];
    }

    protected function getLightThemeLayout(): array
    {
        return [
            'paper_bgcolor' => '#ffffff',
            'plot_bgcolor'  => '#ffffff',
            'font'          => ['color' => '#0f172a'],
        ];
    }
}
```

The layout properties returned by each method are merged into the live chart layout via `Plotly.relayout()`. Return any valid [Plotly layout](https://plotly.com/javascript/reference/layout/) keys.

## Plotly event listeners

Override `getPlotlyEventListeners()` to map Plotly JS events to public methods on your widget. When a mapped event fires, two things happen:

1. The mapped method is called on this widget with a serialised payload.
2. A window-level Alpine event `plotly:{event}` is dispatched so that **sibling Livewire components** (a table, a slide-over) can also react without coupling to the chart widget.

```php
protected function getPlotlyEventListeners(): array
{
    return [
        'plotly_click'    => 'onChartClick',
        'plotly_selected' => 'onChartSelected',
    ];
}

public function onChartClick(array $data): void
{
    $recordId = $data['points'][0]['customdata'] ?? null;

    $this->dispatch('open-record', id: $recordId);
}

public function onChartSelected(array $data): void
{
    $ids = collect($data['points'])->pluck('customdata')->filter()->all();

    $this->dispatch('filter-table', ids: $ids);
}
```

Store the record's primary key in `customdata` when building trace data — that's the standard Plotly mechanism for carrying arbitrary metadata per point:

```php
protected function getChartData(): array
{
    $rows = Order::query()->get();

    return [[
        'x'          => $rows->pluck('created_at'),
        'y'          => $rows->pluck('total'),
        'customdata' => $rows->pluck('id'),   // ← record IDs travel with each point
        'type'       => 'scatter',
        'mode'       => 'markers',
    ]];
}
```

To react from a **separate** Livewire component (e.g. a `ListOrders` resource page), listen for the broadcasted window event:

```php
use Livewire\Attributes\On;

#[On('plotly:plotly_click')]
public function handleChartClick(array $data): void
{
    $this->tableFilters['id'] = $data['points'][0]['customdata'];
    $this->resetTable();
}
```

### Supported events and payload shapes

| Event | Payload fields |
|---|---|
| `plotly_click`, `plotly_hover`, `plotly_unhover`, `plotly_doubleclick` | `points[]` → `{curveNumber, pointIndex, x, y, z, text, customdata, traceName}` |
| `plotly_selected`, `plotly_deselect` | `points[]` + `range`, `lassoPoints` |
| `plotly_legendclick`, `plotly_legenddoubleclick` | `{curveNumber, traceName}` |
| `plotly_relayout`, `plotly_restyle`, `plotly_autosize` | raw layout/style change object |

DOM nodes, full trace objects, and axis definitions are stripped before serialisation — only safe, JSON-friendly values reach Livewire.

## Streaming support

The `HasStreamingSupport` concern replaces the traditional `getFooter()` SSE boilerplate with a small set of override methods. The library owns the `EventSource` lifecycle, the progress overlay, and component cleanup — your widget only declares what is domain-specific.

### SSE message protocol

Your server-side stream must emit JSON messages in this format:

| Message | Meaning |
|---|---|
| `{"init": true, "total": N}` | Stream is starting; `N` is the total number of data messages expected (used for the progress bar). |
| `{"done": true}` | Stream finished. The library closes the `EventSource` and removes the progress overlay. |
| `{ ...data }` | A data point. Forwarded to `getOnStreamMessageScript()`. |

### Basic example

```php
use Asharif88\FilamentPlotly\Concerns\HasStreamingSupport;
use Asharif88\FilamentPlotly\Widgets\PlotlyWidget;

class LiveDataChart extends PlotlyWidget
{
    use HasStreamingSupport;

    public int $sourceId = 1;

    // Return null to disable streaming (e.g. before required state is set)
    protected function getStreamUrl(): ?string
    {
        return url('/stream/data');
    }

    // Query-string params appended to the URL on both initial load and every restart
    protected function getStreamParams(): array
    {
        return ['source_id' => $this->sourceId];
    }

    // JS body called for each data message. `d` = parsed JSON, `el` = chart DOM element.
    // Return null to use the built-in default: Plotly.extendTraces(el, {x:[[d.x]], y:[[d.y]]}, [0])
    protected function getOnStreamMessageScript(): ?string
    {
        return <<<'JS'
            Plotly.extendTraces(el, { x: [[d.ts]], y: [[d.value]] }, [0]);
        JS;
    }

    // JS body called once when {done: true} arrives, after the overlay is removed.
    // `el` is the chart DOM element. Return null if no post-stream work is needed.
    protected function getOnStreamDoneScript(): ?string
    {
        return null;
    }

    protected function getChartData(): array
    {
        return [['x' => [], 'y' => [], 'type' => 'scatter', 'mode' => 'lines']];
    }
}
```


> Returning `null` from `getStreamUrl()` disables streaming entirely — useful when required state (e.g. a selected source) has not been set yet.

### Full example with theme and click events

The following shows `HasStreamingSupport`, `HasChartTheme`, and `getPlotlyEventListeners()` working together, which is the recommended pattern for a production streaming widget:

```php
use Asharif88\FilamentPlotly\Concerns\HasChartTheme;
use Asharif88\FilamentPlotly\Concerns\HasStreamingSupport;
use Asharif88\FilamentPlotly\Widgets\PlotlyWidget;
use Livewire\Attributes\On;

class DailyVariationChart extends PlotlyWidget
{
    use HasStreamingSupport;
    use HasChartTheme;

    protected ?string $pollingInterval = null;
    protected static ?string $chartId  = 'dailyVariationChart';
    protected static int $contentHeight = 660;

    public ?int $sourceId  = null;
    public ?string $dateFrom = null;
    public ?string $dateTo   = null;

    // --- Filter handlers -----------------------------------------------------

    #[On('sourceSelected')]
    public function onSourceSelected(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    #[On('dateRangeChanged')]
    public function onDateRangeChanged(?string $from, ?string $to): void
    {
        $this->dateFrom = $from;
        $this->dateTo   = $to;
    }

    // --- Chart data ----------------------------------------------------------

    // Starts empty — data is streamed in via extendTraces
    protected function getChartData(): array
    {
        return [[
            'x'          => [],
            'y'          => [],
            'customdata' => [],
            'mode'       => 'lines+markers',
            'line'       => ['width' => 2, 'color' => 'orange'],
            'marker'     => ['size' => 6, 'color' => 'orange'],
            'showlegend' => false,
        ]];
    }

    protected function getChartLayout(): array
    {
        return [
            'title'      => ['text' => 'Variations per day'],
            'yaxis'      => ['type' => 'category', 'autorange' => 'reversed'],
            'autosize'   => true,
            'showlegend' => false,
        ];
    }

    protected function getChartConfig(): array
    {
        return ['responsive' => true];
    }

    // --- HasStreamingSupport -------------------------------------------------

    protected function getStreamUrl(): ?string
    {
        return $this->sourceId ? url('/stream/data') : null;
    }

    protected function getStreamParams(): array
    {
        return [
            'source_id' => $this->sourceId,
            'date_from' => $this->dateFrom ?? '',
            'date_to'   => $this->dateTo   ?? '',
        ];
    }

    // Expected message shape: { x: <date>, y: <float>, payloadId: <int> }
    protected function getOnStreamMessageScript(): ?string
    {
        return <<<'JS'
            Plotly.extendTraces(el, {
                x:          [[d.x]],
                y:          [[d.y]],
                customdata: [[d.payloadId]],
            }, [0]);
        JS;
    }

    // --- HasChartTheme — automatic dark/light sync ---------------------------

    protected function getDarkThemeLayout(): array
    {
        return [
            'paper_bgcolor' => '#1e293b',
            'plot_bgcolor'  => '#1e293b',
            'font'          => ['color' => '#f1f5f9'],
        ];
    }

    protected function getLightThemeLayout(): array
    {
        return [
            'paper_bgcolor' => '#ffffff',
            'plot_bgcolor'  => '#ffffff',
            'font'          => ['color' => '#0f172a'],
        ];
    }

    // --- Plotly event listeners ----------------------------------------------

    protected function getPlotlyEventListeners(): array
    {
        return ['plotly_click' => 'onChartClick'];
    }

    public function onChartClick(array $data): void
    {
        $payloadId = $data['points'][0]['customdata'] ?? null;

        if ($payloadId !== null) {
            $this->dispatch('payloadSelected', payloadId: (int) $payloadId);
        }
    }
}
```

## Streaming layout patch

Override `getStreamingLayoutPatch()` to declare layout properties that must be force-merged into the layout on every stream reset, regardless of what `el.layout` currently holds.

```php
protected function getStreamingLayoutPatch(): array
{
    return [
        'template' => [
            'layout' => [
                'paper_bgcolor' => '#1e293b',
                'plot_bgcolor'  => '#1e293b',
            ],
        ],
    ];
}
```

> **When to use this vs `HasChartTheme`:** If you use `HasChartTheme`, theme properties are tracked in `el.layout` and are automatically preserved across stream resets — you do not need `getStreamingLayoutPatch()` for theme sync. Use `getStreamingLayoutPatch()` when you need to inject layout properties that are not managed by `HasChartTheme`, or when you cannot use that trait.

## Dark mode

The dark mode is supported and enabled by default for the container.

## Publishing views

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-plotly-views"
```

## Publishing translations

Optionally, you can publish the translations using:

```bash
php artisan vendor:publish --tag=filament-plotly-translations
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Ahmad SHARIF](https://github.com/Asharif88)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
