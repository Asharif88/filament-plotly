import * as Plotly from 'plotly.js-dist'
export default function plotly({
								   chartData,
								   chartLayout,
								   chartConfig,
								   chartId,
								   streamingLayoutPatch = {},
								   darkThemeLayout = {},
								   lightThemeLayout = {},
								   plotlyEventListeners = {},
								   streamUrl = null,
								   streamParams = {},
								   onStreamMessageScript = null,
								   onStreamDoneScript = null,
								}) {
	return {
		chartData,
		chartLayout,
		chartConfig,
		chartId,
		streamingLayoutPatch,
		darkThemeLayout,
		lightThemeLayout,
		plotlyEventListeners,
		streamUrl,
		streamParams,
		onStreamMessageScript,
		onStreamDoneScript,
		_evtSource: null,
		_streamTid: null,
		init() {
			this.chartData = this.parseData(chartData);

			this.renderChart();

			// Re-render if Livewire updates the data
			this.$wire.on('updateChartData', ({chartData}) => {
				this.chartData = this.parseData(chartData);
			})

			// HasStreamingSupport: auto-start SSE stream once chart is ready
			if (this.streamUrl) {
				this.setupStream();
			}

			Alpine.effect(() => {
				this.$nextTick(() => {
					this.updateChart(this.chartData);
				})
			})
			// Resize chart on window resize
			window.addEventListener('resize', () => Plotly.Plots.resize(
				document.querySelector(this.chartId)
			));

			// HasChartTheme: watch the <html> dark class and relayout accordingly
			if (Object.keys(this.darkThemeLayout).length > 0 || Object.keys(this.lightThemeLayout).length > 0) {
				const _themeEl = document.querySelector(this.chartId);
				const applyTheme = (isDark) => {
					const layout = isDark ? this.darkThemeLayout : this.lightThemeLayout;
					if (Object.keys(layout).length > 0) {
						Plotly.relayout(_themeEl, layout);
					}
				};
				const _observer = new MutationObserver(() => {
					applyTheme(document.documentElement.classList.contains('dark'));
				});
				_observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
				applyTheme(document.documentElement.classList.contains('dark'));
			}

			// Plotly event → Livewire bridge
			if (Object.keys(this.plotlyEventListeners).length > 0) {
				const _el = document.querySelector(this.chartId);
				for (const [plotlyEvent, wireMethod] of Object.entries(this.plotlyEventListeners)) {
					_el.on(plotlyEvent, (data) => {
						const payload = this.serializePlotlyEvent(plotlyEvent, data);

						// 1. Call the mapped method on this widget
						this.$wire[wireMethod](payload);
						// 2. Broadcast so sibling components can react with #[On('plotly:plotly_click')]
						this.$dispatch('plotly:' + plotlyEvent, payload);
					});
				}
			}
		},
		setupStream() {
			const _streamEl = document.querySelector(this.chartId);
			let _streamTries = 0;

			this._streamTid = setInterval(() => {
				if ((_streamEl && _streamEl._fullLayout) || ++_streamTries > 100) {
					clearInterval(this._streamTid);
					if (_streamEl && _streamEl._fullLayout) {
						this.startStream(this.streamUrl, this.streamParams);
					}
				}
			}, 20);
		},
		destroy() {
			// This runs automatically when the component is destroyed
			if (this._evtSource) this._evtSource.close();
			if (this._streamTid) clearInterval(this._streamTid);
		},
		renderChart() {
			if(Object.keys(this.chartLayout).length === 0 && Object.keys(this.chartConfig).length === 0){
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
		/**
		 * Reset the chart for a new stream, merging streamingLayoutPatch into
		 * the current live layout so relayout-applied properties (e.g. theme
		 * template) are not lost when the chart is re-rendered from scratch.
		 * Uses the original PHP trace templates (this.chartData) so trace styles
		 * (mode, line, marker) are preserved after each stream reset.
		 */
		resetStream() {
			const el = document.querySelector(this.chartId);
			const layout = Object.assign({}, el.layout || {}, this.streamingLayoutPatch);
			Plotly.react(el, this.chartData, layout);
		},
		/**
		 * Open an EventSource to `url` with `params` as query-string.
		 * Handles the init / data / done message protocol, progress overlay,
		 * and invokes getOnStreamMessageScript / getOnStreamDoneScript overrides.
		 *
		 * Message protocol (JSON):
		 *   { init: true, total: N }        — stream is starting; N total messages expected
		 *   { done: true }                   — stream finished
		 *   { ... }                          — data message; forwarded to onStreamMessageScript
		 *
		 * Default data handler (when onStreamMessageScript is null):
		 *   Plotly.extendTraces(el, { x: [[d.x]], y: [[d.y]] }, [0])
		 */
		startStream(url, params) {
			if (this._evtSource) { this._evtSource.close(); this._evtSource = null; }

			const el = document.querySelector(this.chartId);
			if (!el || !window.Plotly) return;

			this.resetStream();
			this._showStreamLoader(el);

			let total = 0, loaded = 0;
			const onMsg  = this.onStreamMessageScript ? new Function('d', 'el', this.onStreamMessageScript)  : null;
			const onDone = this.onStreamDoneScript    ? new Function('d','el', this.onStreamDoneScript)           : null;

			this._evtSource = new EventSource(url + '?' + new URLSearchParams(params));

			// Capture a reference so stale handlers from a superseded stream can
			// detect they belong to a closed source and bail out early.
			const source = this._evtSource;

			source.onmessage = (event) => {
				if (this._evtSource !== source) return; // stale — a newer stream replaced us

				const d = JSON.parse(event.data);

				if (d.init) {
					total = d.total ?? 0;
					this._updateStreamProgress(el, 0, total);
					return;
				}

				if (d.done) {
					this._evtSource.close(); this._evtSource = null;
					if (onDone) onDone(d,el);
					this._hideStreamLoader(el);
					return;
				}

				loaded++;
				this._updateStreamProgress(el, loaded, total);

				if (onMsg) {
					onMsg(d, el);
				} else {
					const PROTOCOL_KEYS = new Set(['init', 'done', 'total']);
					const update = {};
					for (const key of Object.keys(d)) {
						if (!PROTOCOL_KEYS.has(key)) update[key] = [[d[key]]];
					}
					if (Object.keys(update).length) Plotly.extendTraces(el, update, [0]);
				}
			};

			source.onerror = () => {
				if (this._evtSource !== source) return; // stale
				this._evtSource.close(); this._evtSource = null;
				this._hideStreamLoader(el);
			};
		},
		_showStreamLoader(el) {
			if (!el || el.querySelector('[data-stream-loader]')) { return; }

			const ov = document.createElement('div');
			ov.setAttribute('data-stream-loader', '');
			ov.style.cssText = 'position:absolute;inset:0;z-index:10;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.75rem';
			ov.innerHTML = `
				<span style="font-size:.75rem;font-weight:500;text-transform:uppercase;letter-spacing:.05em;opacity:.6">Loading data</span>
				<div style="width:11rem;height:3px;border-radius:9999px;overflow:hidden;background:rgba(128,128,128,.25)">
					<div data-progress-bar style="height:100%;border-radius:9999px;background:currentColor;transition:width 150ms ease;width:0%"></div>
				</div>
				<span data-progress-text style="font-size:.75rem;opacity:.6"></span>
			`;
			el.appendChild(ov);
		},
		_updateStreamProgress(el, done, total) {
			const ov = el?.querySelector('[data-stream-loader]');
			if (!ov) return;
			const pct = total > 0 ? (done / total) * 100 : 0;
			ov.querySelector('[data-progress-bar]').style.width = pct + '%';
			ov.querySelector('[data-progress-text]').textContent = `${done} / ${total}`;
		},
		_hideStreamLoader(el) {
			el?.querySelector('[data-stream-loader]')?.remove();
		},
		/**
		 * Strip non-serialisable fields (DOM nodes, full trace objects, axis
		 * definitions) from a Plotly event before forwarding to Livewire.
		 *
		 * serializePoint walks all own keys of the point object and drops only the
		 * known non-serialisable ones, so every chart type (scatter, bar, pie,
		 * polar, geo, sankey, sunburst, …) forwards its native fields without
		 * needing an explicit allowlist. traceName is added as a convenience by
		 * pulling it from the otherwise-skipped `data` trace reference.
		 *
		 * Keys always skipped:
		 *   data / fullData  — full trace object (large, may contain circular refs)
		 *   xaxis / yaxis / zaxis — axis layout objects (DOM-linked)
		 *   node / element / _input — DOM nodes and internal Plotly fields
		 *
		 * Store your record's primary key in the trace's `customdata` field when
		 * building chart data and retrieve it here as points[0].customdata.
		 */
		serializePlotlyEvent(event, data) {
			const SKIP = new Set(['data', 'fullData', 'xaxis', 'yaxis', 'zaxis', 'node', 'element', '_input']);
			const serializePoint = (pt) => {
				const result = { traceName: pt.data?.name ?? null };
				for (const key of Object.keys(pt)) {
					if (SKIP.has(key)) continue;
					const val = pt[key];
					if (typeof val === 'function') continue;
					if (typeof val === 'object' && val !== null && val instanceof Node) continue;
					result[key] = val ?? null;
				}
				return result;
			};

			// Point-based events
			if (['plotly_click', 'plotly_hover', 'plotly_unhover', 'plotly_doubleclick'].includes(event)) {
				return { points: (data?.points ?? []).map(serializePoint) };
			}

			// Selection events add range / lasso info
			if (['plotly_selected', 'plotly_deselect'].includes(event)) {
				return {
					points:      (data?.points ?? []).map(serializePoint),
					range:       data?.range       ?? null,
					lassoPoints: data?.lassoPoints ?? null,
				};
			}

			// Legend events carry the trace index and whether it is now visible
			if (['plotly_legendclick', 'plotly_legenddoubleclick'].includes(event)) {
				return {
					curveNumber: data?.curveNumber ?? null,
					traceName:   data?.data?.name  ?? null,
				};
			}

			// Layout / style change events are already plain objects — safe as-is
			if (['plotly_relayout', 'plotly_restyle', 'plotly_autosize'].includes(event)) {
				return data ?? {};
			}

			// Unknown event: return nothing (avoid serialization errors)
			return {};
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
