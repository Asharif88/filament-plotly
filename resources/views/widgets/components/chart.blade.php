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
    'onChartReadyScript' => null,
    'chartOverlay' => null,
    'streamingLayoutPatch' => [],
    'darkThemeLayout' => [],
    'lightThemeLayout' => [],
    'plotlyEventListeners' => [],
    'streamUrl' => null,
    'streamParams' => [],
    'onStreamMessageScript' => null,
    'onStreamDoneScript' => null,
])
@php
	$onChartReadyScript = method_exists($this, 'getOnChartReadyScript') ? $this->getOnChartReadyScript() : null;
	$onStreamMessageScript = method_exists($this, 'getOnStreamMessageScript') ? $this->getOnStreamMessageScript() : null;
	$onStreamDoneScript = method_exists($this, 'getOnStreamDoneScript') ? $this->getOnStreamDoneScript() : null;
@endphp

<div
		{!! $deferLoading ? ' wire:init="loadWidget" ' : '' !!}
		class="flex flex-col justify-center  filament-plotly-chart"
>
	<x-filament-plotly::before :beforeContent="$beforeContent" />
	@if ($readyToLoad)
		<div class="w-full filament-plotly-chart-container"
		     wire:ignore x-ignore x-load
		     x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-plotly', 'asharif88/filament-plotly') }}"
		     x-data="plotly({
			chartData: @js($chartData),
			chartLayout: @js($chartLayout),
			chartConfig: @js($chartConfig),
			chartId: '#{{ $chartId }}',
			streamingLayoutPatch: @js($streamingLayoutPatch),
			darkThemeLayout: @js($darkThemeLayout),
			lightThemeLayout: @js($lightThemeLayout),
			plotlyEventListeners: @js($plotlyEventListeners),
			streamUrl: @js($streamUrl),
			streamParams: @js($streamParams),
			onStreamMessageScript: @js($onStreamMessageScript),
			onStreamDoneScript: @js($onStreamDoneScript),
		})">
			<div wire:ignore class="filament-plotly-chart-object" x-ref="{{ $chartId }}" id="{{ $chartId }}"
			     style="position: relative; overflow:hidden;{{ 'height: '.$contentHeight.'px;' }}">
				<x-filament-plotly::overlay :chartOverlay="$chartOverlay" />
			</div>
		</div>
		<div {!! $pollingInterval ? 'wire:poll.' . $pollingInterval . '="updateChartData"' : '' !!} x-data="{}"
		     x-init="$watch('dropdownOpen', value => $wire.dropdownOpen = value)">
		</div>

		@if ($onChartReadyScript)
			<div x-data x-init="
			const _el = document.getElementById('{{ $chartId }}');
			const _fn = new Function('el', {{ json_encode($onChartReadyScript) }});
			let tries = 0;
			const _tid = setInterval(() => {
				if ((_el && _el._fullLayout) || ++tries > 100) {
					clearInterval(_tid);
					if (_el && _el._fullLayout) _fn(_el);
				}
			}, 100);
			return () => clearInterval(_tid);
		"></div>
		@endif
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