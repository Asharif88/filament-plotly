@props(['beforeContent'])
<div class="filament-plotly-before-content" style="margin-block:calc(var(--spacing))">
	@if ($beforeContent)
		<div class="sm:flex justify-between gap-4 py-2 relative">
			{{ $beforeContent }}
		</div>
	@endif
</div>