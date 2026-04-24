@php
	$plugin = (function_exists('filament') && filament()->isServing()) ? \Asharif88\FilamentPlotly\FilamentPlotlyPlugin::get() : null;
	$heading = $this->getHeading();
	$subheading = $this->getSubheading();
	$beforeContent = $this->getBeforeContent();
	$filters = $this->getFilters();
	$isCollapsible = $this->isCollapsible();
	$width = $this->getFilterFormWidth();
	$pollingInterval = $this->getPollingInterval();
	$chartId = $this->getChartId();
	$chartData = $this->getChartData();
	$chartLayout = $this->getChartLayout();
	$chartConfig = $this->getChartConfig();
	$loadingIndicator = $this->getLoadingIndicator();
	$contentHeight = $this->getContentHeight();
	$deferLoading = $this->getDeferLoading();
	$footer = $this->getFooter();
	$readyToLoad = $this->readyToLoad;

	// New: header/footer actions
	$headerActions = method_exists($this, 'getHeaderActions') ? $this->getHeaderActions() : [];
	$headerActionsAlignment = method_exists($this, 'getHeaderActionsAlignment') ? $this->getHeaderActionsAlignment() : null;
	$footerActions = method_exists($this, 'getFooterActions') ? $this->getFooterActions() : [];
	$footerActionsAlignment = method_exists($this, 'getFooterActionsAlignment') ? $this->getFooterActionsAlignment() : null;

	// New chart hooks
	$chartOverlay = method_exists($this, 'getChartOverlay') ? $this->getChartOverlay() : null;
	$streamingLayoutPatch = method_exists($this, 'getStreamingLayoutPatch') ? $this->getStreamingLayoutPatch() : [];
	$darkThemeLayout = method_exists($this, 'getDarkThemeLayout') ? $this->getDarkThemeLayout() : [];
	$lightThemeLayout = method_exists($this, 'getLightThemeLayout') ? $this->getLightThemeLayout() : [];
	$plotlyEventListeners = method_exists($this, 'getPlotlyEventListeners') ? $this->getPlotlyEventListeners() : [];

	// HasStreamingSupport
	$streamUrl = method_exists($this, 'getStreamUrl') ? $this->getStreamUrl() : null;
	$streamParams = method_exists($this, 'getStreamParams') ? $this->getStreamParams() : [];
@endphp
<x-filament-widgets::widget wire:replace class="fi-wi-chart filament-widgets-chart-widget filament-plotly-widget">
	<x-filament::section
			class="filament-plotly-section"
			:description="$subheading"
			:heading="$heading"
			:collapsible="$isCollapsible"
	>
		<div x-data="{ dropdownOpen: false }" @plotly-dropdown.window="dropdownOpen = $event.detail.open">
			@if ($filters || method_exists($this, 'getFiltersSchema') || count($headerActions))
				<x-slot name="afterHeader">
					{{-- Existing filter controls --}}
					@if ($filters)
						<x-filament::input.wrapper
								inline-prefix
								wire:target="filter"
								class="fi-wi-chart-filter"
						>
							<x-filament::input.select
									inline-prefix
									wire:model.live="filter"
							>
								@foreach ($filters as $value => $label)
									<option value="{{ $value }}">
										{{ $label }}
									</option>
								@endforeach
							</x-filament::input.select>
						</x-filament::input.wrapper>
					@endif

					@if (method_exists($this, 'getFiltersSchema'))
						<x-filament::dropdown
								placement="bottom-end"
								shift
								width="xs"
								class="fi-wi-chart-filter"
						>
							<x-slot name="trigger">
								{{ $this->getFiltersTriggerAction() }}
							</x-slot>

							<div class="fi-wi-chart-filter-content">
								{{ $this->getFiltersSchema() }}
							</div>
						</x-filament::dropdown>
					@endif

					{{-- Header actions --}}
					@if (count($headerActions))
						<x-filament::actions :actions="$headerActions" :alignment="$headerActionsAlignment" />
					@endif
				</x-slot>
			@endif


			<x-filament-plotly::chart
					:$chartId
					:$chartData
					:$chartConfig
					:$chartLayout
					:$contentHeight
					:$pollingInterval
					:$loadingIndicator
					:$deferLoading
					:$readyToLoad
					:$beforeContent
					:$chartOverlay
					:$streamingLayoutPatch
					:$darkThemeLayout
					:$lightThemeLayout
					:$plotlyEventListeners
					:$streamUrl
					:$streamParams
			/>

			@if ($footer || count($footerActions))
				<div class="relative">
					@if (count($footerActions))
						<x-filament::actions :actions="$footerActions" :alignment="$footerActionsAlignment" />
					@endif

					@if ($footer)
						<div class="mt-2">
							{!! $footer !!}
						</div>
					@endif
				</div>
			@endif
		</div>
	</x-filament::section>
	<x-filament-actions::modals />
</x-filament-widgets::widget>
