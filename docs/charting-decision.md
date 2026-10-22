# Charting Library Decision

**Decision date:** 2026-10-22  
**Selected library:** Apache ECharts  
**Implementation:** Week 5 dashboard response-time history is implemented with ECharts and Vue ECharts using the tree-shakable core API.

## Evaluation criteria

PulseMonitor needs charts for:

- uptime and response-time history;
- incident and recovery timelines;
- status distributions and summary cards;
- future latency heatmaps and SLA/SLO views;
- responsive dashboards with hover details, filtering, and time-range zooming.

## Comparison

| Criteria | Apache ECharts | Chart.js |
| --- | --- | --- |
| Core charts | Line, bar, scatter, heatmap, gauge, timeline, and many other chart types | Strong line, bar, pie, doughnut, radar, scatter, and mixed charts |
| Dashboard interaction | Built-in tooltip, legend, data zoom, brush, and visual mapping components | Strong tooltip and interaction model; advanced zoom/filtering typically comes through plugins or custom code |
| Rendering | Canvas or SVG; renderer can be chosen per chart | Canvas-first |
| Accessibility | Optional ARIA descriptions and decal patterns; must be enabled and tested | Canvas needs an accessible name or fallback content supplied by the application |
| Vue integration | `vue-echarts` provides a Vue component wrapper | `vue-chartjs` or direct Chart.js integration are available |
| Complexity | Larger API surface and more configuration discipline | Smaller learning surface for standard charts |
| Fit for PulseMonitor | Better fit for time-series exploration, heatmaps, and future operational dashboards | Good fit for the initial line/bar charts, but likely needs more plugins as analytics grow |

## Selection

We will use Apache ECharts with the tree-shakable core API and the SVG renderer by default for dashboard charts. SVG is a good default for the moderate data volumes expected in the browser and makes chart inspection easier; Canvas remains available for larger datasets.

Charts must provide a textual summary or table alongside the visual. ECharts ARIA support will be enabled where appropriate, and color will not be the only status signal.

This is an architectural choice for the Week 5 analytics work. The package was added when the first real chart was implemented, after historical monitoring data became available.

## References

- [Apache ECharts features](https://echarts.apache.org/handbook/en/concepts/)
- [Apache ECharts Canvas vs. SVG guidance](https://echarts.apache.org/handbook/en/best-practices/canvas-vs-svg/)
- [Apache ECharts accessibility guidance](https://echarts.apache.org/handbook/en/best-practices/aria/)
- [Vue ECharts](https://vue-echarts.dev/)
- [Chart.js documentation](https://www.chartjs.org/docs/latest/)
- [Chart.js accessibility guidance](https://www.chartjs.org/docs/latest/general/accessibility.html)
