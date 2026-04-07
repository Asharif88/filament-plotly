import * as Plotly from 'plotly.js-dist'
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
			this.chartData = this.parseData(chartData);

			this.renderChart();

			// Re-render if Livewire updates the data
			this.$wire.on('updateChartData', ({chartData}) => {
				this.chartData = this.parseData(chartData);
				this.updateChart(this.chartData);
			})

			Alpine.effect(() => {
				this.$nextTick(() => {
					this.updateChart(this.chartData);
				})
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
		updateChart(chartData) {
			Plotly.react(
				document.querySelector(this.chartId),
				chartData,
				this.chartLayout,
				this.chartConfig
			);
		},
		tryParse(value,key) {
			if (typeof value !== 'string') return value;

			// Quick check: does it look like JSON?
			if (!(value.startsWith('{') || value.startsWith('['))) return value;

			try {
				return JSON.parse(value);
			} catch {
				console.log('unable to parse JSON for key: "' + key + '" -- already type: ' + (typeof value));
				return value; // Not valid JSON, return as is
			}
		},
		parseData(collection) {
			return collection.map(item => {
				const result = { ...item };
				for (const field of Object.keys(item)) {
					if (Array.isArray(result[field])) {
						result[field] = result[field].map(element => this.tryParse(element, field));
					} else {
						result[field] = this.tryParse(result[field], field);
					}
				}
				return result;
			});
		},
	}
}