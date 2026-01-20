@props([
    'chartId',
    'chartLayout',
    'chartData',
    'chartConfig',
    'contentHeight',
    'pollingInterval',
    'loadingIndicator',
    'deferLoading',
    'readyToLoad',
    'beforeContent',
])

<div
		{!! $deferLoading ? ' wire:init="loadWidget" ' : '' !!}
		class="flex items-center justify-center filament-plotly-chart"
>
	<x-filament-plotly::before :beforeContent="$beforeContent" />
	@if ($readyToLoad)
		<div class="w-full filament-plotly-chart-container"
			 x-ignore x-load
		 x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-plotly', 'asharif88/filament-plotly') }}"
		 x-data="plotly({
			chartData: @js($chartData),
			chartLayout: @js($chartLayout),
			chartConfig: @js($chartConfig),
			chartId: '#{{ $chartId }}'
		})">
			<div wire:ignore class="filament-plotly-chart-object" x-ref="{{ $chartId }}" id="{{ $chartId }}"
				 style="position: relative; overflow:hidden;{{ 'height: '.$contentHeight.'px;' }}">
			</div>
		</div>
		<div {!! $pollingInterval ? 'wire:poll.' . $pollingInterval . '="updateOptions"' : '' !!} x-data="{}"
			 x-init="$watch('dropdownOpen', value => $wire.dropdownOpen = value)">
		</div>
	@else
		<div class="filament-plotly-chart-loading-indicator m-auto">
			@if ($loadingIndicator)
				{!! $loadingIndicator !!}
			@else
				<x-filament::loading-indicator class="h-7 w-7 text-gray-500 dark:text-gray-400" wire:loading.delay />
			@endif
		</div>
	@endif
</div>