@props(['width'])

@php
	use Filament\Support\Enums\Width;
@endphp

<div class="filament-plotly-filter-form relative">
	<div class="filament-dropdown-trigger cursor-pointer flex items-center justify-end" aria-expanded="false">
		<button type="button" @click="dropdownOpen = !dropdownOpen"
				class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70 h-9 w-9 text-gray-400 hover:text-gray-500 focus:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus:ring-primary-500 fi-ac-icon-btn-action"
				title="Filter">

            <span class="sr-only">
                Filter
            </span>

			<x-filament::icon icon="heroicon-s-funnel" class="h-5 w-5" />

		</button>
	</div>

	<div x-show="dropdownOpen" x-cloak @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10"></div>

	<div x-show="dropdownOpen" x-cloak @class([
        'absolute mt-2 z-20 w-screen divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 transition dark:divide-gray-700 dark:bg-gray-800 dark:ring-white/20',
    ])
	style="{{ match ($width) {
            Width::ExtraSmall, 'xs' => 'width: 20rem;',
            Width::Small, 'sm' => 'width: 24rem;',
            Width::Medium, 'md' => 'width: 28rem;',
            Width::Large, 'lg' => 'width: 32rem;',
            Width::ExtraLarge, 'xl' => 'width: 36rem;',
            Width::TwoExtraLarge, '2xl' => 'width: 42rem;',
            Width::ThreeExtraLarge, '3xl' => 'width: 48rem;',
            Width::FourExtraLarge, '4xl' => 'width: 56rem;',
            Width::FiveExtraLarge, '5xl' => 'width: 64rem;',
            Width::SixExtraLarge, '6xl' => 'width: 72rem;',
            Width::SevenExtraLarge, '7xl' => 'width: 80rem;',
            default => $width,
        } }}; right:0">
		<div class="py-4 px-6">

			{{ $slot }}

			<div class="mt-2 text-end flex gap-6 justify-end">
				<x-filament::link wire:click="submitFiltersForm" color="primary" tag="button" size="sm">
					{{ __('filament-actions::modal.actions.submit.label') }}
				</x-filament::link>

				<x-filament::link wire:click="resetFiltersForm" color="danger" tag="button" size="sm">
					{{ __('filament-plotly::filters.reset.label') }}
				</x-filament::link>
			</div>
		</div>

	</div>
</div>