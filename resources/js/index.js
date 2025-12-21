import * as Plotly from 'plotly.js-dist'
import merge from 'lodash.merge'
export default function plotly({
								   chartData,
								   chartLayout,
								   chartConfig,
								   chartId,
								}) {
	return {
		chartData,
		chartLayout,
		chartConfig,
		chartId,
		init() {
			this.renderChart();

			// Re-render if Livewire updates the data
			this.$wire.on('updateOptions', ({options}) => {
				this.chartData = merge(this.chartData, options)
				this.updateChart(this.chartData);
			})
			// Resize chart on window resize
			window.addEventListener('resize', () => Plotly.Plots.resize(
				document.querySelector(this.chartId)
			));
		},
		renderChart() {
			if(this.chartLayout.length === 0 && this.chartConfig.length === 0){
				Plotly.react(
					document.querySelector(this.chartId),
					this.chartData
				);
			}
			else
			{
				Plotly.react(
					document.querySelector(this.chartId),
					this.chartData,
					this.chartLayout,
					this.chartConfig
				);
			}
		},
		updateChart(options) {
			Plotly.update(
				document.querySelector(this.chartId),
				options,
				this.chartLayout,
				this.chartConfig
			);
		}
	}
}