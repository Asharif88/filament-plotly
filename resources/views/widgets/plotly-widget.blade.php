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
@endphp
<x-filament-widgets::widget class="fi-wi-chart filament-widgets-chart-widget filament-plotly-widget">
	<x-filament::section
			class="filament-plotly-section"
			:description="$subheading"
			:heading="$heading"
			:collapsible="$isCollapsible"
	>
		<div x-data="{ dropdownOpen: false }" @plotly-dropdown.window="dropdownOpen = $event.detail.open">
			@if ($filters || method_exists($this, 'getFiltersSchema'))
				<x-slot name="afterHeader">
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
			/>

			@if ($footer)
				<div class="relative">
					{!! $footer !!}
				</div>
			@endif
		</div>
	</x-filament::section>
</x-filament-widgets::widget>